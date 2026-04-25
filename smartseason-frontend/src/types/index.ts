// ── User ──────────────────────────────────────────────────────────────

export interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'agent'
  created_at?: string
  updated_at?: string
}

// ── Field Update ──────────────────────────────────────────────────────
// Maps to: field_updates table

export interface FieldUpdate {
  id: number
  field_id: number
  agent_id: number
  agent?: User
  new_stage: string        // planted | growing | ready | harvested
  notes?: string | null
  created_at: string
  updated_at?: string
}

// ── Field Assignment ──────────────────────────────────────────────────
// Maps to: field_assignments table

export interface FieldAssignment {
  id: number
  field_id: number
  agent_id: number
  agent?: User
  assigned_at: string
  created_at?: string
  updated_at?: string
}

// ── Field ─────────────────────────────────────────────────────────────
// Maps to: fields table + computed status via $appends

export interface Field {
  id: number
  name: string
  crop_type: string
  planting_date: string
  stage: 'planted' | 'growing' | 'ready' | 'harvested'
  status: 'active' | 'at_risk' | 'completed'  // computed by Laravel, never stored
  created_by: number
  creator?: User
  assignments?: FieldAssignment[]
  updates?: FieldUpdate[]
  created_at?: string
  updated_at?: string
}

// ── Auth ──────────────────────────────────────────────────────────────

export interface LoginCredentials {
  email: string
  password: string
}

export interface AuthResponse {
  token: string
  user: User
}

// ── API helpers ───────────────────────────────────────────────────────

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}