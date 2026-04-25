<template>
  <div :class="['stat-card', accent && `stat-card--${accent}`]">
    <div class="stat-card__icon-wrap">
      <slot name="icon" />
    </div>
    <div class="stat-card__body">
      <p class="stat-card__label">{{ label }}</p>
      <p class="stat-card__value">
        <span v-if="loading" class="stat-card__skeleton" />
        <template v-else>{{ formattedValue }}</template>
      </p>
      <p v-if="sub" class="stat-card__sub">{{ sub }}</p>
    </div>
    <div v-if="trend !== undefined" class="stat-card__trend" :class="trendClass">
      <span class="stat-card__trend-arrow">{{ trend >= 0 ? '↑' : '↓' }}</span>
      {{ Math.abs(trend) }}%
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

type Accent = 'green' | 'yellow' | 'red' | 'blue' | 'purple'

const props = defineProps<{
  label: string
  value: number | string
  sub?: string
  trend?: number          // positive = up, negative = down
  accent?: Accent
  loading?: boolean
  prefix?: string
  suffix?: string
}>()

const formattedValue = computed(() => {
  const v = props.value
  if (typeof v === 'number') {
    return `${props.prefix ?? ''}${v.toLocaleString()}${props.suffix ?? ''}`
  }
  return v
})

const trendClass = computed(() => {
  if (props.trend === undefined) return ''
  return props.trend >= 0 ? 'stat-card__trend--up' : 'stat-card__trend--down'
})
</script>

<style scoped>
.stat-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  position: relative;
  overflow: hidden;
  transition: box-shadow 0.15s ease, transform 0.15s ease;
}

.stat-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.08);
  transform: translateY(-1px);
}

/* Accent left border */
.stat-card--green  { border-left: 4px solid #16a34a; }
.stat-card--yellow { border-left: 4px solid #ca8a04; }
.stat-card--red    { border-left: 4px solid #dc2626; }
.stat-card--blue   { border-left: 4px solid #2563eb; }
.stat-card--purple { border-left: 4px solid #7c3aed; }

.stat-card__icon-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #f3f4f6;
  margin-bottom: 8px;
  font-size: 1.2rem;
}

.stat-card--green  .stat-card__icon-wrap { background: #dcfce7; }
.stat-card--yellow .stat-card__icon-wrap { background: #fef9c3; }
.stat-card--red    .stat-card__icon-wrap { background: #fee2e2; }
.stat-card--blue   .stat-card__icon-wrap { background: #dbeafe; }
.stat-card--purple .stat-card__icon-wrap { background: #ede9fe; }

.stat-card__label {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #6b7280;
  margin: 0;
}

.stat-card__value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #111827;
  margin: 2px 0 0;
  line-height: 1.1;
  min-height: 2rem;
}

.stat-card__sub {
  font-size: 0.75rem;
  color: #9ca3af;
  margin: 2px 0 0;
}

.stat-card__skeleton {
  display: inline-block;
  width: 80px;
  height: 1.75rem;
  background: linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%);
  background-size: 200% 100%;
  border-radius: 6px;
  animation: shimmer 1.4s infinite;
}

.stat-card__trend {
  position: absolute;
  top: 16px;
  right: 16px;
  font-size: 0.72rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 2px;
  padding: 2px 7px;
  border-radius: 9999px;
}

.stat-card__trend--up   { background: #dcfce7; color: #15803d; }
.stat-card__trend--down { background: #fee2e2; color: #b91c1c; }

.stat-card__trend-arrow { font-size: 0.8em; }

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>