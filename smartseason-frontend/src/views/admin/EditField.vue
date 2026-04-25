<template>
  <div class="page">
    <h2>Edit Field</h2>

    <div v-if="loading">Loading...</div>

    <div v-else class="form">

      <input v-model="form.name" placeholder="Field name" />

      <input v-model="form.crop_type" placeholder="Crop type" />

      <select v-model="form.stage">
        <option value="planted">Planted</option>
        <option value="growing">Growing</option>
        <option value="ready">Ready</option>
        <option value="harvested">Harvested</option>
      </select>

      <input type="date" v-model="form.planting_date" />

      <button @click="update">Save</button>

      <p v-if="message">{{ message }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const id = route.params.id

const loading = ref(false)
const message = ref('')

const form = ref({
  name: '',
  crop_type: '',
  stage: '',
  planting_date: ''
})

onMounted(async () => {
  loading.value = true
  try {
    const res = await api.get(`/fields/${id}`)
    form.value = res.data
  } finally {
    loading.value = false
  }
})

async function update() {
  message.value = ''
  try {
    await api.put(`/fields/${id}`, form.value)
    message.value = 'Field updated successfully'
  } catch (e: any) {
    message.value = e?.response?.data?.message || 'Update failed'
  }
}
</script>

<style scoped>
.edit-field { max-width: 820px; }
.field-form { display: flex; flex-direction: column; gap: 24px; }

.form-section {
  background: white; border-radius: 12px;
  padding: 24px 28px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  display: flex; flex-direction: column; gap: 20px;
}
.section-title {
  font-size: 0.95rem; font-weight: 700; color: #14532d;
  text-transform: uppercase; letter-spacing: 0.05em;
  margin: 0 0 4px; padding-bottom: 10px; border-bottom: 1.5px solid #dcfce7;
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 0.85rem; font-weight: 500; color: #374151; }
.required { color: #dc2626; }
.form-group input,
.form-group select,
.form-group textarea {
  padding: 9px 13px; border: 1.5px solid #d1d5db; border-radius: 8px;
  font-size: 0.9rem; font-family: inherit; outline: none; background: white; transition: border-color 0.15s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color: #16a34a; }
.form-group textarea { resize: vertical; min-height: 90px; }
.field-error { font-size: 0.78rem; color: #dc2626; }
.submit-error {
  background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
  border-radius: 8px; padding: 10px 14px; font-size: 0.875rem;
}
.form-footer { display: flex; gap: 12px; justify-content: flex-end; }
.btn-cancel {
  padding: 10px 22px; border: 1.5px solid #d1d5db; border-radius: 8px;
  color: #374151; font-size: 0.9rem; font-weight: 500; text-decoration: none;
}
.btn-submit {
  display: flex; align-items: center; gap: 8px; padding: 10px 24px;
  background: #16a34a; color: white; border: none; border-radius: 8px;
  font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.15s;
}
.btn-submit:hover:not(:disabled) { background: #15803d; }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }
.loading-state { display: flex; justify-content: center; padding: 60px; }
.spinner-lg {
  width: 36px; height: 36px; border: 3px solid #dcfce7;
  border-top-color: #16a34a; border-radius: 50%; animation: spin 0.7s linear infinite;
}
.not-found { text-align: center; padding: 60px; color: #6b7280; }
.not-found a { color: #16a34a; text-decoration: none; font-weight: 500; }
.spinner {
  width: 15px; height: 15px; border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
@media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
</style>