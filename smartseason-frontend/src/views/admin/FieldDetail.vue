<template>
  <div v-if="field" class="field-detail">
    <!-- Header -->
    <div class="detail-header">
      <div class="header-meta">
        <StatusBadge :status="field.status" />
        <StageBadge  :stage="field.stage" />
      </div>
      <div class="header-actions">
        <RouterLink :to="`/admin/fields/${field.id}/edit`"   class="btn-edit">Edit</RouterLink>
        <RouterLink :to="`/admin/fields/${field.id}/assign`" class="btn-assign">Assign Agent</RouterLink>
        <button class="btn-danger-outline" @click="showDeleteModal = true">Delete</button>
      </div>
    </div>

    <!-- Info cards -->
    <div class="info-grid">
      <div class="info-card">
        <span class="info-label">Crop Type</span>
        <span class="info-value">{{ field.crop_type }}</span>
      </div>
      <div class="info-card">
        <span class="info-label">Planting Date</span>
        <span class="info-value">{{ formatDate(field.planting_date) }}</span>
      </div>
      <div class="info-card">
        <span class="info-label">Created By</span>
        <span class="info-value">{{ field.creator?.name ?? 'User #' + field.created_by }}</span>
      </div>
      <div class="info-card">
        <span class="info-label">Assigned Agents</span>
        <span class="info-value">
          {{ field.assignments?.length
            ? field.assignments.map(a => a.agent?.name ?? 'Agent #' + a.agent_id).join(', ')
            : 'None assigned' }}
        </span>
      </div>
      <div class="info-card">
        <span class="info-label">Total Updates</span>
        <span class="info-value">{{ field.updates?.length ?? 0 }}</span>
      </div>
      <div class="info-card">
        <span class="info-label">Last Updated</span>
        <span class="info-value">{{ formatDate(field.updated_at) }}</span>
      </div>
    </div>

    <!-- Update History -->
    <UpdateHistoryList :updates="field.updates ?? []" />

    <!-- Delete modal -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal">
          <h3>Delete Field</h3>
          <p>Are you sure you want to delete <strong>{{ field.name }}</strong>? This cannot be undone.</p>
          <div class="modal-actions">
            <button class="btn-cancel" @click="showDeleteModal = false">Cancel</button>
            <button class="btn-danger" :disabled="deleting" @click="doDelete">
              {{ deleting ? 'Deleting…' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>

  <div v-else-if="loading" class="loading-state"><div class="spinner-lg" /></div>
  <div v-else class="not-found">
    <p>Field not found.</p>
    <RouterLink to="/admin/fields">← Back to Fields</RouterLink>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useFieldsStore } from '@/stores/fields'
import type { Field } from '@/types'
import StatusBadge      from '@/components/fields/StatusBadge.vue'
import StageBadge       from '@/components/fields/StageBadge.vue'
import UpdateHistoryList from '@/components/fields/UpdateHistoryList.vue'

const route       = useRoute()
const router      = useRouter()
const fieldsStore = useFieldsStore()

const field           = ref<Field | null>(null)
const loading         = ref(true)
const showDeleteModal  = ref(false)
const deleting        = ref(false)

onMounted(async () => {
  try {
    await fieldsStore.fetchField(Number(route.params.id))
    field.value = fieldsStore.currentField
  } finally {
    loading.value = false
  }
})

function formatDate(d?: string) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-KE', { year: 'numeric', month: 'short', day: 'numeric' })
}

async function doDelete() {
  if (!field.value) return
  deleting.value = true
  try {
    await fieldsStore.deleteField(field.value.id)
    router.push('/admin/fields')
  } finally {
    deleting.value = false
  }
}
</script>

<style scoped>
.field-detail { display: flex; flex-direction: column; gap: 20px; max-width: 900px; }
.detail-header {
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.header-meta  { display: flex; gap: 8px; align-items: center; }
.header-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.btn-edit, .btn-assign {
  padding: 8px 16px; border-radius: 7px; font-size: 0.875rem; font-weight: 600; text-decoration: none;
}
.btn-edit   { background: #16a34a; color: white; }
.btn-edit:hover { background: #15803d; }
.btn-assign { background: #eff6ff; color: #1d4ed8; border: 1.5px solid #bfdbfe; }
.btn-assign:hover { background: #dbeafe; }
.btn-danger-outline {
  padding: 8px 16px; border: 1.5px solid #dc2626; color: #dc2626; background: white;
  border-radius: 7px; font-size: 0.875rem; font-weight: 500; cursor: pointer;
}
.btn-danger-outline:hover { background: #fef2f2; }
.info-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;
}
.info-card {
  background: white; border-radius: 10px; padding: 16px 18px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 4px;
}
.info-label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; font-weight: 500; }
.info-value { font-size: 0.9rem; color: #111827; font-weight: 500; }
.loading-state { display: flex; justify-content: center; padding: 60px; }
.spinner-lg {
  width: 36px; height: 36px; border: 3px solid #dcfce7;
  border-top-color: #16a34a; border-radius: 50%; animation: spin 0.7s linear infinite;
}
.not-found { text-align: center; padding: 60px; color: #6b7280; }
.not-found a { color: #16a34a; text-decoration: none; font-weight: 500; }
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
  padding: 9px 20px; border: 1.5px solid #d1d5db; border-radius: 7px; background: white; font-size: 0.875rem; cursor: pointer;
}
.btn-danger {
  padding: 9px 20px; background: #dc2626; color: white; border: none;
  border-radius: 7px; font-size: 0.875rem; font-weight: 600; cursor: pointer;
}
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>