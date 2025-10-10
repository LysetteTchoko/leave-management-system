<template>
  <div class="mx-auto flex flex-col w-full p-8 gap-4" >
    <h3 class="text-4xl text-2xl text-[#287196] mx-auto mb-4">Tableau De Bord</h3>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="flex flex-col gap-4 shadow p-4 bg-white col-span-1">
        <p class="text-2xl text-xl mb-4" >{{ user.username }}</p>
        <hr class="border-2 border-[#287196]">
        <div class="flex gap-4 text-xl">
          <p>Status du jour</p><br/>
          <p class="text-red-500 font-semibold" >{{statut1}}</p>
        </div>
      </div>
      <div class="shadow-lg bg-white p-4 col-span-2">
        <h3 class="sm:text-2xl text-xl mb-2">Statistiques Personnelles</h3>
        <div class="grid md:grid-cols-3 grid-cols-1 gap-4">
          <div class="border-2 p-4  flex flex-col gap-2 border-green-500">
            <h3 class="text-xl">Presence ce mois</h3>
            <p class="text-xl text-green-500">{{ stat.presence_mois }}</p>
          </div>
          <div class="border-2 p-4  flex flex-col gap-2 border-red-500">
            <h3 class="text-xl">Absences ce mois</h3>
            <p class="text-xl text-red-500">{{ stat.absences_mois }}</p>
          </div>
          <div class="border-2 p-4  flex flex-col gap-2 border-yellow-500">
            <h3 class="text-xl">Retard ce mois</h3>
            <p class="text-xl text-yellow-500">{{ stat.retards_mois }}</p>
          </div>
        </div>  
      </div>
    </div>
    <div class="border shadow-lg bg-white p-4">
      <div class="flex-cols lg:flex lg:justify-between mb-4">
        <h3 class="text-2xl mb-2 text-[#287196]">Historique</h3>
        <form class="flex flex-wrap lg:flex-nowrap gap-2 items-center mb-4" @submit.prevent="filter">
                <div class="flex items-center gap-2">
                    <span class="font-semibold">Status</span>
                    <select v-model="statut" class="border-2 p-1 border-[#287196] text-sm md:text-base" >
                      <option value="">Libre</option>
                      <option v-for="(statut, index) in statuts" :key="index" :value="statut">
                        {{ statut }}
                      </option>
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
      </div>
      <div class="w-full overflow-x-auto" v-if="userPresence.length > 0">
        <table class="table-auto w-full border-collapse border-2 rounded">
          <thead class="bg-gray-200 py-4 ">
            <tr>
              <th class="uppercase font-semibold py-2">Date</th>
              <th class="uppercase font-semibold">Arrivée</th>
              <th class="uppercase font-semibold">Depart</th>
              <th class="uppercase font-semibold">Statut</th>
              <th class="uppercase font-semibold">Duree du Retard</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for = "presence in userPresence" :key="userPresence.id_presence" class="border-b hover:bg-gray-100">
              <td class="px-4 py-2 text-center ">{{ presence.date }}</td>
              <td class="px-4 py-2 text-center">{{ presence.heure_arrivee }}</td>
              <td class="px-4 py-2 text-center">{{ presence.heure_depart?? '--' }}</td>
              <td class="px-4 py-2 text-center">{{presence.statut}}</td>
              <td class="px-4 py-2 text-center">{{ presence.duree_retard ?? '--' }}</td>
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
        Aucun resultat trouvé 
      </div>
    </div>
  </div>
</template>

<script setup>
  import axios from 'axios'
  import { onMounted, ref, computed} from 'vue'

  const user = ref({});
  const userPresence = ref([]);
  const statut = ref('');
  const statut1 = ref('');
  const statuts = ref('');
  const stat = ref({
    retards_mois: 0,
    absences_mois: 0,
    presence_mois: 0
  });
  const date_debut = ref('');
  const date_fin = ref('');
  const conges = ref([])
  const pagination = ref({})
  let result = ref('')

  const getStatut = async () => {
    try {
      const response = await axios.get('getStatut')
      statuts.value = response.data.StatutPresence
      console.log('statut',response)
    } catch (err) {
      console.error(err);
    }
}

    const loadPresence = async (page = 1) => {
        try {
            const response = await axios.get("getUserPresence", {
                params: {
                    page,
                    statut: statut.value,
                    date_debut: date_debut.value,
                    date_fin: date_fin.value,
                }
            })
             userPresence.value = response.data.data.data 
            pagination.value = response.data.data

            const users = await axios.get('getUser');
            statut1.value = users.data.statut;
            const statUser = await axios.get('statUser');
            stat.value = statUser.data.data;
           
        } catch (error) {
            console.error("Erreur chargement Presence", error)
        } 
    }

    const currentPage = computed(() => pagination.value?.current_page || 1)
    const lastPage = computed(() => pagination.value?.last_page || 1) 

    const loadInitialData = async () => {
      try {
        const users = await axios.get('getUser');
        statut1.value = users.data.statut;

        const statUser = await axios.get('statUser');
        stat.value = statUser.data.data;
        } catch (error) {
            console.error("Erreur chargement données initiales", error)
        }
    }
    const filter = () => {
        loadPresence(1) // toujours revenir a la premiere page
    }

    const prevPage = () => {
        if (pagination.value.current_page > 1) {
            loadPresence(pagination.value.current_page - 1)
        }
    }

    const nextPage = () => {
        if (pagination.value.current_page < pagination.value.last_page) {
            loadPresence(pagination.value.current_page + 1)
        }
    }
  onMounted(() => {
        loadPresence()
        getStatut()
        const storedUser = localStorage.getItem('user');
        user.value = storedUser ? JSON.parse(storedUser) : null;
    })
</script>