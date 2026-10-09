import { beforeEach, describe, expect, it, vi } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'

const push = vi.fn()

vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
}))

vi.mock('../../lib/api', async (importOriginal) => ({
  ...(await importOriginal()),
  ambilPeminjaman: vi.fn(),
  tambahPeminjaman: vi.fn(),
  ubahPeminjaman: vi.fn(),
}))

const api = await import('../../lib/api')
const { default: PeminjamanFormView } = await import('../PeminjamanFormView.vue')

const pasang = (props = {}) =>
  mount(PeminjamanFormView, { props, global: { stubs: { RouterLink: true } } })

describe('PeminjamanFormView', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('mode tambah mengirim isian ke API lalu kembali ke daftar', async () => {
    api.tambahPeminjaman.mockResolvedValue({ id: 9 })
    const wrapper = pasang()

    await wrapper.find('input[name="nama_peminjam"]').setValue('Rina')
    await wrapper.find('input[name="judul_buku"]').setValue('Refactoring')
    await wrapper.find('input[name="tanggal_pinjam"]').setValue('2026-10-09')
    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(api.tambahPeminjaman).toHaveBeenCalledWith({
      nama_peminjam: 'Rina',
      judul_buku: 'Refactoring',
      tanggal_pinjam: '2026-10-09',
      tanggal_kembali: null,
      status: 'dipinjam',
    })
    expect(push).toHaveBeenCalledWith({ name: 'peminjaman', query: { aksi: 'ditambah' } })
  })

  it('menampilkan error validasi 422 di bawah field yang salah', async () => {
    api.tambahPeminjaman.mockRejectedValue({
      response: {
        status: 422,
        data: {
          errors: {
            judul_buku: ['Judul buku wajib diisi.'],
            tanggal_kembali: ['Tanggal kembali tidak boleh sebelum tanggal pinjam.'],
          },
        },
      },
    })
    const wrapper = pasang()

    await wrapper.find('form').trigger('submit')
    await flushPromises()

    const pesan = wrapper.findAll('.error-field').map((el) => el.text())
    expect(pesan).toEqual([
      'Judul buku wajib diisi.',
      'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
    ])
    expect(push).not.toHaveBeenCalled()
  })

  it('mode ubah memuat data lama lalu menyimpannya dengan PUT', async () => {
    api.ambilPeminjaman.mockResolvedValue({
      id: 4,
      nama_peminjam: 'Andi',
      judul_buku: 'Clean Code',
      tanggal_pinjam: '2026-09-01',
      tanggal_kembali: null,
      status: 'dipinjam',
    })
    api.ubahPeminjaman.mockResolvedValue({ id: 4 })
    const wrapper = pasang({ id: '4' })
    await flushPromises()

    expect(api.ambilPeminjaman).toHaveBeenCalledWith('4')
    expect(wrapper.find('input[name="judul_buku"]').element.value).toBe('Clean Code')

    await wrapper.find('select[name="status"]').setValue('dikembalikan')
    await wrapper.find('input[name="tanggal_kembali"]').setValue('2026-09-08')
    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(api.ubahPeminjaman).toHaveBeenCalledWith('4', expect.objectContaining({
      status: 'dikembalikan',
      tanggal_kembali: '2026-09-08',
    }))
    expect(push).toHaveBeenCalledWith({ name: 'peminjaman', query: { aksi: 'diubah' } })
  })
})
