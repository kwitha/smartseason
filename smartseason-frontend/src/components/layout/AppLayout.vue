<template>
  <div class="app-layout">
    <AppSidebar />

    <div class="app-main">
      <AppHeader :title="pageTitle" :subtitle="pageSubtitle" />

      <main class="app-content">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AppSidebar from './AppSidebar.vue'
import AppHeader  from './AppHeader.vue'

const route = useRoute()

// Each route can define meta.title and meta.subtitle
const pageTitle    = computed(() => (route.meta.title as string)    ?? 'SmartSeason')
const pageSubtitle = computed(() => (route.meta.subtitle as string) ?? '')
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background: #f0fdf4;
}

.app-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0; /* prevents flex overflow */
  overflow: hidden;
}

.app-content {
  flex: 1;
  padding: 1.75rem;
  overflow-y: auto;
}
</style>