import { beforeEach, describe, expect, it, vi } from 'vitest'
import { hapusSesi, sesi, simpanSesi } from '../auth'
import { aturPenangananSesiHabis, http, sisipkanToken, tanganiErrorRespons } from '../http'

describe('instance Axios', () => {
  it('memakai VITE_API_URL sebagai baseURL dan meminta JSON', () => {
    expect(http.defaults.baseURL).toBe(import.meta.env.VITE_API_URL || '/api')
    expect(http.defaults.headers.Accept).toBe('application/json')
  })
})

describe('request interceptor sisipkanToken', () => {
  beforeEach(() => {
    hapusSesi()
  })

  it('menambahkan header Authorization Bearer saat sudah login', () => {
    simpanSesi('7|token-rahasia', { id: 1 })

    const config = sisipkanToken({ headers: {} })

    expect(config.headers.Authorization).toBe('Bearer 7|token-rahasia')
  })

  it('tidak menambahkan header saat belum login', () => {
    const config = sisipkanToken({ headers: {} })

    expect(config.headers.Authorization).toBeUndefined()
  })
})

describe('response interceptor tanganiErrorRespons', () => {
  beforeEach(() => {
    hapusSesi()
  })

  it('menghapus sesi dan memanggil penanganan saat token ditolak (401)', async () => {
    const saatHabis = vi.fn()
    aturPenangananSesiHabis(saatHabis)
    simpanSesi('7|kedaluwarsa', { id: 1 })

    const error = { response: { status: 401 } }

    await expect(tanganiErrorRespons(error)).rejects.toBe(error)
    expect(sesi.token).toBeNull()
    expect(saatHabis).toHaveBeenCalledOnce()
  })

  it('tidak mengarahkan ulang saat login gagal (401 tanpa sesi)', async () => {
    const saatHabis = vi.fn()
    aturPenangananSesiHabis(saatHabis)

    await expect(tanganiErrorRespons({ response: { status: 401 } })).rejects.toBeTruthy()
    expect(saatHabis).not.toHaveBeenCalled()
  })

  it('membiarkan error lain (422) tanpa menghapus sesi', async () => {
    simpanSesi('7|aktif', { id: 1 })

    await expect(tanganiErrorRespons({ response: { status: 422 } })).rejects.toBeTruthy()
    expect(sesi.token).toBe('7|aktif')
  })
})
