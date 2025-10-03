<template>
  <div v-if="showUser" class="fixed inset-0 flex items-center justify-center z-50">
    <div @click="toggleUser" class="fixed inset-0 bg-black bg-opacity-50"></div>
    <div class="bg-white shadow-lg relative rounded-lg w-5/6 max-w-md z-50 p-6">
      <button @click="toggleUser" class="absolute top-2 right-2 text-gray-500 text-4xl font-bold focus:outline-none">
        &times;
      </button>
      <h3 class="font-semibold text-2xl text-[#287196] text-center mb-4">Modifier Un Utilisateur</h3>
      <div class="p-2 text-xl text-white mb-2 text-center bg-green-400" v-if="result">
        {{  result }}
      </div>
      <form  class="flex flex-col gap-4" @submit.prevent="handleUpdateUser(idUser)" >
        <input class="p-2 text-black mb-4  rounded bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
          type="text" placeholder="Nom d'utilisateur"  v-model="username">
        <input class="p-2 mb-4 rounded bg-gray-200 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]"
          type="text" placeholder="Email"  v-model="email">
        <div class="flex w-full justify-between text-center">
          <label class="font-semibold my-auto">Role :</label>
          <select v-model="role" class="w-[350px] border p-2 rounded bg-gray-100" >
            <option v-for="(role, index) in roles" :key="index" :value="role">
              {{ role }}
            </option>
          </select>
        </div>
        <button  class="bg-[#287196] p-2 mt-2 text-white rounded-xl font-bold ">Enregistrer</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref , onMounted, watch } from 'vue'
import axios from 'axios'

const roles = ref([])
const role = ref('')
const username = ref('')
const email = ref('')
const result = ref('')

const props = defineProps({
  showUser: {
    type: Boolean,
    required: true
  },
  toggleUser: {
    type: Function,
    required: true
  },
  idUser: {
    type: Number,
    required: true
  }
})

const getRole = async () => {
  try {
    const response = await axios.get('getRoles')
    roles.value = response.data.roles

  } catch (err) {
    console.error(err);
  }
}

const loadUser = async (id) => {
  try {
    const response = await axios.get(`getUserId/${id}`);
    const userData = response.data.data;

    username.value = userData.username;
    email.value = userData.email;
    role.value = userData.role;
  } catch (error) {
    console.error("Erreur chargement user :", error);
  }
}

const handleUpdateUser = async (id) => {
  try {
    if (role.value !== '') {
      await axios.put(`updateRole/${id}`, {
        role: role.value
      });
      result.value = 'Role modifié avec succès'
    }
    const params = {};
    if (username.value) params.username = username.value;
    if (email.value) params.email = email.value;

    if (Object.keys(params).length > 0) {
      await axios.put(`updateUsers/${id}`, params);
      result.value = 'Utilisateur modifié avec succès';
    }

    username.value = '';
    email.value = '';
    role.value = '';
    
  } catch (error) {
    console.error("Erreur :",  error);
  }
}
watch(() => props.idUser , (newId) => {
  if (newId) loadUser(newId)
})
onMounted(() => {
  getRole()
})
</script>
