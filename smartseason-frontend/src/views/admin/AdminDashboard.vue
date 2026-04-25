<template>
  <div class="dashboard">

    <!-- Stat cards -->
    <section class="dashboard__stats">
      <StatCard label="Total Fields" :value="stats.total"     accent="green"  :loading="loading" sub="All registered fields">
        <template #icon>🌾</template>
      </StatCard>
      <StatCard label="Active"       :value="stats.active"    accent="green"  :loading="loading" sub="Currently growing">
        <template #icon>✅</template>
      </StatCard>
      <StatCard label="At Risk"      :value="stats.atRisk"    accent="yellow" :loading="loading" sub="No update in 14+ days">
        <template #icon>⚠️</template>
      </StatCard>
      <StatCard label="Completed"    :value="stats.completed" accent="blue"   :loading="loading" sub="Harvested fields">
        <template #icon>📦</template>
      </StatCard>
    </section>

    <!-- Body -->
    <div class="dashboard__body">

      <!-- Recent fields -->
      <section class="dashboard__panel">
        <div class="dashboard__panel-header">
          <h2 class="dashboard__panel-title">Recent Fields</h2>
          <RouterLink :to="{ name: 'admin-fields' }" class="dashboard__see-all">See all →</RouterLink>
        </div>

        <LoadingSpinner v-if="loading" size="lg" show-label label="Loading fields…" class="dashboard__spinner" />

        <EmptyState
          v-else-if="!recentFields.length"
          icon="🌾"
          title="No fields yet"
          description="Start by adding your first field."
        >
          <template #action>
            <RouterLink :to="{ name: 'admin-fields-create' }" class="btn-add">+ Add Field</RouterLink>
          </template>
        </EmptyState>

        <div v-else class="dashboard__cards">
          <FieldCard
            v-for="field in recentFields"
            :key="field.id"
            :field="field"
            show-edit
            show-delete
            @view="goToField(field)"
            @edit="goToEdit(field)"
            @delete="confirmDelete(field)"
          />
        </div>
      </section>

      <!-- Aside -->
      <aside class="dashboard__aside">

        <!-- Quick actions -->
        <div class="dashboard__panel">
          <div class="dashboard__panel-header">
            <h2 class="dashboard__panel-title">Quick Actions</h2>
          </div>
          <div class="dashboard__actions">
            <RouterLink :to="{ name: 'admin-fields-create' }" class="dashboard__action-card">
              <span class="dashboard__action-icon">➕</span>
              <div>
                <p class="dashboard__action-label">Add New Field</p>
                <p class="dashboard__action-sub">Register a farm field</p>
              </div>
            </RouterLink>
            <RouterLink :to="{ name: 'admin-fields' }" class="dashboard__action-card">
              <span class="dashboard__action-icon">📋</span>
              <div>
                <p class="dashboard__action-label">Manage Fields</p>
                <p class="dashboard__action-sub">View all fields</p>
              </div>
            </RouterLink>
          </div>
        </div>

        <!-- Stage breakdown -->
        <div class="dashboard__panel dashboard__panel--stages">
          <div class="dashboard__panel-header">
            <h2 class="dashboard__panel-title">By Stage</h2>
          </div>
          <div v-if="loading" class="dashboard__stage-loading">
            <div v-for="i in 4" :key="i" class="dashboard__stage-skeleton" />
          </div>
          <ul v-else class="dashboard__stage-list">
            <li v-for="item in stageBreakdown" :key="item.stage" class="dashboard__stage-item">
              <div class="dashboard__stage-info">
                <StageBadge :stage="item.stage" />
                <span class="dashboard__stage-count">{{ item.count }}</span>
              </div>
              <div class="dashboard__stage-bar">
                <div class="dashboard__stage-fill" :style="{ width: `${item.pct}%` }" />
              </div>
            </li>
            <li v-if="!stageBreakdown.length" class="dashboard__stage-empty">No stage data yet</li>
          </ul>
        </div>

      </aside>
    </div>

    <!-- Delete modal -->
    <Teleport to="body">
      <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
        <div class="modal">
          <h3 class="modal__title">Delete Field</h3>
          <p class="modal__body">
            Are you sure you want to delete <strong>{{ deleteTarget.name }}</strong>?
            This cannot be undone.
          </p>
          <div class="modal__actions">
            <button class="btn-cancel" @click="deleteTarget = null">Cancel</button>
            <button class="btn-danger" :disabled="deleting" @click="doDelete">
              {{ deleting ? 'Deleting…' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useFieldsStore } from '@/stores/fields'
import type { Field } from '@/types'
import StatCard       from '@/components/ui/StatCard.vue'
import FieldCard      from '@/components/fields/FieldCard.vue'
import StageBadge     from '@/components/fields/StageBadge.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import EmptyState     from '@/components/ui/EmptyState.vue'

const router      = useRouter()
const fieldsStore = useFieldsStore()

const loading      = ref(false)
const deleting     = ref(false)
const deleteTarget = ref<Field | null>(null)

// ── Computed ────────────────────────────────────────────────────────────
const recentFields = computed(() => fieldsStore.fields.slice(0, 6))

const stats = computed(() => {
  const fields = fieldsStore.fields
  return {
    total:     fields.length,
    active:    fields.filter(f => f.status === 'active').length,
    atRisk:    fields.filter(f => f.status === 'at_risk').length,
    completed: fields.filter(f => f.status === 'completed').length,
  }
})

const stageBreakdown = computed(() => {
  const fields = fieldsStore.fields
  const map: Record<string, number> = {}
  for (const f of fields) {
    if (f.stage) map[f.stage] = (map[f.stage] ?? 0) + 1
  }
  const total = fields.length || 1
  return Object.entries(map)
    .map(([stage, count]) => ({ stage, count, pct: Math.round((count / total) * 100) }))
    .sort((a, b) => b.count - a.count)
})

// ── Lifecycle ───────────────────────────────────────────────────────────
onMounted(async () => {
  loading.value = true
  try { await fieldsStore.fetchFields() }
  finally { loading.value = false }
})

// ── Actions ─────────────────────────────────────────────────────────────
function goToField(field: Field) {
  router.push({ name: 'admin-field-detail', params: { id: field.id } })
}
function goToEdit(field: Field) {
  router.push({ name: 'admin-field-edit', params: { id: field.id } })
}
function confirmDelete(field: Field) {
  deleteTarget.value = field
}
async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await fieldsStore.deleteField(deleteTarget.value.id)
    deleteTarget.value = null
  } finally {
    deleting.value = false
  }
}
</script>

