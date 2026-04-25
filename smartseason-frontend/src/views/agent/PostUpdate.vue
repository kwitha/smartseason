<template>
  <div class="post-update">
    <div v-if="pageLoading" class="loading-state"><div class="spinner-lg" /></div>

    <div v-else-if="field" class="content">
      <!-- Field summary -->
      <div class="field-summary">
        <div class="summary-row">
          <span class="summary-label">Field</span>
          <span class="summary-value">{{ field.name }}</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Crop</span>
          <span class="summary-value">{{ field.crop_type }}</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Current Stage</span>
          <StageBadge :stage="field.stage" />
        </div>
        <div class="summary-row">
          <span class="summary-label">Status</span>
          <StatusBadge :status="field.status" />
        </div>
      </div>

      <!-- Form -->
      <div class="update-card">
        <h3>Post Field Update</h3>

        <!-- new_stage is REQUIRED in field_updates table -->
        <div class="form-group">
          <label>New Stage <span class="required">*</span></label>
          <select v-model="form.new_stage" :class="{ error: errors.new_stage }">
            <option value="">Select new stage</option>
            <option value="planted">🌱 Planted</option>
            <option value="growing">🌿 Growing</option>
            <option value="ready">🌾 Ready</option>
            <option value="harvested">📦 Harvested</option>
          </select>
          <span v-if="errors.new_stage" class="field-error">{{ errors.new_stage }}</span>
        </div>

        <div class="form-group">
          <label>Notes <span class="hint">(optional)</span></label>
          <textarea
            v-model="form.notes"
            rows="5"
            placeholder="Describe what is happening in the field — crop health, irrigation, observations…"
          />
          <span class="char-count" :class="{ warn: form.notes.length > 900 }">
            {{ form.notes.length }} / 1000
          </span>
        </div>

        <div v-if="submitError" class="submit-error">{{ submitError }}</div>

        <div class="form-footer">
          <RouterLink :to="`/agent/fields/${field.id}`" class="btn-cancel">Cancel</RouterLink>
          <button class="btn-submit" :disabled="saving" @click="handleSubmit">
            <span v-if="saving" class="spinner" />
            {{ saving ? 'Submitting…' : 'Submit Update' }}
          </button>
        </div>
      </div>

      <!-- Recent updates -->
      <div v-if="recentUpdates.length" class="recent-card">
        <h3>Recent Updates</h3>
        <div v-for="u in recentUpdates" :key="u.id" class="recent-item">
          <div class="recent-meta">
            <StageBadge :stage="u.new_stage" />
            <span class="recent-date">{{ formatDate(u.created_at) }}</span>
          </div>
          <p v-if="u.notes" class="recent-notes">{{ u.notes }}</p>
        </div>
      </div>
    </div>

    <div v-else class="not-found">
      <p>Field not found.</p>
      <RouterLink to="/agent/fields">← Back to My Fields</RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useFieldsStore } from '@/stores/fields'
import { addFieldUpdate } from '@/api/fields'
import type { Field } from '@/types'
import StatusBadge from '@/components/fields/StatusBadge.vue'
import StageBadge  from '@/components/fields/StageBadge.vue'

const route       = useRoute()
const router      = useRouter()
const fieldsStore = useFieldsStore()

const field       = ref<Field | null>(null)
const pageLoading = ref(true)
const saving      = ref(false)
const submitError = ref('')
const errors      = ref<Record<string, string>>({})

const form = ref({ new_stage: '', notes: '' })

const recentUpdates = computed(() => (field.value?.updates ?? []).slice(0, 3))

onMounted(async () => {
  try {
    await fieldsStore.fetchField(Number(route.params.id))
    field.value = fieldsStore.currentField
  } finally {
    pageLoading.value = false
  }
})

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('en-KE', { year: 'numeric', month: 'short', day: 'numeric' })
}

