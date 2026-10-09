import { beforeEach, describe, expect, it } from 'vitest'
import { hapusSesi, sesi, simpanSesi, sudahLogin } from '../auth'

describe('sesi login', () => {
  beforeEach(() => {
    hapusSesi()
  })

  it('belum login saat token kosong', () => {
    expect(sudahLogin()).toBe(false)
    expect(sesi.token).toBeNull()
  })

  it('menyimpan token dan user ke memori dan localStorage', () => {
    simpanSesi('1|abc', { id: 1, name: 'Admin' })

    expect(sudahLogin()).toBe(true)
    expect(sesi.user.name).toBe('Admin')
    expect(localStorage.getItem('kepl_token')).toBe('1|abc')
    expect(JSON.parse(localStorage.getItem('kepl_user'))).toEqual({ id: 1, name: 'Admin' })
  })

  it('menghapus token dan user saat logout', () => {
    simpanSesi('1|abc', { id: 1, name: 'Admin' })
    hapusSesi()

    expect(sudahLogin()).toBe(false)
    expect(localStorage.getItem('kepl_token')).toBeNull()
    expect(localStorage.getItem('kepl_user')).toBeNull()
  })
})
