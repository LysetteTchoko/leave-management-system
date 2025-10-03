<template>
  <div v-if="showConge" class="fixed inset-0 flex items-center justify-center z-50">
    <div @click="toggleConge" class="fixed inset-0 bg-black bg-opacity-50"></div>
    <div class="bg-white shadow-lg relative rounded-lg w-5/6 max-w-md z-50 p-6"  >
      <button @click="toggleConge" class="absolute top-2 right-2 text-red-500 text-4xl font-bold focus:outline-none">
        &times;
      </button>
      <h3 class="font-semibold text-2xl text-[#287196] text-center">Demande de conge</h3>
      <Error class="text-red-500 p-2 bg-red-200 mt-2 text-center" :error="error" v-if="error"/>
      <form @submit.prevent="handleDemandConge" class="flex flex-col gap-2">
        <label for="date_debut">Date Debut :</label>
        <input type="date" v-model="data.date_debut" class="border p-2 rounded bg-gray-100" required />
        <label for="date_fin">Date Fin :</label>
        <input type="date" v-model="data.date_fin" class="border p-2 rounded bg-gray-100" required />
        <label for="type">Type :</label>
        <select v-model="data.type" class="border p-2 rounded bg-gray-100" required>
          <option v-for="(typeConge, index) in typeConges" :key="index" :value="typeConge">
            {{ typeConge }}
          </option>
        </select>
        <button class="bg-[#287196] text-white p-2 rounded">Envoyer</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios';
import Error from '../../components/Error.vue'

const props = defineProps({
  showConge: {
    type: Boolean,
    required: true
  },
  toggleConge: {
    type: Function,
    required: true
  }
})

// Emit pour informer le parent
const emit = defineEmits(['success'])
const error = ref('')
const typeConges = ref([])

const data = reactive({
  date_debut: '',
  date_fin: '',
  type: ''
})

const getTypeConges = async () => {
  try 
  {
    const response = await axios.get('getType')
    typeConges.value = response.data.Typeconges
    console.log(response)
  } catch (err) {
    console.error("Erreur de hargement des types de congés", err)
  }
}

const handleDemandConge = async () => {

  const fin = new Date(data.date_fin)
  const debut = new Date(data.date_debut)
  const today = new Date()

  if (fin < debut) {
    error.value = "La date de fin doit etre superieur a la date de debut"
    return
  }
  if (debut < today) {
    error.value = "La date de debut doit etre superieur a la date d'aujourd'hui"
    return
  }

  try 
  {
    await axios.post('addConge',data)
    props.toggleConge()
    emit('success', 'Demande envoyée avec succès')

  } catch (err) {
    console.error("Erreur lors de l'envoi de la demande", err)
  }
}

onMounted(() => {
  getTypeConges()
})

</script>