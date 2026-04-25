<template>
  <span class="status-badge" :class="`status-badge--${status}`">
    <span class="status-badge__dot" />
    {{ label }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ status: string }>()

const label = computed(() => {
  const map: Record<string, string> = {
    active:    'Active',
    at_risk:   'At Risk',
    completed: 'Completed',
  }
  return map[props.status] ?? props.status
})
</script>

<style scoped>
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 10px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge__dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

/* active */
.status-badge--active {
  background: #dcfce7;
  color: #14532d;
}
.status-badge--active .status-badge__dot {
  background: #16a34a;
  animation: pulse 2s infinite;
}

/* at_risk */
.status-badge--at_risk {
  background: #fef3c7;
  color: #92400e;
}
.status-badge--at_risk .status-badge__dot {
  background: #f59e0b;
  animation: pulse 2s infinite;
}

/* completed */
.status-badge--completed {
  background: #e0e7ff;
  color: #3730a3;
}
.status-badge--completed .status-badge__dot {
  background: #6366f1;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.4; }
}
</style>