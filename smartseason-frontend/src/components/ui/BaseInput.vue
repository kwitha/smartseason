<template>
  <div :class="['field', block && 'field--block']">
    <label v-if="label" :for="inputId" class="field__label">
      {{ label }}
      <span v-if="required" class="field__required" aria-hidden="true">*</span>
    </label>

    <div :class="['field__wrap', error && 'field__wrap--error', disabled && 'field__wrap--disabled']">
      <span v-if="$slots.prefix" class="field__affix field__affix--prefix">
        <slot name="prefix" />
      </span>

      <input
        :id="inputId"
        v-bind="$attrs"
        :value="modelValue"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :class="['field__input', $slots.prefix && 'field__input--has-prefix', $slots.suffix && 'field__input--has-suffix']"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      />

      <span v-if="$slots.suffix" class="field__affix field__affix--suffix">
        <slot name="suffix" />
      </span>
    </div>

    <p v-if="error" class="field__error">{{ error }}</p>
    <p v-else-if="hint" class="field__hint">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  modelValue?: string | number
  label?: string
  type?: string
  placeholder?: string
  error?: string
  hint?: string
  disabled?: boolean
  required?: boolean
  block?: boolean
  id?: string
}>(), {
  type: 'text',
  disabled: false,
  required: false,
  block: false,
})

defineEmits<{
  'update:modelValue': [value: string]
  blur: [event: FocusEvent]
  focus: [event: FocusEvent]
}>()

const inputId = computed(() => props.id ?? `input-${Math.random().toString(36).slice(2, 7)}`)
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
}

.field__wrap:focus-within {
  border-color: #16a34a;
  box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
}

.field__wrap--error {
  border-color: #dc2626;
}
.field__wrap--error:focus-within {
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(220,38,38,0.12);
}

.field__wrap--disabled {
  background: #f9fafb;
  opacity: 0.65;
}

.field__input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  padding: 9px 12px;
  font-size: 0.875rem;
  color: #111827;
  font-family: inherit;
  min-width: 0;
}

.field__input::placeholder { color: #9ca3af; }
.field__input:disabled { cursor: not-allowed; }
.field__input--has-prefix { padding-left: 6px; }
.field__input--has-suffix { padding-right: 6px; }

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
.field__affix--suffix { border-left: 1px solid #f3f4f6;  background: #f9fafb; }

.field__error {
  font-size: 0.75rem;
  color: #dc2626;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 4px;
}
.field__error::before { content: '⚠'; font-size: 0.8em; }

.field__hint {
  font-size: 0.75rem;
  color: #9ca3af;
  margin: 0;
}
</style>