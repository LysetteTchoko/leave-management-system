<template>
  <nav class="bg-white h-12 top-0 left-0 flex justify-start fixed items-center w-full z-50 shadow">
    <div class="flex text-[#287196] mx-2 md:mx-0">
      <button class="md:hidden" @click="toggleSidebar">
        <IconMenu class="stroke-2"/>
      </button>
      <div class=" md:mx-12 h-10 w-32 mt-1">
        <img :src="Logo" class="w-full h-full">
      </div>
    </div>
  </nav>
</template>
<script setup>
import  Logo from '../assets/logo.jpg'
import IconMenu from './icons/IconMenu.vue'
import { inject } from 'vue'

const sidebarOpen = inject('sidebarOpen')

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
</script>

