<template>
    <div class="mx-auto flex flex-col w-full p-8 gap-4" >
        <h3 class="md:text-4xl text-2xl font-semibold text-[#287196] mx-auto mb-4 bg-white shadow-lg p-2">Liste des Conges</h3>
        <div class="p-2 text-xl text-white font-semibold text-center " v-if="result" :class="{'bg-green-400': result === 'Conge approuvé','bg-red-400': result === 'Conge rejeté'}">
            {{  result }}
        </div>
        <button class="px-8 py-2 bg-[#287196] text-white text-xl font-semibold  md:w-1/2 lg:w-1/3 " @click="toggleAddConge">
            Demander un conge
        </button>
        <div class="border shadow-lg bg-white p-4">
            <h3 class="text-2xl mb-2 text-center"></h3>
            <form class="flex flex-wrap lg:flex-nowrap gap-2 items-center mb-4" @submit.prevent="filter">
                <div class="flex items-center gap-2">
                    <span class="font-semibold">Status</span>
                    <select class="border-2 p-1 border-[#287196] text-sm md:text-base" v-model="statut">
                        <option value="approuve">Approuvé</option>
                        <option value="rejete">Rejeté</option>
                        <option value="en_attente">En Attente</option>
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
            <div class="w-full overflow-x-auto" v-if="conges.length > 0">
                <table class="table-auto w-full border-collapse border-2 rounded">
                    <thead class="bg-gray-200 ">
                        <tr>
                            <th class="uppercase font-semibold py-2">Date debut</th>
                            <th class="uppercase font-semibold">Date fin</th>
                            <th class="uppercase font-semibold">Type</th>
                            <th class="uppercase font-semibold">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="conge in conges" :key="conge.id_conge" class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2 text-center">{{ conge.date_debut }}</td>
                            <td class="px-4 py-2 text-center">{{ conge.date_fin }}</td>
                            <td class="px-4 py-2 text-center">{{ conge.type }}</td>
                            <td class="text-center">
                                <span class="px-3 py-1 my-1 rounded text-white font-semibold" :class="{'bg-green-500': conge.statut === 'approuve','bg-red-500': conge.statut === 'rejete','bg-yellow-400': conge.statut === 'en_attente'}">
                                    {{ conge.statut }}
                                </span>
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
                Aucun congé trouvé 
            </div>
        </div>
        <AddConge :showConge="showConge" :toggleConge=" toggleAddConge" @success="handleSuccess"/>
    </div>
</template>

<script setup>
import { ref, onMounted , computed} from "vue"
import AddConge from "./AddConge.vue"
import axios from "axios"

    const conges = ref([])
    const pagination = ref({})
    let result = ref('')
    const showConge = ref(false)

    const date_debut = ref('')
    const date_fin = ref('')
    const statut = ref('')

    const toggleAddConge = () => {
        showConge.value = !showConge.value
    }

    const loadConges = async (page = 1) => {
        try {
            const response = await axios.get("getUserConge", {
                params: {
                    page,
                    statut: statut.value,
                    date_debut: date_debut.value,
                    date_fin: date_fin.value,
                }
            })
            conges.value = response.data.data.data 
            pagination.value = response.data.data
        } catch (error) {
            console.error("Erreur chargement conges", error)
        } 
    }

    const currentPage = computed(() => pagination.value?.current_page || 1)
    const lastPage = computed(() => pagination.value?.last_page || 1) 


    const filter = () => {
        loadConges(1) // toujours revenir a la premiere page
    }

    const prevPage = () => {
        if (pagination.value.current_page > 1) {
            loadConges(pagination.value.current_page - 1)
        }
    }

    const nextPage = () => {
        if (pagination.value.current_page < pagination.value.last_page) {
            loadConges(pagination.value.current_page + 1)
        }
    }

    const handleSuccess = (message) => {
        result.value = message
        loadConges() 
        setTimeout(() => {
            result.value = ''
        }, 3000)
    }

    onMounted(() => {
        loadConges()
    })
</script>