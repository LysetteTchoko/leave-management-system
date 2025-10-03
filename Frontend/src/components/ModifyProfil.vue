<template>
  <div v-if="showModif" class="fixed inset-0 flex items-center justify-center z-50">
    <div @click="toggleModify" class="fixed inset-0 bg-black bg-opacity-50"></div>
    <div class="bg-white shadow-lg relative rounded-lg w-5/6 max-w-md z-50 p-6">
      <button @click="toggleModify" class="absolute top-2 right-2 text-red-500 text-4xl font-bold focus:outline-none">
        &times;
      </button>
      <h3 class="font-semibold text-2xl text-[#287196] text-center">Modifier les informations</h3>
      <Error class="text-red-500 p-2 bg-red-200 mt-2 text-center" :error="error" v-if="error"/>
      <form class="flex flex-col gap-4" @submit.prevent="handleUpdate">
        <input class="p-2 mt-4 rounded-xl border-gray-300 bg-gray-200 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]"
          type="text"  placeholder="Nom"  v-model="username">
        <input class="p-2 mt-4 rounded-xl border-gray-300 bg-gray-200 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]"
          type="text"  placeholder="Email"  v-model="email">
        <button class="bg-[#287196] p-2 mt-2 text-white rounded-xl font-bold">Enregistrer</button>
      </form>
    </div>
  </div>
</template>

<script setup>

import { ref,onMounted } from 'vue'
import axios from "axios";
import Error from './Error.vue';

const error = ref('');
const username = ref('')
const email = ref('')

const props = defineProps({
  showModif: {
    type: Boolean,
    required: true
  },
  toggleModify: {
    type: Function,
    required: true
  }
})
onMounted(( )=> {
  const user = localStorage.getItem('user');
  const userData = JSON.parse(user);
  username.value = userData.username;
  email.value = userData.email
})

const handleUpdate = async () => 
{
  console.log('Sending update with:', {
  username: username.value,
  email: email.value
});
  try
  {
    const response = await axios.put('updateUser', {
      username: username.value,
      email: email.value 
    });

    

    props.toggleModify();
    console.log(response);
  } catch (e) {
    error.value ='Entrez des informations ';
  }
}

</script>
