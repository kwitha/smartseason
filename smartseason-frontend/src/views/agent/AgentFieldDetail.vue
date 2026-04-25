<template>
  <div v-if="field" class="field-detail">
    <div class="detail-header">
      <div class="header-meta">
        <StatusBadge :status="field.status" />
        <StageBadge  :stage="field.stage" />
      </div>
      <RouterLink :to="`/agent/fields/${field.id}/update`" class="btn-update">
        + Post Update
      </RouterLink>
    </div>

    <!-- Info -->
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
        <span class="info-label">Total Updates</span>
        <span class="info-value">{{ field.updates?.length ?? 0 }}</span>
      </div>
      <div class="info-card">
        <span class="info-label">Last Updated</span>
        <span class="info-value">{{ formatDate(field.updated_at) }}</span>
      </div>
    </div>

    <UpdateHistoryList :updates="field.updates ?? []" />
  </div>

  <div v-else-if="loading" class="loading-state"><div class="spinner-lg" /></div>
  <div v-else class="not-found">
    <p>Field not found.</p>
    <RouterLink to="/agent/fields">← Back to My Fields</RouterLink>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useFieldsStore } from '@/stores/fields'
import type { Field } from '@/types'
import StatusBadge       from '@/components/fields/StatusBadge.vue'
import StageBadge        from '@/components/fields/StageBadge.vue'
import UpdateHistoryList  from '@/components/fields/UpdateHistoryList.vue'

const route       = useRoute()
const fieldsStore = useFieldsStore()
const field       = ref<Field | null>(null)
const loading     = ref(true)

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
</script>

<style scoped>
.field-detail { display: flex; flex-direction: column; gap: 20px; max-width: 860px; }
.detail-header {
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.header-meta { display: flex; gap: 8px; align-items: center; }
.btn-update {
  padding: 9px 18px; background: #16a34a; color: white; border-radius: 8px;
  font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: background 0.15s;
}
.btn-update:hover { background: #15803d; }
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
@keyframes spin { to { transform: rotate(360deg); } }
</style>