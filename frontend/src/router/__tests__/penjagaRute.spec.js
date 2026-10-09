import { describe, expect, it } from 'vitest'
import { penjagaRute } from '../index'

const halamanTerlindungi = { fullPath: '/peminjaman/tambah', meta: { perluLogin: true } }
const halamanLogin = { fullPath: '/login', meta: { khususTamu: true } }
const beranda = { fullPath: '/', meta: {} }

describe('penjagaRute', () => {
  it('mengarahkan tamu ke login sambil mengingat tujuan awal', () => {
    expect(penjagaRute(halamanTerlindungi, false)).toEqual({
      name: 'login',
      query: { redirect: '/peminjaman/tambah' },
    })
  })

  it('mengizinkan user yang sudah login membuka halaman terlindungi', () => {
    expect(penjagaRute(halamanTerlindungi, true)).toBe(true)
  })

  it('mengalihkan user yang sudah login dari halaman login', () => {
    expect(penjagaRute(halamanLogin, true)).toEqual({ name: 'peminjaman' })
  })

  it('halaman publik bisa dibuka siapa saja', () => {
    expect(penjagaRute(beranda, false)).toBe(true)
    expect(penjagaRute(beranda, true)).toBe(true)
  })
})
