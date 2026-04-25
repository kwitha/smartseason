<template>
  <div class="assign-agent">
    <div v-if="pageLoading" class="loading-state"><div class="spinner-lg" /></div>

    <div v-else-if="field" class="content">

      <!-- Field summary -->
      <div class="field-summary">
        <div class="summary-row">
          <span class="summary-label">Field</span>
          <span class="summary-value">{{ field.name }}</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Crop</span>
          <span class="summary-value">{{ field.crop_type }}</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Stage</span>
          <StageBadge :stage="field.stage" />
        </div>
        <div class="summary-row">
          <span class="summary-label">Currently Assigned</span>
          <span class="summary-value">
            <span v-if="currentAgents.length">
              {{ currentAgents.map(a => a.agent?.name ?? 'Agent #' + a.agent_id).join(', ') }}
            </span>
            <span v-else class="unassigned">None</span>
          </span>
        </div>
      </div>

      <!-- Assign form -->
      <div class="assign-card">
        <h3>Assign an Agent</h3>
        <p class="assign-hint">
          Select an agent to assign to this field. Multiple agents can be assigned.
          An agent already assigned will be skipped.
        </p>

        <div v-if="loadingAgents" class="agents-loading">Loading agents…</div>
        <div v-else-if="agents.length === 0" class="no-agents">No agents found.</div>

        <div v-else class="agents-list">
          <label
            v-for="agent in agents"
            :key="agent.id"
            class="agent-option"
            :class="{
              selected: selectedAgentId === agent.id,
              assigned: isAssigned(agent.id)
            }"
          >
            <input type="radio" :value="agent.id" v-model="selectedAgentId" class="agent-radio" />
            <div class="agent-info">
              <div class="agent-avatar">{{ agent.name?.charAt(0).toUpperCase() }}</div>
              <div class="agent-details">
                <span class="agent-name-text">{{ agent.name }}</span>
                <span class="agent-email">{{ agent.email }}</span>
              </div>
            </div>
            <span v-if="isAssigned(agent.id)" class="current-tag">Assigned</span>
          </label>
        </div>

        <div v-if="submitError" class="submit-error">{{ submitError }}</div>

        <div class="form-footer">
          <RouterLink :to="`/admin/fields/${field.id}`" class="btn-cancel">Cancel</RouterLink>
          <button
            class="btn-assign"
            :disabled="saving || !selectedAgentId || isAssigned(selectedAgentId)"
            @click="handleAssign"
          >
            <span v-if="saving" class="spinner" />
            {{ saving ? 'Assigning…' : 'Assign Agent' }}
          </button>
        </div>
      </div>

      <!-- Current assignments -->
      <div v-if="currentAgents.length" class="assignments-card">
        <h3>Current Assignments</h3>
        <ul class="assignments-list">
          <li v-for="a in currentAgents" :key="a.id" class="assignment-item">
            <div class="assignment-agent">
              <div class="agent-avatar sm">{{ a.agent?.name?.charAt(0).toUpperCase() ?? '?' }}</div>
              <div>
                <p class="agent-name-text">{{ a.agent?.name ?? 'Agent #' + a.agent_id }}</p>
                <p class="agent-email">Assigned {{ formatDate(a.assigned_at) }}</p>
              </div>
            </div>
            <button
              class="btn-unassign"
              :disabled="unassigning === a.agent_id"
              @click="handleUnassign(a.agent_id)"
            >
              {{ unassigning === a.agent_id ? '…' : 'Remove' }}
            </button>
          </li>
        </ul>
      </div>

    </div>

    <div v-else class="not-found">
      <p>Field not found.</p>
      <RouterLink to="/admin/fields">← Back to Fields</RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useFieldsStore } from '@/stores/fields'
import { getAgents, assignAgent, unassignAgent } from '@/api/fields'
import type { Field } from '@/types'
import StageBadge from '@/components/fields/StageBadge.vue'

const route       = useRoute()
const router      = useRouter()
const fieldsStore = useFieldsStore()

const field           = ref<Field | null>(null)
const agents          = ref<any[]>([])
const selectedAgentId = ref<number | null>(null)
const pageLoading     = ref(true)
const loadingAgents   = ref(false)
const saving          = ref(false)
const unassigning     = ref<number | null>(null)
const submitError     = ref('')

const currentAgents = computed(() => field.value?.assignments ?? [])

function isAssigned(agentId: number) {
  return currentAgents.value.some(a => a.agent_id === agentId)
}

onMounted(async () => {
  try {
    await fieldsStore.fetchField(Number(route.params.id))
    field.value = fieldsStore.currentField

    loadingAgents.value = true
    const res = await getAgents()
    agents.value = res.data?.data ?? res.data ?? []
  } catch {
    // handled by field null check
  } finally {
    pageLoading.value   = false
    loadingAgents.value = false
  }
})

function formatDate(d?: string) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-KE', { year: 'numeric', month: 'short', day: 'numeric' })
}

