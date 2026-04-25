<template>
  <header class="app-header">
    <!-- Page title (slot so each page sets its own) -->
    <div class="header-left">
      <h1 class="page-title">{{ title }}</h1>
      <span v-if="subtitle" class="page-subtitle">{{ subtitle }}</span>
    </div>

    <!-- Right side actions -->
    <div class="header-right">
      <!-- Role badge -->
      <span class="role-badge" :class="authStore.isAdmin ? 'admin' : 'agent'">
        {{ authStore.isAdmin ? 'Admin' : 'Field Agent' }}
      </span>

      <!-- User menu -->
      <div class="user-menu" ref="menuRef">
        <button class="user-btn" @click="menuOpen = !menuOpen">
          <div class="user-avatar">{{ userInitial }}</div>
          <span class="user-name">{{ authStore.user?.name }}</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
        </button>

        <!-- Dropdown -->
        <Transition name="dropdown">
          <div v-if="menuOpen" class="dropdown">
            <div class="dropdown-header">
              <span class="dropdown-name">{{ authStore.user?.name }}</span>
              <span class="dropdown-email">{{ authStore.user?.email }}</span>
            </div>
            <hr class="dropdown-divider" />
            <button class="dropdown-item logout" @click="handleLogout">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              Sign out
            </button>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

defineProps<{
  title: string
  subtitle?: string
}>()

const authStore = useAuthStore()
const router    = useRouter()
const menuOpen  = ref(false)
const menuRef   = ref<HTMLElement | null>(null)

const userInitial = computed(() =>
  authStore.user?.name?.charAt(0).toUpperCase() ?? '?'
)

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}

// Close dropdown when clicking outside
function handleClickOutside(e: MouseEvent) {
  if (menuRef.value && !menuRef.value.contains(e.target as Node)) {
    menuOpen.value = false
  }
}

onMounted(() => document.addEventListener('mousedown', handleClickOutside))
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside))
</script>

<style scoped>
.app-header {
  height: 64px;
  background: white;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  gap: 1rem;
  flex-shrink: 0;
}

/* Left */
.header-left {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.page-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #14532d;
  margin: 0;
  letter-spacing: -0.02em;
}

.page-subtitle {
  font-size: 0.78rem;
  color: #6b7280;
  margin-top: 1px;
}

/* Right */
.header-right {
  display: flex;
  align-items: center;
  gap: 0.875rem;
}

.role-badge {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.25rem 0.625rem;
  border-radius: 99px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.role-badge.admin {
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.role-badge.agent {
  background: #fef9c3;
  color: #854d0e;
  border: 1px solid #fef08a;
}

/* User button */
.user-menu {
  position: relative;
}

.user-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.375rem 0.625rem;
  background: none;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  cursor: pointer;
  color: #374151;
  transition: border-color 0.15s, background 0.15s;
}

.user-btn:hover {
  background: #f9fafb;
  border-color: #d1fae5;
}

.user-avatar {
  width: 28px;
  height: 28px;
  background: #16a34a;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}

.user-name {
  font-size: 0.85rem;
  font-weight: 600;
  color: #374151;
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Dropdown */
.dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 220px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
  overflow: hidden;
  z-index: 100;
}

.dropdown-header {
  padding: 0.875rem 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
}

.dropdown-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
}

.dropdown-email {
  font-size: 0.78rem;
  color: #6b7280;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.dropdown-divider {
  margin: 0;
  border: none;
  border-top: 1px solid #f3f4f6;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  text-align: left;
  transition: background 0.15s;
}

.dropdown-item.logout {
  color: #dc2626;
}

.dropdown-item.logout:hover {
  background: #fef2f2;
}

/* Dropdown animation */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>