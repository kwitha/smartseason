<template>
  <div :class="['spinner-wrap', `spinner-wrap--${size}`]" role="status" :aria-label="label">
    <svg
      :class="['spinner', `spinner--${variant}`]"
      viewBox="0 0 50 50"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <circle
        class="spinner__track"
        cx="25" cy="25" r="20"
        stroke-width="4"
      />
      <circle
        class="spinner__arc"
        cx="25" cy="25" r="20"
        stroke-width="4"
        stroke-linecap="round"
      />
    </svg>
    <span v-if="showLabel" class="spinner__label">{{ label }}</span>
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
  variant?: 'green' | 'white' | 'gray'
  label?: string
  showLabel?: boolean
}>(), {
  size: 'md',
  variant: 'green',
  label: 'Loading…',
  showLabel: false,
})
</script>

<style scoped>
.spinner-wrap {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

/* Sizes */
.spinner-wrap--xs  .spinner { width: 16px; height: 16px; }
.spinner-wrap--sm  .spinner { width: 22px; height: 22px; }
.spinner-wrap--md  .spinner { width: 32px; height: 32px; }
.spinner-wrap--lg  .spinner { width: 48px; height: 48px; }
.spinner-wrap--xl  .spinner { width: 64px; height: 64px; }

.spinner {
  animation: rotate 0.9s linear infinite;
}

.spinner__track {
  stroke: #e5e7eb;
}

/* Green variant */
.spinner--green .spinner__arc {
  stroke: #16a34a;
  stroke-dasharray: 80, 200;
  stroke-dashoffset: 0;
  animation: dash 1.4s ease-in-out infinite;
}

/* White variant */
.spinner--white .spinner__track { stroke: rgba(255,255,255,0.25); }
.spinner--white .spinner__arc {
  stroke: #ffffff;
  stroke-dasharray: 80, 200;
  animation: dash 1.4s ease-in-out infinite;
}

/* Gray variant */
.spinner--gray .spinner__arc {
  stroke: #9ca3af;
  stroke-dasharray: 80, 200;
  animation: dash 1.4s ease-in-out infinite;
}

.spinner__label {
  font-size: 0.78rem;
  color: #6b7280;
  font-weight: 500;
}

@keyframes rotate {
  100% { transform: rotate(360deg); }
}

@keyframes dash {
  0%   { stroke-dasharray: 1, 200; stroke-dashoffset: 0; }
  50%  { stroke-dasharray: 90, 200; stroke-dashoffset: -35px; }
  100% { stroke-dasharray: 90, 200; stroke-dashoffset: -125px; }
}
</style>