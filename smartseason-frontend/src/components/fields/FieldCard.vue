<template>
  <div class="field-card" @click="emit('view')">
    <!-- Header -->
    <div class="field-card__header">
      <h3 class="field-card__name">{{ field.name }}</h3>
      <StatusBadge :status="field.status" />
    </div>

    <!-- Crop + Stage row -->
    <div class="field-card__meta">
      <span class="field-card__crop">🌾 {{ field.crop_type }}</span>
      <StageBadge :stage="field.stage" />
    </div>

    <!-- Planting date -->
    <div class="field-card__date">
      🗓 Planted {{ formatDate(field.planting_date) }}
    </div>

    <!-- Updates count -->
    <div class="field-card__updates">
      📝 {{ field.updates?.length ?? 0 }} update{{ field.updates?.length === 1 ? '' : 's' }}
    </div>

    <!-- Actions -->
    <div class="field-card__footer" @click.stop>
      <button class="field-card__btn field-card__btn--view" @click="emit('view')">
        View
      </button>
      <button
        v-if="showEdit"
        class="field-card__btn field-card__btn--edit"
        @click="emit('edit')"
      >
        Edit
      </button>
      <button
        v-if="showDelete"
        class="field-card__btn field-card__btn--delete"
        @click="emit('delete')"
      >
        Delete
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Field } from '@/types'
import StatusBadge from '@/components/fields/StatusBadge.vue'
import StageBadge  from '@/components/fields/StageBadge.vue'

defineProps<{
  field:      Field
  showEdit?:  boolean
  showDelete?: boolean
}>()

const emit = defineEmits<{
  (e: 'view'): void
  (e: 'edit'): void
  (e: 'delete'): void
}>()

function formatDate(d?: string) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-KE', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>

<style scoped>
.field-card {
  background: white;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.field-card:hover {
  border-color: #86efac;
  box-shadow: 0 4px 16px rgba(22,163,74,0.08);
}

.field-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
}

.field-card__name {
  font-size: 0.95rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
  line-height: 1.3;
}

.field-card__meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.field-card__crop {
  font-size: 0.8rem;
  color: #4b5563;
  font-weight: 500;
}

.field-card__date,
.field-card__updates {
  font-size: 0.78rem;
  color: #6b7280;
}

.field-card__footer {
  display: flex;
  gap: 6px;
  padding-top: 6px;
  border-top: 1px solid #f3f4f6;
  margin-top: 2px;
}

.field-card__btn {
  flex: 1;
  padding: 6px 0;
  border-radius: 6px;
  font-size: 0.78rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.15s;
}

.field-card__btn--view   { background: #f0fdf4; color: #15803d; }
.field-card__btn--view:hover { background: #dcfce7; }

.field-card__btn--edit   { background: #eff6ff; color: #1d4ed8; }
.field-card__btn--edit:hover { background: #dbeafe; }

.field-card__btn--delete { background: #fef2f2; color: #dc2626; }
.field-card__btn--delete:hover { background: #fee2e2; }
</style>