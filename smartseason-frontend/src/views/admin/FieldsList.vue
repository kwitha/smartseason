<template>
  <div class="fields-list">
    <div class="page-actions">
      <RouterLink to="/admin/fields/create" class="btn-create">+ Add Field</RouterLink>
    </div>

    <!-- Filters -->
    <div class="filters">
      <input v-model="search" type="text" placeholder="Search by name or crop…" class="search-input" />
      <select v-model="stageFilter" class="filter-select">
        <option value="">All Stages</option>
        <option value="planted">🌱 Planted</option>
        <option value="growing">🌿 Growing</option>
        <option value="ready">🌾 Ready</option>
        <option value="harvested">📦 Harvested</option>
      </select>
      <select v-model="statusFilter" class="filter-select">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="at_risk">At Risk</option>
        <option value="completed">Completed</option>
      </select>
    </div>

    <!-- Loading -->
    <div v-if="fieldsStore.loading" class="loading-grid">
      <div v-for="n in 6" :key="n" class="skeleton-card" />
    </div>

    <!-- Error -->
    <div v-else-if="fieldsStore.error" class="error-state">
      <p>{{ fieldsStore.error }}</p>
      <button @click="fieldsStore.fetchFields()">Retry</button>
    </div>

    <!-- Empty -->
    <EmptyState
      v-else-if="filtered.length === 0"
      icon="🌾"
      title="No fields found"
      :description="hasFilters ? 'Try adjusting your filters.' : 'Add your first field to get started.'"
    >
      <template #action>
        <RouterLink v-if="!hasFilters" to="/admin/fields/create" class="btn-create">
          + Add Field
        </RouterLink>
      </template>
    </EmptyState>

    <!-- Grid -->
    <div v-else class="fields-grid">
      <FieldCard
        v-for="field in filtered"
        :key="field.id"
        :field="field"
        :show-edit="true"
        :show-delete="true"
        @view="router.push(`/admin/fields/${field.id}`)"
        @edit="router.push(`/admin/fields/${field.id}/edit`)"
        @delete="confirmDelete(field)"
      />
    </div>

    <!-- Delete modal -->
    <Teleport to="body">
      <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
        <div class="modal">
          <h3>Delete Field</h3>
          <p>Are you sure you want to delete <strong>{{ deleteTarget.name }}</strong>? This cannot be undone.</p>
          <div class="modal-actions">
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
import FieldCard  from '@/components/fields/FieldCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const router      = useRouter()
const fieldsStore = useFieldsStore()

const search       = ref('')
const stageFilter  = ref('')
const statusFilter = ref('')
const deleteTarget = ref<Field | null>(null)
const deleting     = ref(false)

onMounted(() => fieldsStore.fetchFields())

const hasFilters = computed(() => !!(search.value || stageFilter.value || statusFilter.value))

const filtered = computed(() =>
  fieldsStore.fields.filter(f => {
    const q = search.value.toLowerCase()
    return (
      (!q || f.name.toLowerCase().includes(q) || f.crop_type.toLowerCase().includes(q)) &&
      (!stageFilter.value  || f.stage  === stageFilter.value) &&
      (!statusFilter.value || f.status === statusFilter.value)
    )
  })
)

function confirmDelete(field: Field) { deleteTarget.value = field }

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
.fields-list { display: flex; flex-direction: column; gap: 20px; }
.page-actions { display: flex; justify-content: flex-end; }
.btn-create {
  background: #16a34a; color: white; padding: 10px 20px; border-radius: 8px;
  font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: background 0.15s;
}
.btn-create:hover { background: #15803d; }
.filters { display: flex; gap: 12px; flex-wrap: wrap; }
.search-input {
  flex: 1; min-width: 200px; padding: 9px 14px; border: 1.5px solid #d1d5db;
  border-radius: 8px; font-size: 0.875rem; outline: none;
}
.search-input:focus { border-color: #16a34a; }
.filter-select {
  padding: 9px 14px; border: 1.5px solid #d1d5db; border-radius: 8px;
  font-size: 0.875rem; background: white; outline: none; cursor: pointer;
}
.filter-select:focus { border-color: #16a34a; }
.loading-grid, .fields-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;
}
.skeleton-card {
  height: 180px;
  background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
  background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 12px;
}
@keyframes shimmer { to { background-position: -200% 0; } }
.error-state { text-align: center; padding: 40px; color: #dc2626; }
.error-state button {
  margin-top: 12px; padding: 8px 20px; background: #16a34a;
  color: white; border: none; border-radius: 6px; cursor: pointer;
}
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.5);
  display: flex; align-items: center; justify-content: center; z-index: 999;
}
.modal {
  background: white; border-radius: 12px; padding: 28px 32px; width: 100%; max-width: 420px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.12);
}
.modal h3 { margin: 0 0 12px; font-size: 1.1rem; color: #111; }
.modal p  { color: #4b5563; font-size: 0.9rem; margin: 0 0 24px; line-height: 1.5; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
.btn-cancel {
  padding: 9px 20px; border: 1.5px solid #d1d5db; border-radius: 7px; background: white;
  font-size: 0.875rem; cursor: pointer;
}
.btn-danger {
  padding: 9px 20px; background: #dc2626; color: white; border: none;
  border-radius: 7px; font-size: 0.875rem; font-weight: 600; cursor: pointer;
}
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }
</style>