<template>
  <div class="create-field">
    <form @submit.prevent="handleSubmit" class="field-form">
      <div class="form-section">
        <h2 class="section-title">Basic Information</h2>

        <div class="form-row">
          <div class="form-group">
            <label>Field Name <span class="required">*</span></label>
            <input v-model="form.name" type="text" placeholder="e.g. North Block A" required />
            <span v-if="errors.name" class="field-error">{{ errors.name }}</span>
          </div>
          <div class="form-group">
            <label>Location <span class="required">*</span></label>
            <input v-model="form.location" type="text" placeholder="e.g. Nakuru, Kenya" required />
            <span v-if="errors.location" class="field-error">{{ errors.location }}</span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Size (acres) <span class="required">*</span></label>
            <input v-model.number="form.size_acres" type="number" min="0.1" step="0.1" placeholder="0.0" required />
            <span v-if="errors.size_acres" class="field-error">{{ errors.size_acres }}</span>
          </div>
          <div class="form-group">
            <label>Crop Type <span class="required">*</span></label>
            <input v-model="form.crop_type" type="text" placeholder="e.g. Maize, Tomatoes" required />
            <span v-if="errors.crop_type" class="field-error">{{ errors.crop_type }}</span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Current Stage</label>
            <select v-model="form.current_stage">
              <option value="">Select stage</option>
              <option value="land_preparation">🪵 Land Preparation</option>
              <option value="planting">🌱 Planting</option>
              <option value="germination">🌿 Germination</option>
              <option value="vegetative">🍃 Vegetative</option>
              <option value="flowering">🌸 Flowering</option>
              <option value="fruiting">🍅 Fruiting</option>
              <option value="ripening">🟡 Ripening</option>
              <option value="harvest">🌾 Harvest</option>
              <option value="post_harvest">📦 Post Harvest</option>
              <option value="fallow">💤 Fallow</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select v-model="form.status">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="pending">Pending</option>
            </select>
          </div>
        </div>
      </div>

      <div class="form-section">
        <h2 class="section-title">Planting Details</h2>
        <div class="form-row">
          <div class="form-group">
            <label>Planting Date</label>
            <input v-model="form.planting_date" type="date" />
          </div>
          <div class="form-group">
            <label>Expected Harvest Date</label>
            <input v-model="form.expected_harvest_date" type="date" />
          </div>
        </div>

        <div class="form-group full">
          <label>Notes</label>
          <textarea v-model="form.notes" rows="4" placeholder="Any additional notes about this field…" />
        </div>
      </div>

      <div class="form-section">
        <h2 class="section-title">Agent Assignment</h2>
        <div class="form-group">
          <label>Assign Agent</label>
          <select v-model="form.agent_id">
            <option value="">Unassigned</option>
            <option v-for="agent in agents" :key="agent.id" :value="agent.id">
              {{ agent.name }} ({{ agent.email }})
            </option>
          </select>
          <p class="hint">The assigned agent will be able to view and update this field.</p>
        </div>
      </div>

      <div v-if="submitError" class="submit-error">{{ submitError }}</div>

      <div class="form-footer">
        <RouterLink to="/admin/fields" class="btn-cancel">Cancel</RouterLink>
        <button type="submit" class="btn-submit" :disabled="loading">
          <span v-if="loading" class="spinner" />
          {{ loading ? 'Creating…' : 'Create Field' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useFieldsStore } from '@/stores/fields'
import { getAgents } from '@/api/fields'

const router = useRouter()
const fieldsStore = useFieldsStore()

const loading = ref(false)
const submitError = ref('')
const agents = ref<any[]>([])
const errors = ref<Record<string, string>>({})

const form = ref({
  name: '',
  location: '',
  size_acres: '',
  crop_type: '',
  current_stage: '',
  status: 'active',
  planting_date: '',
  expected_harvest_date: '',
  notes: '',
  agent_id: '',
})

onMounted(async () => {
  try {
    const res = await getAgents()
    agents.value = res.data?.data ?? res.data ?? []
  } catch {
    // agents list optional
  }
})

function validate() {
  errors.value = {}
  if (!form.value.name.trim()) errors.value.name = 'Field name is required'
  if (!form.value.location.trim()) errors.value.location = 'Location is required'
  if (!form.value.size_acres) errors.value.size_acres = 'Size is required'
  if (!form.value.crop_type.trim()) errors.value.crop_type = 'Crop type is required'
  return Object.keys(errors.value).length === 0
}

async function handleSubmit() {
  if (!validate()) return
  submitError.value = ''
  loading.value = true
  try {
    const payload: any = { ...form.value }
    if (!payload.agent_id) delete payload.agent_id
    if (!payload.current_stage) delete payload.current_stage
    if (!payload.planting_date) delete payload.planting_date
    if (!payload.expected_harvest_date) delete payload.expected_harvest_date
    if (!payload.notes) delete payload.notes

    await fieldsStore.createField(payload)
    router.push('/admin/fields')
  } catch (e: any) {
    const data = e?.response?.data
    if (data?.errors) {
      // Laravel validation errors
      Object.entries(data.errors).forEach(([k, v]: any) => {
        errors.value[k] = Array.isArray(v) ? v[0] : v
      })
    } else {
      submitError.value = data?.message || 'Failed to create field. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.create-field { max-width: 820px; }

.field-form { display: flex; flex-direction: column; gap: 24px; }

.form-section {
  background: white;
  border-radius: 12px;
  padding: 24px 28px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.section-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #14532d;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 0 4px;
  padding-bottom: 10px;
  border-bottom: 1.5px solid #dcfce7;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-group.full { grid-column: 1 / -1; }

.form-group label {
  font-size: 0.85rem;
  font-weight: 500;
  color: #374151;
}
.required { color: #dc2626; }

.form-group input,
.form-group select,
.form-group textarea {
  padding: 9px 13px;
  border: 1.5px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9rem;
  font-family: inherit;
  outline: none;
  background: white;
  transition: border-color 0.15s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color: #16a34a; }

.form-group textarea { resize: vertical; min-height: 90px; }

.field-error { font-size: 0.78rem; color: #dc2626; }

.hint { font-size: 0.8rem; color: #6b7280; margin: 2px 0 0; }

.submit-error {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 0.875rem;
}

.form-footer {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding: 4px 0;
}

.btn-cancel {
  padding: 10px 22px;
  border: 1.5px solid #d1d5db;
  border-radius: 8px;
  color: #374151;
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
  transition: border-color 0.15s;
}
.btn-cancel:hover { border-color: #9ca3af; }

.btn-submit {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 24px;
  background: #16a34a;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}
.btn-submit:hover:not(:disabled) { background: #15803d; }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

.spinner {
  width: 15px; height: 15px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 600px) {
  .form-row { grid-template-columns: 1fr; }
}
</style>