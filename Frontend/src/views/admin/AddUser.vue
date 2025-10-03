<template>
  <div v-if="showAddUser" class="fixed inset-0 flex items-center justify-center z-50">
    <div @click="toggleAddUser" class="fixed inset-0 bg-black bg-opacity-50"></div>
    <div class="bg-white shadow-lg relative rounded-lg w-5/6 max-w-md z-50 p-6"  >
      <button @click="toggleAddUser" class="absolute top-2 right-2 text-gray-500 text-4xl font-bold focus:outline-none">
        &times;
      </button>
      <h3 class="font-semibold text-2xl text-[#287196] text-center">Ajouter Un Utilisateur</h3>
      <div class="p-2 text-xl text-white font-semibold text-center bg-green-400" v-if="result" >
        {{  result }}
      </div>
      <form class="flex flex-col gap-4" @submit.prevent="handleAddUser" >
        <input class="p-2 text-black mt-4 border-gray-300 rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
          type="text" placeholder="Nom d'utilisateur" required v-model="data.username">
        <input class="p-2 mt-4 rounded-xl border-gray-300 bg-gray-200 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]"
          type="text"  placeholder="Email" required v-model="data.email">
        <input class="p-2 text-black mt-4 border-gray-300 rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
          type="password" placeholder="Mot de passe" required v-model="data.password">
        <div class="flex w-full justify-between text-center">
          <label class="font-semibold my-auto">Role :</label>
          <select v-model="data.role" class="w-[350px] border p-2 rounded bg-gray-100" required>
            <option v-for="(role, index) in roles" :key="index" :value="role">
              {{ role }}
            </option>
          </select>
        </div>
        <button class="bg-[#287196] p-2 mt-2 text-white rounded-xl font-bold ">Enregistrer</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref,onMounted } from 'vue'
import axios from 'axios'

const roles = ref([]);
const result = ref('')
const data = reactive({
  username: '',
  email: '',
  password: '',
  role: ''
})

const resetData = () => {
  Object.assign(data, {
    username: '',
    email: '',
    password: '',
    role: ''
  })
}

const props = defineProps({
  showAddUser: {
    type: Boolean,
    required: true
  },
  toggleAddUser: {
    type: Function,
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

onMounted(() => {
  getRole()
})

const handleAddUser = async () => {
  try {
    await axios.post('registerUser', data)
    result.value = 'Utilisateur ajouté avec succès'
    resetData()
  } catch (e) {
    result.value = 'L\'email de l\'utilisateur doit existe';
  } 
}
</script>
