<template>
  <div class="update-history">
    <h3 class="update-history__title">Update History</h3>

    <div v-if="!updates.length" class="update-history__empty">
      No updates have been posted yet.
    </div>

    <ol v-else class="update-history__list">
      <li v-for="update in updates" :key="update.id" class="update-history__item">
        <div class="update-history__line" />
        <div class="update-history__dot" />
        <div class="update-history__content">
          <div class="update-history__header">
            <StageBadge :stage="update.new_stage" />
            <span class="update-history__date">{{ formatDate(update.created_at) }}</span>
          </div>
          <p v-if="update.notes" class="update-history__notes">{{ update.notes }}</p>
          <span class="update-history__agent">
            by {{ update.agent?.name ?? 'Agent #' + update.agent_id }}
          </span>
        </div>
      </li>
    </ol>
  </div>
</template>

<script setup lang="ts">
import type { FieldUpdate } from '@/types'
import StageBadge from '@/components/fields/StageBadge.vue'

defineProps<{ updates: FieldUpdate[] }>()

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('en-KE', {
    year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit',
  })
}
</script>

<style scoped>
.update-history {
  background: white;
  border-radius: 12px;
  padding: 20px 22px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.update-history__title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #14532d;
  margin: 0 0 16px;
}

.update-history__empty {
  text-align: center;
  color: #9ca3af;
  font-size: 0.875rem;
  padding: 20px 0;
}

.update-history__list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.update-history__item {
  display: flex;
  gap: 14px;
  position: relative;
  padding-bottom: 20px;
}
.update-history__item:last-child { padding-bottom: 0; }
.update-history__item:last-child .update-history__line { display: none; }

.update-history__line {
  position: absolute;
  left: 7px;
  top: 18px;
  bottom: 0;
  width: 2px;
  background: #e5e7eb;
}

.update-history__dot {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #16a34a;
  border: 2px solid white;
  box-shadow: 0 0 0 2px #16a34a;
  flex-shrink: 0;
  margin-top: 2px;
}

.update-history__content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.update-history__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  flex-wrap: wrap;
}

.update-history__date {
  font-size: 0.75rem;
  color: #9ca3af;
}

.update-history__notes {
  margin: 4px 0 0;
  font-size: 0.875rem;
  color: #374151;
  line-height: 1.5;
}

.update-history__agent {
  font-size: 0.75rem;
  color: #6b7280;
  font-style: italic;
}
</style>