<template>
    <div class="mx-auto flex flex-col w-full p-8 gap-4" >
        <h3 class="md:text-4xl text-2xl font-semibold text-[#287196] mx-auto mb-4 bg-white shadow-lg p-2">Liste des Conges</h3>
        <div class="p-2 text-xl text-white font-semibold text-center " v-if="result" :class="{'bg-green-400': result === 'Conge approuvé','bg-red-400': result === 'Conge rejeté'}">
            {{  result }}
        </div>
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
                            <th class="uppercase font-semibold px-4 py-2">Nom</th>
                            <th class="uppercase font-semibold ">Prenom</th>
                            <th class="uppercase font-semibold">Date debut</th>
                            <th class="uppercase font-semibold">Date fin</th>
                            <th class="uppercase font-semibold">Type</th>
                            <th class="uppercase font-semibold">Statut</th>
                            <th class="uppercase font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="conge in conges" :key="conge.id_conge" class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2 text-center">{{ conge.employer.nom }}</td>
                            <td class="px-4 py-2 text-center">{{ conge.employer.prenom }}</td>
                            <td class="px-4 py-2 text-center">{{ conge.date_debut }}</td>
                            <td class="px-4 py-2 text-center">{{ conge.date_fin }}</td>
                            <td class="px-4 py-2 text-center">{{ conge.type }}</td>
                            <td class="text-center">
                                <span class="px-3 py-1 my-1 rounded text-white" :class="{'bg-green-500': conge.statut === 'approuve','bg-red-500': conge.statut === 'rejete','bg-yellow-400': conge.statut === 'en_attente'}">
                                    {{ conge.statut }}
                                </span>
                            </td>
                            <td class="place-items-center  text-center">
                                <div class="flex gap-2">
                                    <button @click="rejeteConge(conge.id_conge)" :disabled="conge.statut != 'en_attente'" class="px-2 py-1 my-1 bg-red-500 text-white rounded hover:bg-red-600 disabled:opacity-50" >
                                        Rejeter
                                    </button>
                                    <button @click="accepteConge(conge.id_conge)" :disabled="conge.statut != 'en_attente'" class="px-2 py-1 my-1 bg-green-500 text-white rounded hover:bg-green-600disabled:opacity-50">
                                        Accepter
                                    </button>
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
                Aucun congé trouvé 
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted , computed} from "vue"
import axios from "axios"

    const conges = ref([])
    const pagination = ref({})
    let result = ref('')

    const date_debut = ref('')
    const date_fin = ref('')
    const statut = ref('')

    const loadConges = async (page = 1) => {
        try {
            const response = await axios.get("getConge", {
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

    const accepteConge = async (id) => {
        try {
            const response = await axios.put(`valideConge/${id}`, {
                decision_comite: 'approuve'
            });
            result.value = 'Conge approuvé'
            await loadConges();
        } catch (error) {
            console.error("Erreur approuvé conge :", error.response?.data || error);
        }
    }

    const rejeteConge = async (id) => {
        try {
            const response = await axios.put(`valideConge/${id}`,
            {
                decision_comite: 'rejete'
            });
            result.value = 'Conge rejeté'
            await loadConges();
        } catch (error) {
            console.error("Erreur rejet conge :", error.response?.data || error);
        }
    }
    setTimeout(() => {
        result.value = ''
    }, 3000)

    onMounted(() => {
        loadConges()
    })
</script>