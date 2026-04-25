<template>
  <div :class="['field', block && 'field--block']">
    <label v-if="label" :for="selectId" class="field__label">
      {{ label }}
      <span v-if="required" class="field__required" aria-hidden="true">*</span>
    </label>

    <div :class="['field__wrap', error && 'field__wrap--error', disabled && 'field__wrap--disabled']">
      <span v-if="$slots.prefix" class="field__affix field__affix--prefix">
        <slot name="prefix" />
      </span>

      <select
        :id="selectId"
        v-bind="$attrs"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :class="['field__select', !modelValue && 'field__select--placeholder']"
        @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
        @blur="$emit('blur', $event)"
      >
        <option v-if="placeholder" value="" disabled :selected="!modelValue">
          {{ placeholder }}
        </option>
        <option
          v-for="opt in normalizedOptions"
          :key="opt.value"
          :value="opt.value"
          :disabled="opt.disabled"
        >
          {{ opt.label }}
        </option>
      </select>

      <span class="field__chevron" aria-hidden="true">▾</span>
    </div>

    <p v-if="error" class="field__error">{{ error }}</p>
    <p v-else-if="hint" class="field__hint">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

type OptionRaw = string | { label: string; value: string | number; disabled?: boolean }

const props = withDefaults(defineProps<{
  modelValue?: string | number
  options: OptionRaw[]
  label?: string
  placeholder?: string
  error?: string
  hint?: string
  disabled?: boolean
  required?: boolean
  block?: boolean
  id?: string
}>(), {
  disabled: false,
  required: false,
  block: false,
})

defineEmits<{
  'update:modelValue': [value: string]
  blur: [event: FocusEvent]
}>()

const selectId = computed(() => props.id ?? `select-${Math.random().toString(36).slice(2, 7)}`)

const normalizedOptions = computed(() =>
  props.options.map(o =>
    typeof o === 'string'
      ? { label: o, value: o, disabled: false }
      : { disabled: false, ...o }
  )
)
</script>

<style scoped>
.field {
  display: inline-flex;
  flex-direction: column;
  gap: 5px;
}

.field--block { width: 100%; }
.field--block .field__wrap { width: 100%; }

.field__label {
  font-size: 0.82rem;
  font-weight: 600;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 2px;
}

.field__required { color: #dc2626; font-size: 0.9em; }

.field__wrap {
  display: flex;
  align-items: center;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  transition: border-color 0.15s, box-shadow 0.15s;
  overflow: hidden;
  position: relative;
}

.field__wrap:focus-within {
  border-color: #16a34a;
  box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
}

.field__wrap--error { border-color: #dc2626; }
.field__wrap--error:focus-within {
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(220,38,38,0.12);
}

.field__wrap--disabled { background: #f9fafb; opacity: 0.65; }

.field__select {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  padding: 9px 36px 9px 12px;
  font-size: 0.875rem;
  color: #111827;
  font-family: inherit;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  min-width: 0;
  width: 100%;
}

.field__select--placeholder { color: #9ca3af; }
.field__select:disabled { cursor: not-allowed; }

.field__chevron {
  position: absolute;
  right: 12px;
  pointer-events: none;
  color: #9ca3af;
  font-size: 0.75rem;
  line-height: 1;
}

.field__affix {
  display: flex;
  align-items: center;
  padding: 0 10px;
  color: #9ca3af;
  font-size: 0.875rem;
  flex-shrink: 0;
  user-select: none;
}

.field__affix--prefix { border-right: 1px solid #f3f4f6; background: #f9fafb; }

.field__error {
  font-size: 0.75rem;
  color: #dc2626;
  margin: 0;
}
.field__error::before { content: '⚠ '; }

.field__hint {
  font-size: 0.75rem;
  color: #9ca3af;
  margin: 0;
}
</style>