async function handleAssign() {
  if (!field.value || !selectedAgentId.value) return
  saving.value      = true
  submitError.value = ''
  try {
    await assignAgent(field.value.id, selectedAgentId.value)
    // Refresh to get updated assignments list
    await fieldsStore.fetchField(field.value.id)
    field.value       = fieldsStore.currentField
    selectedAgentId.value = null
  } catch (e: any) {
    submitError.value = e?.response?.data?.message || 'Failed to assign agent.'
  } finally {
    saving.value = false
  }
}

async function handleUnassign(agentId: number) {
  if (!field.value) return
  unassigning.value = agentId
  try {
    await unassignAgent(field.value.id, agentId)
    await fieldsStore.fetchField(field.value.id)
    field.value = fieldsStore.currentField
  } catch (e: any) {
    submitError.value = e?.response?.data?.message || 'Failed to remove agent.'
  } finally {
    unassigning.value = null
  }
}
</script>

<style scoped>
.assign-agent { max-width: 640px; }
.content { display: flex; flex-direction: column; gap: 20px; }

.field-summary {
  background: white; border-radius: 12px; padding: 20px 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 12px;
}
.summary-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.summary-label { font-size: 0.82rem; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 0.04em; }
.summary-value { font-size: 0.9rem; color: #111827; font-weight: 500; }
.unassigned { color: #9ca3af; font-style: italic; }

.assign-card {
  background: white; border-radius: 12px; padding: 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 16px;
}
.assign-card h3 { margin: 0; font-size: 1rem; font-weight: 700; color: #14532d; }
.assign-hint { margin: 0; font-size: 0.85rem; color: #6b7280; line-height: 1.5; }

.agents-loading, .no-agents { text-align: center; padding: 24px; color: #6b7280; font-size: 0.875rem; }

.agents-list { display: flex; flex-direction: column; gap: 8px; }

.agent-option {
  display: flex; align-items: center; gap: 12px; padding: 12px 16px;
  border: 1.5px solid #e5e7eb; border-radius: 10px; cursor: pointer;
  transition: border-color 0.15s, background 0.15s; position: relative;
}
.agent-option:hover:not(.assigned) { border-color: #86efac; background: #f0fdf4; }
.agent-option.selected             { border-color: #16a34a; background: #f0fdf4; }
.agent-option.assigned             { opacity: 0.6; cursor: not-allowed; background: #f9fafb; }
.agent-radio { display: none; }

.agent-info   { display: flex; align-items: center; gap: 12px; flex: 1; }
.agent-avatar {
  width: 38px; height: 38px; background: #14532d; color: white; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.95rem; font-weight: 700; flex-shrink: 0;
}
.agent-avatar.sm { width: 32px; height: 32px; font-size: 0.8rem; }
.agent-details  { display: flex; flex-direction: column; gap: 2px; }
.agent-name-text { font-size: 0.9rem; font-weight: 600; color: #111827; }
.agent-email     { font-size: 0.78rem; color: #6b7280; }

.current-tag {
  font-size: 0.72rem; font-weight: 600; background: #dcfce7; color: #15803d;
  padding: 2px 8px; border-radius: 99px;
}

.submit-error {
  background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
  border-radius: 8px; padding: 10px 14px; font-size: 0.875rem;
}

.form-footer {
  display: flex; gap: 10px; justify-content: flex-end;
  padding-top: 4px; border-top: 1px solid #f3f4f6;
}
.btn-cancel {
  padding: 9px 20px; border: 1.5px solid #d1d5db; border-radius: 8px;
  color: #374151; font-size: 0.875rem; font-weight: 500; text-decoration: none;
}
.btn-assign {
  display: flex; align-items: center; gap: 7px;
  padding: 9px 22px; background: #16a34a; color: white;
  border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600;
  cursor: pointer; transition: background 0.15s;
}
.btn-assign:hover:not(:disabled) { background: #15803d; }
.btn-assign:disabled { opacity: 0.5; cursor: not-allowed; }

/* Current assignments */
.assignments-card {
  background: white; border-radius: 12px; padding: 20px 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.assignments-card h3 { margin: 0 0 14px; font-size: 0.95rem; font-weight: 700; color: #374151; }
.assignments-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 10px; }
.assignment-item {
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
  padding: 10px 0; border-bottom: 1px solid #f3f4f6;
}
.assignment-item:last-child { border-bottom: none; }
.assignment-agent { display: flex; align-items: center; gap: 10px; }

.btn-unassign {
  padding: 5px 12px; background: #fef2f2; color: #dc2626;
  border: 1px solid #fecaca; border-radius: 6px; font-size: 0.75rem;
  font-weight: 600; cursor: pointer; white-space: nowrap;
}
.btn-unassign:hover:not(:disabled) { background: #fee2e2; }
.btn-unassign:disabled { opacity: 0.5; cursor: not-allowed; }

.loading-state { display: flex; justify-content: center; padding: 60px; }
.spinner-lg {
  width: 36px; height: 36px; border: 3px solid #dcfce7;
  border-top-color: #16a34a; border-radius: 50%; animation: spin 0.7s linear infinite;
}
.not-found { text-align: center; padding: 60px; color: #6b7280; }
.not-found a { color: #16a34a; text-decoration: none; font-weight: 500; }
.spinner {
  width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>