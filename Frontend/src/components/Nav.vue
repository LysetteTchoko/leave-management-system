<template>
  <nav class="bg-white h-12 top-0 left-0 flex justify-between md:justify-start fixed items-center w-full z-50 shadow">
    <div class="flex text-[#287196] mx-2 md:mx-0">
      <button class="md:hidden" >
        <IconMenu class="stroke-2"/>
      </button>
      <div class=" md:mx-12 h-10 w-32 mt-1">
        <img :src="Logo" class="w-full h-full">
      </div>
    </div>
    <div class="rounded-full border md:hidden border-[#287196] overflow-hidden w-8 mx-4 ">
      <img :src="chat" class="w-full h-full">
    </div>
  </nav>
</template>
<script setup>
import chat from '../assets/chat.png'
import  Logo from '../assets/logo.jpg'
import IconMenu from './icons/IconMenu.vue'

</script>

