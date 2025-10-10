<template>
  <div class="min-h-screen w-full text-black bg-gray-200">
    <Nav/>
    <Sidebar />
    <main class="fixed top-12 left-0 md:left-20 lg:left-64 right-0 bottom-0 overflow-y-auto bg-gray-200 p-4">
      <router-view></router-view>
    </main>
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"></div>
  </div>
</template>


<script setup>
import Sidebar from '../../components/Sidebar.vue'
import Nav from '../../components/Nav.vue'
import { ref, provide } from 'vue'

const sidebarOpen = ref(false)
provide('sidebarOpen', sidebarOpen)
</script>
