<template>
  <div class="login-page">
    <div class="login-card">
      <div class="brand">
        <div class="brand-icon">🌾</div>
        <h1 class="brand-name">FarmTrack</h1>
        <p class="brand-sub">Field Management System</p>
      </div>

      <form class="login-form" @submit.prevent="handleLogin">
        <div v-if="error" class="error-banner">
          {{ error }}
        </div>

        <div class="field-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="you@example.com"
            autocomplete="email"
            required
          />
        </div>

        <div class="field-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          />
        </div>

        <button type="submit" class="login-btn" :disabled="loading">
          <span v-if="loading" class="spinner" />
          <span>{{ loading ? 'Signing in…' : 'Sign In' }}</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await authStore.login(form.value)
    router.push('/admin/dashboard')
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Invalid email or password.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0fdf4;
  font-family: 'Segoe UI', system-ui, sans-serif;
}

.login-card {
  background: white;
  border-radius: 16px;
  padding: 48px 40px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

.brand {
  text-align: center;
  margin-bottom: 36px;
}

.brand-icon { font-size: 2.5rem; margin-bottom: 8px; }
.brand-name { font-size: 1.6rem; font-weight: 700; color: #14532d; margin: 0 0 4px; }
.brand-sub { font-size: 0.85rem; color: #6b7280; margin: 0; }

.login-form { display: flex; flex-direction: column; gap: 20px; }

.error-banner {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 0.875rem;
}

.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-group label { font-size: 0.875rem; font-weight: 500; color: #374151; }
.field-group input {
  padding: 10px 14px;
  border: 1.5px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.15s;
}
.field-group input:focus { border-color: #16a34a; }

.login-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #16a34a;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 12px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
  margin-top: 4px;
}
.login-btn:hover:not(:disabled) { background: #15803d; }
.login-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>