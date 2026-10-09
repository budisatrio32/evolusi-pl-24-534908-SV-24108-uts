import { beforeEach, describe, expect, it, vi } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'

const replace = vi.fn()
const query = {}

vi.mock('vue-router', () => ({
  useRouter: () => ({ replace }),
  useRoute: () => ({ query }),
}))

vi.mock('../../lib/api', async (importOriginal) => ({
  ...(await importOriginal()),
  login: vi.fn(),
}))

const { login } = await import('../../lib/api')
const { hapusSesi, sesi } = await import('../../lib/auth')
const { default: LoginView } = await import('../LoginView.vue')

async function isiDanKirim(wrapper, email, password) {
  await wrapper.find('input[name="email"]').setValue(email)
  await wrapper.find('input[name="password"]').setValue(password)
  await wrapper.find('form').trigger('submit')
  await flushPromises()
}

describe('LoginView', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    hapusSesi()
    delete query.redirect
  })

  it('menyimpan token lalu pindah ke halaman peminjaman saat login berhasil', async () => {
    login.mockResolvedValue({ access_token: '3|xyz', user: { id: 1, name: 'Admin' } })
    const wrapper = mount(LoginView)

    await isiDanKirim(wrapper, 'admin@kepl.test', 'password')

    expect(login).toHaveBeenCalledWith('admin@kepl.test', 'password')
    expect(sesi.token).toBe('3|xyz')
    expect(replace).toHaveBeenCalledWith('/peminjaman')
  })

  it('kembali ke tujuan awal setelah login', async () => {
    query.redirect = '/peminjaman/tambah'
    login.mockResolvedValue({ access_token: '3|xyz', user: { id: 1 } })
    const wrapper = mount(LoginView)

    await isiDanKirim(wrapper, 'admin@kepl.test', 'password')

    expect(replace).toHaveBeenCalledWith('/peminjaman/tambah')
  })

  it('menolak redirect ke situs luar', async () => {
    query.redirect = '//situs-jahat.test'
    login.mockResolvedValue({ access_token: '3|xyz', user: { id: 1 } })
    const wrapper = mount(LoginView)

    await isiDanKirim(wrapper, 'admin@kepl.test', 'password')

    expect(replace).toHaveBeenCalledWith('/peminjaman')
  })

  it('menampilkan pesan dari server saat password salah (401)', async () => {
    login.mockRejectedValue({
      response: { status: 401, data: { message: 'Email atau password salah.' } },
    })
    const wrapper = mount(LoginView)

    await isiDanKirim(wrapper, 'admin@kepl.test', 'salah')

    expect(wrapper.find('[role="alert"]').text()).toBe('Email atau password salah.')
    expect(sesi.token).toBeNull()
    expect(replace).not.toHaveBeenCalled()
  })

  it('menampilkan error per field saat validasi gagal (422)', async () => {
    login.mockRejectedValue({
      response: { status: 422, data: { errors: { email: ['Email wajib diisi.'] } } },
    })
    const wrapper = mount(LoginView)

    await isiDanKirim(wrapper, '', '')

    expect(wrapper.find('.error-field').text()).toBe('Email wajib diisi.')
  })
})
