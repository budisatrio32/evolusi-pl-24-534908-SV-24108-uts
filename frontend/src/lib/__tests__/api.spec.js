import { beforeEach, describe, expect, it, vi } from 'vitest'

// Instance Axios diganti tiruan, jadi test tidak membutuhkan Laravel yang berjalan.
vi.mock('../http', () => ({
  http: { get: vi.fn(), post: vi.fn(), put: vi.fn(), delete: vi.fn() },
}))

const { http } = await import('../http')
const api = await import('../api')

describe('fungsi API memanggil endpoint yang benar', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('login mengirim email dan password ke POST /login', async () => {
    http.post.mockResolvedValue({ data: { access_token: '1|abc' } })

    const hasil = await api.login('admin@kepl.test', 'password')

    expect(http.post).toHaveBeenCalledWith('/login', {
      email: 'admin@kepl.test',
      password: 'password',
    })
    expect(hasil.access_token).toBe('1|abc')
  })

  it('daftarPeminjaman memanggil GET /peminjaman dan mengembalikan isi data', async () => {
    http.get.mockResolvedValue({ data: { data: [{ id: 1 }, { id: 2 }] } })

    expect(await api.daftarPeminjaman()).toEqual([{ id: 1 }, { id: 2 }])
    expect(http.get).toHaveBeenCalledWith('/peminjaman')
  })

  it('tambah, ubah, dan hapus memakai metode HTTP yang sesuai', async () => {
    const isian = { judul_buku: 'Clean Code' }
    http.post.mockResolvedValue({ data: { data: { id: 5 } } })
    http.put.mockResolvedValue({ data: { data: { id: 5 } } })
    http.delete.mockResolvedValue({ data: {} })

    await api.tambahPeminjaman(isian)
    await api.ubahPeminjaman(5, isian)
    await api.hapusPeminjaman(5)

    expect(http.post).toHaveBeenCalledWith('/peminjaman', isian)
    expect(http.put).toHaveBeenCalledWith('/peminjaman/5', isian)
    expect(http.delete).toHaveBeenCalledWith('/peminjaman/5')
  })
})

describe('uraikanError', () => {
  it('mengambil error per field dari respons 422', () => {
    const errors = { judul_buku: ['Judul buku wajib diisi.'] }

    expect(api.uraikanError({ response: { status: 422, data: { errors } } })).toEqual({
      pesan: 'Periksa kembali isian formulir.',
      errors,
    })
  })

  it('memakai pesan dari server untuk 401 dan 404', () => {
    const uraian = api.uraikanError({
      response: { status: 401, data: { message: 'Email atau password salah.' } },
    })

    expect(uraian.pesan).toBe('Email atau password salah.')
  })

  it('memberi pesan jelas saat server tidak dapat dihubungi', () => {
    expect(api.uraikanError(new Error('Network Error')).pesan).toMatch(/tidak dapat dihubungi/)
  })
})
