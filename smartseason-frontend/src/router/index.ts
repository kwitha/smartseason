import { createRouter, createWebHistory } from 'vue-router'
import { watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/components/layout/AppLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // ── Public ──────────────────────────────────────────────────────
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { requiresGuest: true },
    },

    // ── Admin routes ─────────────────────────────────────────────────
    {
      path: '/admin',
      component: AppLayout,
      meta: { requiresAuth: true, role: 'admin' },
      children: [
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: () => import('@/views/admin/AdminDashboard.vue'),
          meta: { title: 'Dashboard', subtitle: 'Overview of all fields' },
        },
        {
          path: 'fields',
          name: 'admin-fields',
          component: () => import('@/views/admin/FieldsList.vue'),
          meta: { title: 'All Fields', subtitle: 'Manage and monitor fields' },
        },
        {
          path: 'fields/create',
          name: 'admin-fields-create',
          component: () => import('@/views/admin/CreateField.vue'),
          meta: { title: 'Add Field', subtitle: 'Register a new field' },
        },
        {
          path: 'fields/:id',
          name: 'admin-field-detail',
          component: () => import('@/views/admin/FieldDetail.vue'),
          meta: { title: 'Field Detail', subtitle: 'View field information' },
        },
        {
          path: 'fields/:id/edit',
          name: 'admin-field-edit',
          component: () => import('@/views/admin/EditField.vue'),
          meta: { title: 'Edit Field', subtitle: 'Update field details' },
        },
        {
          path: 'fields/:id/assign',
          name: 'admin-field-assign',
          component: () => import('@/views/admin/AssignAgent.vue'),
          meta: { title: 'Assign Agent', subtitle: 'Manage field assignments' },
        },
      ],
    },

    // ── Agent routes ─────────────────────────────────────────────────
    {
      path: '/agent',
      component: AppLayout,
      meta: { requiresAuth: true, role: 'agent' },
      children: [
        {
          path: 'dashboard',
          name: 'agent-dashboard',
          component: () => import('@/views/agent/AgentDashboard.vue'),
          meta: { title: 'Dashboard', subtitle: 'Your assigned fields at a glance' },
        },
        {
          path: 'fields',
          name: 'agent-fields',
          component: () => import('@/views/agent/MyFields.vue'),
          meta: { title: 'My Fields', subtitle: 'Fields assigned to you' },
        },
        {
          path: 'fields/:id',
          name: 'agent-field-detail',
          component: () => import('@/views/agent/AgentFieldDetail.vue'),
          meta: { title: 'Field Detail', subtitle: 'View field and update history' },
        },
        {
          path: 'fields/:id/update',
          name: 'agent-field-update',
          component: () => import('@/views/agent/PostUpdate.vue'),
          meta: { title: 'Post Update', subtitle: 'Submit a field stage update' },
        },
      ],
    },

    // ── Fallback ─────────────────────────────────────────────────────
    {
      path: '/',
      redirect: () => {
        const auth = useAuthStore()
        if (!auth.isAuthenticated) return '/login'
        return auth.isAdmin ? '/admin/dashboard' : '/agent/dashboard'
      },
    },
    { path: '/:pathMatch(.*)*', redirect: '/login' },
  ],
})

// ── Navigation Guard ──────────────────────────────────────────────────
router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // Wait for fetchMe() to complete before allowing any navigation
  if (!auth.initialized) {
    await new Promise<void>(resolve => {
      const stop = watch(
        () => auth.initialized,
        (val) => {
          if (val) {
            stop()
            resolve()
          }
        }
      )
    })
  }

  if (to.meta.requiresGuest && auth.isAuthenticated) {
    return auth.isAdmin ? '/admin/dashboard' : '/agent/dashboard'
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return '/login'
  }

  if (to.meta.role === 'admin' && !auth.isAdmin) {
    return '/agent/dashboard'
  }

  if (to.meta.role === 'agent' && !auth.isAgent) {
    return '/admin/dashboard'
  }
})

export default router