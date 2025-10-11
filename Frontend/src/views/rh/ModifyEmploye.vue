<template>
    <div v-if="showEmploye" class="fixed inset-0 flex items-center justify-center z-50">
        <div @click="toggleEmploye" class="fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white shadow-lg relative rounded-lg w-5/6 max-w-md z-50 p-6">
            <button @click="toggleEmploye" class="absolute top-2 right-2 text-gray-500 text-4xl font-bold focus:outline-none">
                &times;
            </button>
            <h3 class="font-semibold text-2xl text-[#287196] text-center mb-4">Modifier Un Employé</h3>
            <div class="p-2 text-xl text-white mb-2 text-center bg-green-400" v-if="result">
                {{ result }}
            </div>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="handleUpdateEmploye(idEmploye)">
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Matricule</label>
                    <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                    type="text" v-model="matricule">
                </div>
                
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Nom</label>
                    <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                    type="text" v-model="nom">
                </div>
                
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Prénom</label>
                    <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                    type="text" v-model="prenom">
                </div>
                
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input class="p-2 rounded-xl bg-gray-200 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]"
                    type="email" v-model="email">
                </div>
                
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Poste</label>
                    <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                    type="text" v-model="poste">
                </div>
                
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Téléphone</label>
                    <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                    type="tel" v-model="telephone">
                </div>
                
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Département</label>
                    <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                    type="text" v-model="departement">
                </div>
                
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Date d'embauche</label>
                    <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                    type="date" v-model="date_embauche">
                </div>
                
                <div class="flex flex-col">
                    <label class="text-sm font-semibold text-gray-700 mb-1">Statut</label>
                    <input class="p-2 text-black rounded-xl bg-gray-200 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                    type="text" v-model="statut">
                </div>
                
                <button class="bg-[#287196] p-2 mt-2 text-white rounded-xl font-bold md:col-span-2">Enregistrer</button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref , onMounted, watch } from 'vue'
import axios from 'axios'

const matricule = ref('')
const nom = ref('')
const prenom = ref('')
const email = ref('')
const poste = ref('')
const telephone = ref('')
const departement = ref('')
const date_embauche = ref('')
const statut = ref('')
const result = ref('')

const props = defineProps({
  showEmploye: {
    type: Boolean,
    required: true
  },
  toggleEmploye: {
    type: Function,
    required: true
  },
  idEmploye: {
    type: Number,
    required: true
  }
})

const loadEmploye = async (id) => {
  try {
    const response = await axios.get(`getEmployeId/${id}`);
    const EmployeData = response.data.data;

    matricule.value = EmployeData.matricule;
    nom.value = EmployeData.nom;
    prenom.value = EmployeData.prenom;
    email.value = EmployeData.email;
    poste.value = EmployeData.poste;
    telephone.value = EmployeData.telephone;
    departement.value = EmployeData.departement;
    date_embauche.value = EmployeData.date_embauche;
    statut.value = EmployeData.statut;
  } catch (error) {
    console.error("Erreur chargement Employe :", error);
  }
}

const handleUpdateEmploye = async (id) => {
  try {
    const params = {};
    if (matricule.value) params.matricule = matricule.value;
    if (nom.value) params.nom = nom.value;
    if (prenom.value) params.prenom = prenom.value;
    if (email.value) params.email = email.value;
    if (poste.value) params.poste = poste.value;
    if (telephone.value) params.telephone = telephone.value;
    if (departement.value) params.departement = departement.value;
    if (date_embauche.value) params.date_embauche = date_embauche.value;
    if (statut.value) params.status = statut.value;

    if (Object.keys(params).length > 0) {
      await axios.put(`updateEmploye/${id}`, params);
      result.value = 'Employé modifié avec succès';
      setTimeout(() => {
        props.toggleEmploye();
      }, 1500);
    }
  } catch (error) {
    console.error("Erreur :",  error);
  }
}

watch(() => props.idEmploye , (newId) => {
  if (newId) loadEmploye(newId)
})

</script>
