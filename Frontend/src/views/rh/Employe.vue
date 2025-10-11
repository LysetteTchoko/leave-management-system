<template>
    <div class="mx-auto flex flex-col w-full p-2 gap-4" >
        <h3 class="md:text-4xl text-2xl font-semibold text-[#287196] mx-auto mb-4 bg-white shadow-lg p-2">Liste des Employés</h3>
        
        <div class="flex flex-col md:flex-row gap-4 items-center">
            <button class="px-8 py-2 bg-[#287196] text-white text-xl font-semibold w-full md:w-auto" @click="toggleAddEmploye">
                Ajouter un Employé
            </button>
            <form @submit.prevent="handleSearch" class="flex gap-2 w-full md:w-auto">
                <input v-model="search" @input="handleSearch" type="text" placeholder="Rechercher par nom ou prénom..." 
                    class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#287196] flex-1 md:w-80"/>
                <button  v-if="search" @click.prevent="clearSearch" type="button" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                  X
                </button>
            </form>
        </div>
        <div class="p-2 text-xl text-white mb-2 text-center bg-green-400" v-if="result">
            {{  result }}
        </div>
        <div class="border shadow-lg bg-white p-2">
            <div v-if="employes.length > 0" class="w-full overflow-x-auto">
                <table class="table-auto w-full border-collapse border-2 rounded">
                <thead class="bg-gray-200 py-4 ">
                    <tr>
                    <th class="uppercase font-semibold py-2">Matricule</th>
                    <th class="uppercase font-semibold">Nom</th>
                    <th class="uppercase font-semibold">Prénom</th>
                    <th class="uppercase font-semibold">Email</th>
                    <th class="uppercase font-semibold">Poste</th>
                    <th class="uppercase font-semibold">Téléphone</th>
                    <th class="uppercase font-semibold">Département</th>
                    <th class="uppercase font-semibold">Date D'Embauche</th>
                    <th class="uppercase font-semibold">Statut</th>
                    <th class="uppercase font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="employe in employes" :key="employe.id_employer" class="border-b hover:bg-gray-100">
                    <td class="px-2 py-2 text-center">{{ employe.matricule }}</td>
                    <td class="px-2 py-2 text-center">{{ employe.nom }}</td>
                    <td class="px-2 py-2 text-center">{{ employe.prenom }}</td>
                    <td class="px-2 py-2 text-center">{{ employe.email }}</td>
                    <td class="px-2 py-2 text-center">{{ employe.poste }}</td>
                    <td class="px-2 py-2 text-center">{{ employe.telephone }}</td>
                    <td class="px-2 py-2 text-center">{{ employe.departement }}</td>
                    <td class="px-2 py-2 text-center">{{ employe.date_embauche }}</td>
                    <td class="px-2 py-2 text-center">{{ employe.statut }}</td>
                    <td class="place-items-center text-center ">
                        <div class="flex">
                        <button class="bg-yellow-500 px-2 py-2  my-1 text-black shadow font-bold rounded-lg" @click="toggleEmploye(employe.id_employer)">Modifier</button>
                        <button class="bg-red-500 ml-2 my-1 px-2 py-2 text-white shadow font-bold rounded-lg" @click="deleteEmploye(employe.id_employer)">Supprimer</button>
                        </div>
                    </td>
                    </tr>
                </tbody>
                </table>
                <div class="flex items-center mt-4 space-x-4 justify-end">
                <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50">
                    Précédent
                </button>
                <span>Page {{ currentPage }} / {{ lastPage }}</span>
                <button @click="nextPage" :disabled="currentPage === lastPage" class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50">
                    Suivant
                </button>
                </div>
            </div>
            <div v-else class="text-center text-gray-500 py-8 text-2xl">
                Aucun Employé trouvé 
            </div>
        </div>
        <ModifyEmploye :showEmploye="showEmploye" :toggleEmploye="toggleEmploye" :idEmploye="idEmploye"/>
        <AddEmploye :showAddEmploye="showAddEmploye" :toggleAddEmploye="toggleAddEmploye"/>
    </div>
</template>

<script setup>
  import ModifyEmploye from "./ModifyEmploye.vue"
  import AddEmploye from "./AddEmploye.vue"
  import { ref, onMounted , computed} from "vue"
  import axios from "axios"

  const showEmploye = ref(false)
  const showAddEmploye = ref(false)
  const idEmploye = ref(0)
  const employes = ref([])
  const pagination = ref({})
  let result = ref('')
  const search = ref('')
  let searchTimeout = null

  const toggleAddEmploye = () => {
    loadEmployes(1)
    showAddEmploye.value = !showAddEmploye.value
  }
  
  const toggleEmploye = (id) => {
    idEmploye.value = id
    showEmploye.value = !showEmploye.value
    loadEmployes(1)
  }

  const loadEmployes = async (page = 1) => {
    try {
      const params = { page }
      
      // Si l'utilisateur a tape quelque chose on ajoute le filtre
      if (search.value) {
        params.nom = search.value
      }
      
      const response = await axios.get("viewEmployer", { params })
      employes.value = response.data.data.data 
      pagination.value = response.data.data
    } catch (error) {
      console.error("Erreur chargement employes", error)
    } 
  }

  const handleSearch = () => {
    // Annule le timer precedent s'il existe
    if (searchTimeout) clearTimeout(searchTimeout)
    
    // Cree un nouveau timer de 500ms
    searchTimeout = setTimeout(() => {
      loadEmployes(1) // Lance la recherche après 500ms sans activite
    }, 500)
  }

  // Efface la recherche et recharge tous les employés
  const clearSearch = () => {
    search.value = ''
    loadEmployes(1)
  }

  const currentPage = computed(() => pagination.value?.current_page || 1)
  const lastPage = computed(() => pagination.value?.last_page || 1) 

  const prevPage = () => {
    if (pagination.value.current_page > 1) {
      loadEmployes(pagination.value.current_page - 1)
    }
  }

  const nextPage = () => {
    if (pagination.value.current_page < pagination.value.last_page) {
      loadEmployes(pagination.value.current_page + 1)
    }
  }

  const deleteEmploye = async (id) => {
    try {
      const response = await axios.delete(`deleteEmploye/${id}`)
      result.value = 'Employé supprimé avec succès'
      setTimeout(() => {
        result.value = ''
      }, 2000);
      await loadEmployes();
    } catch (error) {
      console.error("Erreur :", error.response?.data || error);
    }
  }

  onMounted(() => {
    loadEmployes()
  })
</script>