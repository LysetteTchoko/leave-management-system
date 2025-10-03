<template>
  <div v-if="showMotifRetard" class="fixed inset-0 flex items-center justify-center z-50">
    <div @click="toggleRetard" class="fixed inset-0 bg-black bg-opacity-50"></div>
    <div class="bg-white shadow-lg relative rounded-lg w-5/6 max-w-md z-50 p-4"  >
      <button @click="toggleRetard" class="absolute top-2 right-2 text-red-500 text-4xl font-bold focus:outline-none">
        &times;
      </button>
      <div class="p-2 text-xl text-white mb-2 text-center bg-green-400" v-if="result">
        {{  result }}
      </div>
      <h3 class="font-semibold text-2xl text-[#287196] text-center">Motif du Retard</h3>
      <Error class="text-red-500 p-2 bg-red-200 my-2 text-center" :error="error" v-if="error"/>
      <form @submit.prevent="handleMotifRetard(idRetard)" class="flex flex-col gap-2">
        <label for="date_debut" class="font-semibold">Motif :</label>
        <textarea  col="30" class="p-2 my-2 rounded-xl bg-gray-200 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]" v-model="motif"></textarea>
        <button class="bg-[#287196] text-white p-2 rounded">Envoyer</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios';
import Error from '../../components/Error.vue'

const props = defineProps({
  showMotifRetard: {
    type: Boolean,
    required: true
  },
  toggleRetard: {
    type: Function,
    required: true
  },
  idRetard: {
    type: Number,
    required: true
  }
})

const error = ref('')
const motif = ref('')
const result = ref('')

const handleMotifRetard = async (id) => {
  try {
    await axios.put(`addMotifRetard/${id}`, {
      motif: motif.value
    });
    result.value = "Motif du Retard Enregistrer"
    
  } catch (error) {
    console.error("Erreur  retard :",  error);
  }
}

</script>