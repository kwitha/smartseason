<template>
  <div class="page">
    <h2>My Fields</h2>

    <div v-if="loading">Loading...</div>

    <div v-else>
      <div v-if="fields.length === 0">
        No fields assigned
      </div>

      <div v-else class="grid">
        <div
          v-for="field in fields"
          :key="field.id"
          class="card"
        >
          <h3>{{ field.name }}</h3>

          <p>Crop: {{ field.crop_type }}</p>

          <p>Stage: {{ field.stage }}</p>

          <p>Status: {{ field.status }}</p>

          <p>Planted: {{ field.planting_date }}</p>

          <button @click="goTo(field.id)">
            View
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'

const router = useRouter()

const loading = ref(false)
const fields = ref<any[]>([])

onMounted(async () => {
  loading.value = true
  try {
    const res = await api.get('/fields')
    fields.value = res.data
  } finally {
    loading.value = false
  }
})

function goTo(id: number) {
  router.push(`/agent/fields/${id}`)
}
</script>
<style scoped>
.my-fields { display: flex; flex-direction: column; gap: 20px; }

.filters { display: flex; gap: 12px; flex-wrap: wrap; }

.search-input {
  flex: 1; min-width: 200px;
  padding: 9px 14px; border: 1.5px solid #d1d5db; border-radius: 8px;
  font-size: 0.875rem; outline: none;
}
.search-input:focus { border-color: #16a34a; }

.filter-select {
  padding: 9px 14px; border: 1.5px solid #d1d5db; border-radius: 8px;
  font-size: 0.875rem; background: white; outline: none; cursor: pointer;
}
.filter-select:focus { border-color: #16a34a; }

.loading-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 16px;
}
.skeleton-card {
  height: 200px;
  background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
  border-radius: 12px;
}
@keyframes shimmer { to { background-position: -200% 0; } }

.fields-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 16px;
}

.error-state { text-align: center; padding: 40px; color: #dc2626; }
.error-state button {
  margin-top: 12px; padding: 8px 20px; background: #16a34a;
  color: white; border: none; border-radius: 6px; cursor: pointer;
}
</style>