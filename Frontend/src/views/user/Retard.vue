<template>
    <div class="mx-auto flex flex-col w-full p-8 gap-4" >
        <h3 class="md:text-4xl text-2xl font-semibold text-[#287196] mx-auto mb-4 bg-white shadow-lg p-2">Liste des Retards</h3>
        <div class="p-2 text-xl text-white bg-green-400 font-semibold text-center " v-if="result">
            {{  result }}
        </div>
        <div class="border shadow-lg bg-white p-4">
            <h3 class="text-2xl mb-2 text-center"></h3>
            <form class="flex flex-wrap lg:flex-nowrap gap-2 items-center mb-4" @submit.prevent="filter">
                <div class="flex items-center gap-2">
                    <span class="font-semibold">Status</span>
                    <select class="border-2 p-1 border-[#287196] text-sm md:text-base" v-model="statut">
                        <option value="1">Valider</option>
                        <option value="0">En Attente</option>
                        <option value="">Libre</option>
                    </select>
                </div>
                <div class="flex flex-wrap md:flex-nowrap gap-2 items-center">
                    <span class="font-semibold">Du:</span>
                    <input type="date" class="px-2 py-1 text-sm md:text-base border-[#287196] border-2 rounded" v-model="date_debut">
                    <span class="font-semibold">Au</span>
                    <input type="date" class="px-2 py-1 text-sm md:text-base border-[#287196] border-2 rounded" v-model="date_fin">
                </div>
                <button class="bg-[#287196] text-white p-2 md:px-4 md:py-2 shadow font-bold rounded-lg w-full sm:w-auto">
                    Filtrer
                </button>
            </form>
            <div class="w-full overflow-x-auto" v-if="retards.length > 0">
                <table class="table-auto w-full border-collapse border-2 rounded">
                    <thead class="bg-gray-200 ">
                        <tr>
                            <th class="uppercase font-semibold">Date du Retard</th>
                            <th class="uppercase font-semibold">Heure d'arrivé</th>
                            <th class="uppercase font-semibold">Duree du retard</th>
                            <th class="uppercase font-semibold">Motif</th>
                            <th class="uppercase font-semibold">Statut</th>
                            <th class="uppercase font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="retard in retards" :key="retard.id_retard" class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2 text-center">{{ retard.date_retard }}</td>
                            <td class="px-4 py-2 text-center">{{ retard.heure_arrivee }}</td>
                            <td class="px-4 py-2 text-center">{{ retard.duree_retard }}</td>
                            <td class="px-4 py-2 text-center">{{ retard.motif }}</td>
                            <td class=" text-center">
                                <span v-if="retard.valider_par_comite == 1" class="px-3 py-1 my-1 rounded " >
                                    Validé
                                </span>
                                <span v-else class="px-3 py-1 my-1 rounded ">
                                    En Attente
                                </span>
                            </td>
                            <td class=" text-center">
                                <button @click="toggleRetard(retard.id_retard)" :disabled="retard.valider_par_comite == 1"
                                 class="px-2 py-1 my-1 bg-green-500 disabled:opacity-50 text-white rounded hover:bg-green-700" >
                                   Ajouter Motif
                                </button>
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
                Aucun retard trouvé 
            </div>
        </div>
        <AddMotifRetard :toggleRetard="toggleRetard" :showMotifRetard = "showMotifRetard" :idRetard="idRetard" />
    </div>
</template>

<script setup>
import { ref, onMounted , computed} from "vue"
import axios from "axios"
import AddMotifRetard from "./AddMotifRetard.vue"

    const retards = ref([])
    const pagination = ref({})
    let result = ref('')
    const showMotifRetard = ref(false)
    const idRetard = ref('')
    
    const toggleRetard = (id) => {
        idRetard.value = id
        showMotifRetard.value = !showMotifRetard.value
        loadRetards()
    }

    const date_debut = ref('')
    const date_fin = ref('')
    const statut = ref('')

    const loadRetards = async (page = 1) => {
        try {
            const response = await axios.get("getUserRetard", {
            params: {
                page,
                statut: statut.value,
                date_debut: date_debut.value,
                date_fin: date_fin.value,
            }
            })
            retards.value = response.data.data.data 
            pagination.value = response.data.data
            console.log(response);
            
        } catch (error) {
            console.error("Erreur chargement retards", error)
        } 
    }

    const currentPage = computed(() => pagination.value?.current_page || 1)
    const lastPage = computed(() => pagination.value?.last_page || 1) 

    const filter = () => {
        loadRetards(1) // toujours revenir a la premiere page
    }

    const prevPage = () => {
        if (pagination.value.current_page > 1) {
            loadRetards(pagination.value.current_page - 1)
        }
    }

    const nextPage = () => {
        if (pagination.value.current_page < pagination.value.last_page) {
            loadRetards(pagination.value.current_page + 1)
        }
    }

    onMounted(() => {
        loadRetards()
        setTimeout(() => {
            result.value = ''
        }, 3000)
    })
</script>