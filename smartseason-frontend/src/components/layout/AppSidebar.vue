<template>
  <aside class="sidebar" :class="{ collapsed: isCollapsed }">
    <!-- Brand -->
    <div class="sidebar-brand">
      <div class="brand-icon">
        <svg width="26" height="26" viewBox="0 0 32 32" fill="none">
          <path d="M16 3C16 3 6 10 6 19C6 24.5 10.5 29 16 29C21.5 29 26 24.5 26 19C26 10 16 3 16 3Z" fill="#4ade80"/>
          <path d="M16 10C16 10 10 14.5 10 19C10 22.2 12.7 25 16 25C19.3 25 22 22.2 22 19C22 14.5 16 10 16 10Z" fill="#16a34a"/>
          <path d="M16 16C16 16 13 18 13 20C13 21.7 14.3 23 16 23C17.7 23 19 21.7 19 20C19 18 16 16 16 16Z" fill="#dcfce7"/>
        </svg>
      </div>
      <Transition name="fade">
        <div v-if="!isCollapsed" class="brand-text">
          <span class="brand-name">SmartSeason</span>
          <span class="brand-sub">Field Monitor</span>
        </div>
      </Transition>
    </div>

    <!-- Toggle -->
    <button class="collapse-btn" @click="isCollapsed = !isCollapsed" :title="isCollapsed ? 'Expand' : 'Collapse'">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <path :d="isCollapsed ? 'M9 18l6-6-6-6' : 'M15 18l-6-6 6-6'" />
      </svg>
    </button>

    <!-- Navigation -->
    <nav class="sidebar-nav">
      <div class="nav-section">
        <span v-if="!isCollapsed" class="nav-label">
          {{ authStore.isAdmin ? 'Admin' : 'Agent' }}
        </span>

        <RouterLink
          v-for="item in navItems"
          :key="item.name"
          :to="item.to"
          class="nav-item"
          :title="isCollapsed ? item.label : ''"
        >
          <span class="nav-icon" v-html="item.icon"></span>
          <Transition name="fade">
            <span v-if="!isCollapsed" class="nav-text">{{ item.label }}</span>
          </Transition>
        </RouterLink>
      </div>
    </nav>

    <!-- User info at bottom -->
    <div class="sidebar-footer">
      <div class="user-avatar">{{ userInitial }}</div>
      <Transition name="fade">
        <div v-if="!isCollapsed" class="user-info">
          <span class="user-name">{{ authStore.user?.name }}</span>
          <span class="user-role">{{ authStore.user?.role }}</span>
        </div>
      </Transition>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore   = useAuthStore()
const isCollapsed = ref(false)

const userInitial = computed(() =>
  authStore.user?.name?.charAt(0).toUpperCase() ?? '?'
)

const adminNav = [
  {
    name: 'dashboard',
    label: 'Dashboard',
    to: '/admin/dashboard',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>`,
  },
  {
    name: 'fields',
    label: 'All Fields',
    to: '/admin/fields',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
  {
    name: 'create',
    label: 'Add Field',
    to: '/admin/fields/create',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>`,
  },
]

const agentNav = [
  {
    name: 'dashboard',
    label: 'Dashboard',
    to: '/agent/dashboard',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>`,
  },
  {
    name: 'fields',
    label: 'My Fields',
    to: '/agent/fields',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
]

const navItems = computed(() =>
  authStore.isAdmin ? adminNav : agentNav
)
</script>

<style scoped>
.sidebar {
  width: 230px;
  min-height: 100vh;
  background: #14532d;
  display: flex;
  flex-direction: column;
  transition: width 0.25s ease;
  position: relative;
  flex-shrink: 0;
}

.sidebar.collapsed {
  width: 64px;
}

/* Brand */
.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1.25rem 1rem;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  min-height: 68px;
}

.brand-icon {
  width: 40px;
  height: 40px;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.brand-text {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.brand-name {
  font-size: 0.95rem;
  font-weight: 700;
  color: #fff;
  white-space: nowrap;
  letter-spacing: -0.01em;
}

.brand-sub {
  font-size: 0.7rem;
  color: #86efac;
  white-space: nowrap;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* Collapse button */
.collapse-btn {
  position: absolute;
  top: 18px;
  right: -12px;
  width: 24px;
  height: 24px;
  background: #16a34a;
  border: 2px solid #14532d;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: white;
  z-index: 10;
  transition: background 0.15s;
}

.collapse-btn:hover {
  background: #15803d;
}

/* Nav */
.sidebar-nav {
  flex: 1;
  padding: 1rem 0.625rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  overflow: hidden;
}

.nav-section {
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
}

.nav-label {
  font-size: 0.68rem;
  font-weight: 700;
  color: #86efac;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 0 0.625rem;
  margin-bottom: 0.375rem;
  white-space: nowrap;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.625rem 0.75rem;
  border-radius: 10px;
  color: #bbf7d0;
  text-decoration: none;
  transition: background 0.15s, color 0.15s;
  white-space: nowrap;
  overflow: hidden;
}

.nav-item:hover {
  background: rgba(255,255,255,0.08);
  color: #fff;
}

.nav-item.router-link-active {
  background: rgba(74, 222, 128, 0.15);
  color: #4ade80;
}

.nav-icon {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  height: 18px;
}

.nav-text {
  font-size: 0.875rem;
  font-weight: 500;
}

/* Footer */
.sidebar-footer {
  padding: 0.875rem 0.875rem;
  border-top: 1px solid rgba(255,255,255,0.08);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  overflow: hidden;
}

.user-avatar {
  width: 34px;
  height: 34px;
  background: #16a34a;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.user-info {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.user-name {
  font-size: 0.82rem;
  font-weight: 600;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 0.7rem;
  color: #86efac;
  text-transform: capitalize;
}

/* Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>