<style scoped>
.dashboard { display: flex; flex-direction: column; gap: 24px; padding: 24px; max-width: 1400px; }

.dashboard__stats {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;
}

.dashboard__body {
  display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start;
}
@media (max-width: 900px) { .dashboard__body { grid-template-columns: 1fr; } }

.dashboard__panel {
  background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;
}
.dashboard__panel--stages { margin-top: 16px; }

.dashboard__panel-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
}
.dashboard__panel-title { font-size: 0.9rem; font-weight: 700; color: #111827; margin: 0; }
.dashboard__see-all { font-size: 0.78rem; font-weight: 600; color: #16a34a; text-decoration: none; }
.dashboard__see-all:hover { text-decoration: underline; }

.dashboard__spinner { padding: 48px; width: 100%; display: flex; justify-content: center; }

.dashboard__cards {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; padding: 16px;
}

.dashboard__aside { display: flex; flex-direction: column; }

.dashboard__actions { display: flex; flex-direction: column; padding: 12px; gap: 8px; }

.dashboard__action-card {
  display: flex; align-items: center; gap: 12px; padding: 12px 14px;
  border: 1px solid #e5e7eb; border-radius: 10px; text-decoration: none;
  transition: background 0.15s, border-color 0.15s;
}
.dashboard__action-card:hover { background: #f0fdf4; border-color: #bbf7d0; }
.dashboard__action-icon { font-size: 1.4rem; flex-shrink: 0; }
.dashboard__action-label { font-size: 0.82rem; font-weight: 700; color: #111827; margin: 0; }
.dashboard__action-sub   { font-size: 0.72rem; color: #9ca3af; margin: 0; }

.dashboard__stage-loading { padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; }
.dashboard__stage-skeleton {
  height: 32px; border-radius: 6px;
  background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
  background-size: 200% 100%; animation: shimmer 1.4s infinite;
}

.dashboard__stage-list {
  list-style: none; margin: 0; padding: 12px 16px; display: flex; flex-direction: column; gap: 10px;
}
.dashboard__stage-item  { display: flex; flex-direction: column; gap: 4px; }
.dashboard__stage-info  { display: flex; align-items: center; justify-content: space-between; }
.dashboard__stage-count { font-size: 0.75rem; font-weight: 700; color: #374151; }
.dashboard__stage-bar   { height: 4px; background: #f3f4f6; border-radius: 9999px; overflow: hidden; }
.dashboard__stage-fill  {
  height: 100%; background: linear-gradient(90deg, #16a34a, #4ade80);
  border-radius: 9999px; transition: width 0.6s ease;
}
.dashboard__stage-empty { font-size: 0.78rem; color: #9ca3af; padding: 8px 0; text-align: center; }

/* Modal */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.45);
  display: flex; align-items: center; justify-content: center; z-index: 200; padding: 16px;
}
.modal {
  background: #fff; border-radius: 14px; padding: 28px; max-width: 400px; width: 100%;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.modal__title { font-size: 1rem; font-weight: 700; color: #111827; margin: 0 0 10px; }
.modal__body  { font-size: 0.875rem; color: #6b7280; margin: 0 0 24px; line-height: 1.5; }
.modal__actions { display: flex; gap: 10px; justify-content: flex-end; }

.btn-add {
  display: inline-block; padding: 8px 18px; background: #16a34a; color: white;
  border-radius: 7px; font-size: 0.875rem; font-weight: 600; text-decoration: none;
}
.btn-cancel {
  padding: 9px 20px; border: 1.5px solid #d1d5db; border-radius: 7px;
  background: white; font-size: 0.875rem; cursor: pointer;
}
.btn-danger {
  padding: 9px 20px; background: #dc2626; color: white; border: none;
  border-radius: 7px; font-size: 0.875rem; font-weight: 600; cursor: pointer;
}
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
</style>