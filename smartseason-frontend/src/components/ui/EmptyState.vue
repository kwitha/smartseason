<template>
  <div class="empty-state">
    <div class="empty-state__icon">{{ icon }}</div>
    <h3 class="empty-state__title">{{ title }}</h3>
    <p v-if="description" class="empty-state__desc">{{ description }}</p>
    <slot name="action" />
  </div>
</template>

<script setup lang="ts">
defineProps<{
  icon?: string
  title: string
  description?: string
}>()
</script>

<style scoped>
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 24px;
  text-align: center;
  gap: 10px;
}

.empty-state__icon {
  font-size: 3rem;
  line-height: 1;
  margin-bottom: 4px;
  filter: grayscale(0.2);
}

.empty-state__title {
  font-size: 1rem;
  font-weight: 700;
  color: #374151;
  margin: 0;
}

.empty-state__desc {
  font-size: 0.85rem;
  color: #9ca3af;
  margin: 0;
  max-width: 320px;
  line-height: 1.5;
}
</style>