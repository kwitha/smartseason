import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi } from '@/api/auth'
import type { User, LoginCredentials } from '@/types'

export const useAuthStore = defineStore('auth', () => {
  // ── State ──────────────────────────────────────────────────────────
  const token       = ref<string | null>(localStorage.getItem('token'))
  const user        = ref<User | null>(JSON.parse(localStorage.getItem('user') ?? 'null'))
  const loading     = ref(false)
  const error       = ref<string | null>(null)
  const initialized = ref(false)

  // ── Getters ────────────────────────────────────────────────────────
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin         = computed(() => user.value?.role === 'admin')
  const isAgent         = computed(() => user.value?.role === 'agent')

  // ── Actions ────────────────────────────────────────────────────────
  async function login(credentials: LoginCredentials) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await authApi.login(credentials)
      token.value = data.token
      user.value  = data.user
      localStorage.setItem('token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
      return data.user
    } catch (err: any) {
      error.value = err.response?.data?.message ?? 'Login failed. Please try again.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await authApi.logout()
    } catch {
      // Ignore — clear locally regardless
    } finally {
      token.value = null
      user.value  = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }

  async function fetchMe() {
    if (!token.value) {
      initialized.value = true
      return
    }
    try {
      const { data } = await authApi.me()
      user.value = data.user
      localStorage.setItem('user', JSON.stringify(data.user))
    } catch {
      token.value = null
      user.value  = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    } finally {
      initialized.value = true
    }
  }

  function clearError() {
    error.value = null
  }

  return {
    token,
    user,
    loading,
    error,
    initialized,
    isAuthenticated,
    isAdmin,
    isAgent,
    login,
    logout,
    fetchMe,
    clearError,
  }
})