<template>
  <span class="stage-badge" :class="`stage-badge--${stage}`">
    {{ emoji }} {{ label }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ stage?: string }>()

const stageMap: Record<string, { label: string; emoji: string }> = {
  planted:   { label: 'Planted',   emoji: '🌱' },
  growing:   { label: 'Growing',   emoji: '🌿' },
  ready:     { label: 'Ready',     emoji: '🌾' },
  harvested: { label: 'Harvested', emoji: '📦' },
}

const info  = computed(() => stageMap[props.stage ?? ''] ?? { label: props.stage ?? 'Unknown', emoji: '🌍' })
const label = computed(() => info.value.label)
const emoji = computed(() => info.value.emoji)
</script>

<style scoped>
.stage-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
}

.stage-badge--planted   { background: #dcfce7; color: #14532d; }
.stage-badge--growing   { background: #d1fae5; color: #065f46; }
.stage-badge--ready     { background: #fef9c3; color: #713f12; }
.stage-badge--harvested { background: #e0e7ff; color: #3730a3; }
</style>