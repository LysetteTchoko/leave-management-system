<template>
  <div class="min-h-screen  grid grid-cols-12 w-full text-black relative">
    <Nav/>
    <div class="w-full grid grid-cols-12 col-span-12 text-black grow mt-12">
      <Sidebar />
      <main class="overflow-y-auto bg-gray-200 flex flex-col col-start-2 col-span-11 md:col-start-3 md:col-span-11" >
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>


<script setup>
import Sidebar from '../../components/Sidebar.vue'
import Nav from '../../components/Nav.vue'

</script>