function validate() {
  errors.value = {}
  if (!form.value.new_stage) errors.value.new_stage = 'Please select the new stage.'
  if (form.value.notes.length > 1000) errors.value.notes = 'Notes must be 1000 characters or less.'
  return Object.keys(errors.value).length === 0
}

async function handleSubmit() {
  if (!validate() || !field.value) return
  saving.value      = true
  submitError.value = ''
  try {
    await addFieldUpdate(field.value.id, {
      new_stage: form.value.new_stage,
      notes:     form.value.notes || undefined,
    })
    router.push(`/agent/fields/${field.value.id}`)
  } catch (e: any) {
    const data = e?.response?.data
    if (data?.errors) {
      Object.entries(data.errors).forEach(([k, v]: any) => {
        errors.value[k] = Array.isArray(v) ? v[0] : v
      })
    } else {
      submitError.value = data?.message || 'Failed to submit update.'
    }
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.post-update { max-width: 620px; }
.content { display: flex; flex-direction: column; gap: 20px; }

.field-summary {
  background: white; border-radius: 12px; padding: 20px 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 12px;
}
.summary-row { display: flex; align-items: center; justify-content: space-between; }
.summary-label { font-size: 0.82rem; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 0.04em; }
.summary-value { font-size: 0.9rem; color: #111827; font-weight: 500; }

.update-card {
  background: white; border-radius: 12px; padding: 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 18px;
}
.update-card h3 { margin: 0; font-size: 1rem; font-weight: 700; color: #14532d; }

.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 0.875rem; font-weight: 500; color: #374151; }
.required { color: #dc2626; }
.hint { font-size: 0.78rem; color: #9ca3af; font-weight: 400; }

.form-group select,
.form-group textarea {
  padding: 9px 13px; border: 1.5px solid #d1d5db; border-radius: 8px;
  font-size: 0.9rem; font-family: inherit; outline: none; background: white; transition: border-color 0.15s;
}
.form-group select:focus,
.form-group textarea:focus { border-color: #16a34a; }
.form-group select.error,
.form-group textarea.error { border-color: #dc2626; }
.form-group textarea { resize: vertical; min-height: 120px; }

.field-error { font-size: 0.78rem; color: #dc2626; }
.char-count  { font-size: 0.76rem; color: #9ca3af; text-align: right; }
.char-count.warn { color: #f59e0b; }

.submit-error {
  background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
  border-radius: 8px; padding: 10px 14px; font-size: 0.875rem;
}

.form-footer { display: flex; gap: 10px; justify-content: flex-end; padding-top: 4px; border-top: 1px solid #f3f4f6; }
.btn-cancel {
  padding: 9px 20px; border: 1.5px solid #d1d5db; border-radius: 8px;
  color: #374151; font-size: 0.875rem; font-weight: 500; text-decoration: none;
}
.btn-submit {
  display: flex; align-items: center; gap: 7px;
  padding: 9px 22px; background: #16a34a; color: white;
  border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer;
}
.btn-submit:hover:not(:disabled) { background: #15803d; }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

.recent-card {
  background: white; border-radius: 12px; padding: 20px 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 12px;
}
.recent-card h3 { margin: 0; font-size: 0.9rem; font-weight: 700; color: #374151; }
.recent-item { border-top: 1px solid #f3f4f6; padding-top: 10px; display: flex; flex-direction: column; gap: 4px; }
.recent-meta { display: flex; align-items: center; gap: 10px; }
.recent-date { font-size: 0.75rem; color: #9ca3af; }
.recent-notes { margin: 0; font-size: 0.875rem; color: #374151; line-height: 1.5; }

.loading-state { display: flex; justify-content: center; padding: 60px; }
.spinner-lg {
  width: 36px; height: 36px; border: 3px solid #dcfce7;
  border-top-color: #16a34a; border-radius: 50%; animation: spin 0.7s linear infinite;
}
.not-found { text-align: center; padding: 60px; color: #6b7280; }
.not-found a { color: #16a34a; text-decoration: none; font-weight: 500; }
.spinner {
  width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>