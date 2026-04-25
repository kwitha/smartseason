<template>
  <div class="dashboard">
    <!-- Welcome banner -->
    <div class="dashboard__welcome">
      <div class="dashboard__welcome-text">
        <h2 class="dashboard__welcome-title">Good {{ greeting }}, {{ firstName }} 👋</h2>
        <p class="dashboard__welcome-sub">Here's a summary of your assigned fields.</p>
      </div>
      <div class="dashboard__welcome-badge">
        <span class="dashboard__welcome-avatar">{{ userInitials }}</span>
      </div>
    </div>

    <!-- Stat cards -->
    <section class="dashboard__stats">
      <StatCard label="My Fields"     :value="stats.total"        accent="green"  :loading="loading" sub="Total assigned to you"><template #icon>🌾</template></StatCard>
      <StatCard label="Active"        :value="stats.active"       accent="green"  :loading="loading" sub="Currently growing"><template #icon>✅</template></StatCard>
      <StatCard label="At Risk"       :value="stats.atRisk"       accent="yellow" :loading="loading" sub="No update in 14+ days"><template #icon>⚠️</template></StatCard>
      <StatCard label="Completed"     :value="stats.completed"    accent="blue"   :loading="loading" sub="Harvested fields"><template #icon>📦</template></StatCard>
    </section>

    <div class="dashboard__body">
      <!-- My Fields panel -->
      <section class="dashboard__panel">
        <div class="dashboard__panel-header">
          <h2 class="dashboard__panel-title">My Fields</h2>
          <RouterLink :to="{ name: 'agent-fields' }" class="dashboard__see-all">View all →</RouterLink>
        </div>

        <LoadingSpinner v-if="loading" size="lg" show-label label="Loading your fields…" class="dashboard__spinner" />

        <EmptyState
          v-else-if="!myFields.length"
          icon="🗺️"
          title="No fields assigned yet"
          description="Your admin hasn't assigned any fields to you yet."
        />

        <div v-else class="dashboard__cards">
          <FieldCard
            v-for="field in myFields"
            :key="field.id"
            :field="field"
            @view="goToField(field)"
          />
        </div>
      </section>

      <!-- Right aside -->
      <aside class="dashboard__aside">
        <div class="dashboard__panel">
          <div class="dashboard__panel-header">
            <h2 class="dashboard__panel-title">Quick Actions</h2>
          </div>
          <div class="dashboard__actions">
            <RouterLink :to="{ name: 'agent-fields' }" class="dashboard__action-card">
              <span class="dashboard__action-icon">📋</span>
              <div>
                <p class="dashboard__action-label">My Fields</p>
                <p class="dashboard__action-sub">Browse all your fields</p>
              </div>
            </RouterLink>
          </div>
        </div>

        <!-- Fields needing update -->
        <div class="dashboard__panel dashboard__panel--alerts">
          <div class="dashboard__panel-header">
            <h2 class="dashboard__panel-title">Needs Update</h2>
            <span class="dashboard__alert-badge">{{ staleFields.length }}</span>
          </div>

          <div v-if="loading" class="dashboard__stale-loading">
            <div v-for="i in 3" :key="i" class="dashboard__stale-skeleton" />
          </div>

          <ul v-else-if="staleFields.length" class="dashboard__stale-list">
            <li
              v-for="field in staleFields"
              :key="field.id"
              class="dashboard__stale-item"
              @click="goToUpdate(field)"
            >
              <div class="dashboard__stale-info">
                <span class="dashboard__stale-name">{{ field.name }}</span>
                <span class="dashboard__stale-loc">{{ field.crop_type }}</span>
              </div>
              <button class="btn-post" @click.stop="goToUpdate(field)">Update</button>
            </li>
          </ul>

          <p v-else class="dashboard__stale-empty">🎉 All fields are up to date!</p>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore }   from '@/stores/auth'
import { useFieldsStore } from '@/stores/fields'
import type { Field } from '@/types'
import StatCard       from '@/components/ui/StatCard.vue'
import FieldCard      from '@/components/fields/FieldCard.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import EmptyState     from '@/components/ui/EmptyState.vue'

const router      = useRouter()
const auth        = useAuthStore()
const fieldsStore = useFieldsStore()
const loading     = ref(false)

// ── User info ──────────────────────────────────────────────────────────
const firstName = computed(() => {
  const name = auth.user?.name ?? 'Agent'
  return name.split(' ')[0]
})

const userInitials = computed(() => {
  const name = auth.user?.name ?? 'A'
  return name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2)
})

const greeting = computed(() => {
  const h = new Date().getHours()
  if (h < 12) return 'morning'
  if (h < 17) return 'afternoon'
  return 'evening'
})

