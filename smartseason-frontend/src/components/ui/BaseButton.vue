<template>
  <component
    :is="tag"
    :type="tag === 'button' ? type : undefined"
    :disabled="disabled || loading"
    :class="[
      'btn',
      `btn--${variant}`,
      `btn--${size}`,
      block && 'btn--block',
      (disabled || loading) && 'btn--disabled',
    ]"
    v-bind="$attrs"
  >
    <LoadingSpinner v-if="loading" size="xs" :variant="spinnerVariant" />
    <span v-if="$slots.icon && !loading" class="btn__icon">
      <slot name="icon" />
    </span>
    <span v-if="$slots.default" class="btn__label">
      <slot />
    </span>
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import LoadingSpinner from './LoadingSpinner.vue'

const props = withDefaults(defineProps<{
  variant?: 'primary' | 'secondary' | 'ghost' | 'danger' | 'outline'
  size?: 'xs' | 'sm' | 'md' | 'lg'
  type?: 'button' | 'submit' | 'reset'
  tag?: 'button' | 'a' | 'RouterLink'
  loading?: boolean
  disabled?: boolean
  block?: boolean
}>(), {
  variant: 'primary',
  size: 'md',
  type: 'button',
  tag: 'button',
  loading: false,
  disabled: false,
  block: false,
})

const spinnerVariant = computed(() =>
  props.variant === 'primary' || props.variant === 'danger' ? 'white' : 'green'
)
</script>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  transition: background 0.15s, box-shadow 0.15s, transform 0.1s, border-color 0.15s;
  text-decoration: none;
  font-family: inherit;
  line-height: 1;
}

.btn:active:not(.btn--disabled) {
  transform: translateY(1px);
}

/* Sizes */
.btn--xs  { padding: 5px 10px;  font-size: 0.72rem; }
.btn--sm  { padding: 7px 14px;  font-size: 0.8rem;  }
.btn--md  { padding: 10px 18px; font-size: 0.875rem; }
.btn--lg  { padding: 13px 24px; font-size: 1rem;    }

/* Primary */
.btn--primary {
  background: #16a34a;
  color: #fff;
}
.btn--primary:hover:not(.btn--disabled) {
  background: #15803d;
  box-shadow: 0 4px 12px rgba(22,163,74,0.3);
}

/* Secondary */
.btn--secondary {
  background: #f3f4f6;
  color: #374151;
}
.btn--secondary:hover:not(.btn--disabled) {
  background: #e5e7eb;
}

/* Outline */
.btn--outline {
  background: transparent;
  color: #16a34a;
  border: 1.5px solid #16a34a;
}
.btn--outline:hover:not(.btn--disabled) {
  background: #f0fdf4;
}

/* Ghost */
.btn--ghost {
  background: transparent;
  color: #6b7280;
}
.btn--ghost:hover:not(.btn--disabled) {
  background: #f9fafb;
  color: #374151;
}

/* Danger */
.btn--danger {
  background: #dc2626;
  color: #fff;
}
.btn--danger:hover:not(.btn--disabled) {
  background: #b91c1c;
  box-shadow: 0 4px 12px rgba(220,38,38,0.3);
}

/* Block */
.btn--block {
  width: 100%;
}

/* Disabled */
.btn--disabled {
  opacity: 0.55;
  cursor: not-allowed;
  pointer-events: none;
}

.btn__icon {
  display: inline-flex;
  align-items: center;
  flex-shrink: 0;
  font-size: 1em;
}
</style>