import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  getFields,
  getField,
  createField as apiCreate,
  updateField as apiUpdate,
  deleteField as apiDelete,
  getMyFields,
} from '@/api/fields'
import { useAuthStore } from './auth'
import type { Field } from '@/types'

export const useFieldsStore = defineStore('fields', () => {
  const fields       = ref<Field[]>([])
  const currentField = ref<Field | null>(null)
  const loading      = ref(false)
  const error        = ref<string | null>(null)

  // Handles both plain array and { data: [] } Laravel responses
  function extractList(res: any): Field[] {
    const p = res.data
    if (Array.isArray(p)) return p
    if (Array.isArray(p?.data)) return p.data
    return []
  }

  function extractItem(res: any): Field {
    const p = res.data
    if (p?.data && !Array.isArray(p.data)) return p.data
    return p
  }

  // ── Fetch all ─────────────────────────────────────────────────────
  async function fetchFields() {
    loading.value = true
    error.value   = null
    try {
      const auth = useAuthStore()
      const res  = auth.isAgent ? await getMyFields() : await getFields()
      fields.value = extractList(res)
    } catch (e: any) {
      error.value = e?.response?.data?.message || 'Failed to load fields.'
    } finally {
      loading.value = false
    }
  }

  // ── Fetch single ──────────────────────────────────────────────────
  async function fetchField(id: number) {
    loading.value = true
    error.value   = null
    try {
      const res = await getField(id)
      currentField.value = extractItem(res)
    } catch (e: any) {
      error.value        = e?.response?.data?.message || 'Failed to load field.'
      currentField.value = null
    } finally {
      loading.value = false
    }
  }

  // ── Create ────────────────────────────────────────────────────────
  async function createField(data: object) {
    const res     = await apiCreate(data)
    const created = extractItem(res)
    fields.value.unshift(created)
    return created
  }

  // ── Update ────────────────────────────────────────────────────────
  async function updateField(id: number, data: object) {
    const res     = await apiUpdate(id, data)
    const updated = extractItem(res)
    const idx     = fields.value.findIndex(f => f.id === id)
    if (idx !== -1) fields.value[idx] = updated
    if (currentField.value?.id === id) currentField.value = updated
    return updated
  }

  // ── Delete ────────────────────────────────────────────────────────
  async function deleteField(id: number) {
    await apiDelete(id)
    fields.value = fields.value.filter(f => f.id !== id)
    if (currentField.value?.id === id) currentField.value = null
  }

  return {
    fields,
    currentField,
    loading,
    error,
    fetchFields,
    fetchField,
    createField,
    updateField,
    deleteField,
  }
})