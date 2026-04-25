import api from './axios'
import type { LoginCredentials, AuthResponse } from '@/types'

export const authApi = {
  login(credentials: LoginCredentials) {
    return api.post<AuthResponse>('/login', credentials)
  },

  logout() {
    return api.post('/logout')
  },

  me() {
    return api.get<{ user: AuthResponse['user'] }>('/me')
  },
}