// ── Fields ──────────────────────────────────────────────────────────────
const myFields = computed((): Field[] => fieldsStore.fields ?? [])

// At-risk = no update for 14+ days (matches Laravel model logic)
const staleFields = computed(() => {
  const cutoff = Date.now() - 14 * 86_400_000
  return myFields.value.filter(f =>
    !f.updated_at || new Date(f.updated_at).getTime() < cutoff
  ).slice(0, 5)
})

const stats = computed(() => {
  const fields = myFields.value
  return {
    total:     fields.length,
    active:    fields.filter(f => f.status === 'active').length,
    atRisk:    fields.filter(f => f.status === 'at_risk').length,
    completed: fields.filter(f => f.status === 'completed').length,
  }
})

// ── Lifecycle ───────────────────────────────────────────────────────────
onMounted(async () => {
  loading.value = true
  try { await fieldsStore.fetchFields() }
  finally { loading.value = false }
})

// ── Navigation ──────────────────────────────────────────────────────────
function goToField(field: Field)  { router.push({ name: 'agent-field-detail', params: { id: field.id } }) }
function goToUpdate(field: Field) { router.push({ name: 'agent-field-update', params: { id: field.id } }) }
</script>

<style scoped>
.dashboard { display: flex; flex-direction: column; gap: 24px; padding: 24px; max-width: 1400px; }

.dashboard__welcome {
  background: linear-gradient(135deg, #14532d 0%, #166534 60%, #15803d 100%);
  border-radius: 14px; padding: 24px 28px;
  display: flex; align-items: center; justify-content: space-between; color: #fff; gap: 16px;
}
.dashboard__welcome-title { font-size: 1.15rem; font-weight: 700; margin: 0 0 4px; color: #fff; }
.dashboard__welcome-sub   { font-size: 0.82rem; color: rgba(255,255,255,0.75); margin: 0; }
.dashboard__welcome-avatar {
  width: 52px; height: 52px; border-radius: 50%;
  background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.3);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; font-weight: 700; color: #fff; flex-shrink: 0;
}

.dashboard__stats {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px;
}

.dashboard__body {
  display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start;
}
@media (max-width: 860px) { .dashboard__body { grid-template-columns: 1fr; } }

.dashboard__panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
.dashboard__panel--alerts { margin-top: 16px; }

.dashboard__panel-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
}
.dashboard__panel-title { font-size: 0.9rem; font-weight: 700; color: #111827; margin: 0; }
.dashboard__see-all { font-size: 0.78rem; font-weight: 600; color: #16a34a; text-decoration: none; }
.dashboard__see-all:hover { text-decoration: underline; }

.dashboard__alert-badge {
  min-width: 22px; height: 22px; padding: 0 6px;
  background: #fef3c7; color: #92400e; font-size: 0.7rem; font-weight: 700;
  border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center;
}
.dashboard__spinner { padding: 48px; width: 100%; display: flex; justify-content: center; }
.dashboard__cards {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; padding: 16px;
}
.dashboard__actions { padding: 12px; }
.dashboard__action-card {
  display: flex; align-items: center; gap: 12px; padding: 12px 14px;
  border: 1px solid #e5e7eb; border-radius: 10px; text-decoration: none; transition: background 0.15s, border-color 0.15s;
}
.dashboard__action-card:hover { background: #f0fdf4; border-color: #bbf7d0; }
.dashboard__action-icon { font-size: 1.4rem; }
.dashboard__action-label { font-size: 0.82rem; font-weight: 700; color: #111827; margin: 0; }
.dashboard__action-sub   { font-size: 0.72rem; color: #9ca3af; margin: 0; }

.dashboard__stale-loading { padding: 12px 16px; display: flex; flex-direction: column; gap: 8px; }
.dashboard__stale-skeleton {
  height: 42px; border-radius: 6px;
  background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
  background-size: 200% 100%; animation: shimmer 1.4s infinite;
}
.dashboard__stale-list { list-style: none; margin: 0; padding: 8px 0; }
.dashboard__stale-item {
  display: flex; align-items: center; justify-content: space-between;
  gap: 10px; padding: 10px 16px; cursor: pointer; transition: background 0.1s;
}
.dashboard__stale-item:hover { background: #fefce8; }
.dashboard__stale-info { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.dashboard__stale-name {
  font-size: 0.8rem; font-weight: 600; color: #111827;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.dashboard__stale-loc {
  font-size: 0.7rem; color: #9ca3af;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.dashboard__stale-empty { text-align: center; font-size: 0.8rem; color: #6b7280; padding: 20px; margin: 0; }

.btn-post {
  padding: 5px 12px; background: #16a34a; color: white; border: none;
  border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; white-space: nowrap;
}
.btn-post:hover { background: #15803d; }

@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
</style>