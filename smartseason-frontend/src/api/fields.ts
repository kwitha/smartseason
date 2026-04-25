import api from './axios'

// ── Fields ────────────────────────────────────────────────────────────
export const getFields   = ()              => api.get('/fields')
export const getField    = (id: number)    => api.get(`/fields/${id}`)
export const createField = (data: object)  => api.post('/fields', data)
export const updateField = (id: number, data: object) => api.put(`/fields/${id}`, data)
export const deleteField = (id: number)    => api.delete(`/fields/${id}`)

// ── Field Updates ─────────────────────────────────────────────────────
// POST  /api/fields/{field}/updates  — agent submits a stage update
export const addFieldUpdate  = (fieldId: number, data: { new_stage: string; notes?: string }) =>
  api.post(`/fields/${fieldId}/updates`, data)

export const getFieldUpdates = (fieldId: number) =>
  api.get(`/fields/${fieldId}/updates`)

// ── Field Assignments ─────────────────────────────────────────────────
// GET   /api/fields/{field}/assignments   — who is assigned
// POST  /api/fields/{field}/assignments   — assign an agent
// DELETE /api/fields/{field}/assignments/{agent} — unassign
export const getFieldAssignments  = (fieldId: number) =>
  api.get(`/fields/${fieldId}/assignments`)

export const assignAgent = (fieldId: number, agentId: number) =>
  api.post(`/fields/${fieldId}/assignments`, { agent_id: agentId })

export const unassignAgent = (fieldId: number, agentId: number) =>
  api.delete(`/fields/${fieldId}/assignments/${agentId}`)

// ── Agents list (admin only) ──────────────────────────────────────────
export const getAgents = () => api.get('/agents')

// ── Agent's own assigned fields ───────────────────────────────────────
export const getMyFields = () => api.get('/my-fields')