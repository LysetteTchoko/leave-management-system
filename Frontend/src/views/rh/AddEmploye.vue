<template>
    <div v-if="showAddEmploye" class="fixed inset-0 flex items-center justify-center z-50">
        <div @click="toggleAddEmploye" class="fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white shadow-lg relative rounded-lg w-5/6 max-w-md z-50 p-6"  >
            <button @click="toggleAddEmploye" class="absolute top-2 right-2 text-gray-500 text-4xl font-bold focus:outline-none">
                &times;
            </button>
            <h3 class="font-semibold text-2xl text-[#287196] text-center mb-4">Ajouter Un Employé</h3>
            <div class="p-2 text-xl text-white font-semibold text-center mb-4" 
                 :class="result === 'Utilisateur ajouté avec succès' ? 'bg-green-400' : 'bg-red-400'" 
                 v-if="result">
                {{  result }}
            </div>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="handleAddEmploye">
                <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="text" placeholder="Matricule" required v-model="data.matricule">
                
                <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="text" placeholder="Nom" required v-model="data.nom" maxlength="50" minlength="2">
                
                <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="text" placeholder="Prénom" required v-model="data.prenom" maxlength="50" minlength="2">
                
                <input class="p-2 rounded-xl bg-gray-200 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]"
                type="email" placeholder="Email" required v-model="data.email" 
                title="Veuillez entrer une adresse email valide">
                
                <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="text" placeholder="Poste" required v-model="data.poste">
                
                <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="tel" placeholder="Téléphone" required v-model="data.telephone" pattern="[0-9]{9,12}" maxlength="12" 
                title="Veuillez entrer un numéro de téléphone valide (9-12 chiffres uniquement)">
                
                <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="text" placeholder="Département" required v-model="data.departement">
                
                <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="date" placeholder="Date d'embauche" required v-model="data.date_embauche">
                <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="text" placeholder="Statut" required v-model="data.statut" maxlength="30">
                <button class="bg-[#287196] p-2 mt-2 text-white rounded-xl font-bold md:col-span-2">Enregistrer</button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref,onMounted } from 'vue'
import axios from 'axios'

const result = ref('')
const data = reactive({
  matricule: '',
  nom: '',
  prenom: '',
  email: '',
  poste: '',
  telephone: '',
  departement: '',
  date_embauche: '',
  statut: ''
})

const resetData = () => {
  Object.assign(data, {
    matricule: '',
    nom: '',
    prenom: '',
    email: '',
    poste: '',
    telephone: '',
    departement: '',
    date_embauche: '',
    statut: ''
  })
}

const props = defineProps({
  showAddEmploye: {
    type: Boolean,
    required: true
  },
  toggleAddEmploye: {
    type: Function,
    required: true
  }
})

const handleAddEmploye = async () => {
  try {
    const response = await axios.post('createEmploye', data)
    result.value = 'Utilisateur ajouté avec succès'
    resetData()
    setTimeout(() => {
      result.value = ''
    }, 2500)
    setTimeout(() => {
        props.toggleAddEmploye();
      }, 2500);
  } catch (e) {
    console.error('Erreur lors de l\'ajout:', e.response?.data)
    Success.value = false
    if (e.response?.data?.errors) {
      // Afficher les erreurs de validation
      const errors = Object.values(e.response.data.errors).flat()
      result.value = errors.join(', ')
    } else if (e.response?.data?.message) {
      result.value = e.response.data.message
    } else {
      result.value = 'Erreur lors de l\'ajout de l\'employé'
    }
  } 
}
</script>
