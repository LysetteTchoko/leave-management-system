<template>
  <div class="mx-auto flex flex-col w-full p-6 gap-4" >
    <h3 class="md:text-4xl text-2xl font-semibold text-[#287196] mx-auto mb-4 bg-white shadow-lg p-2">Liste des Utilisateurs</h3>
    <button class="px-8 py-2 bg-[#287196] text-white text-xl font-semibold  md:w-1/2 lg:w-1/3 " @click="toggleAddUser">
      Ajouter un Utilisateur
    </button>
    <div class="p-2 text-xl text-white mb-2 text-center bg-green-400" v-if="result">
      {{  result }}
    </div>
    <div class="border shadow-lg bg-white p-4">
      <div v-if="users.length > 0" class="w-full overflow-x-auto">
        <table class="table-auto w-full border-collapse border-2 rounded">
          <thead class="bg-gray-200 py-4 ">
            <tr>
              <th class="uppercase font-semibold py-2">Nom</th>
              <th class="uppercase font-semibold">Prenom</th>
              <th class="uppercase font-semibold">Nom d'utilisateur</th>
              <th class="uppercase font-semibold">Email</th>
              <th class="uppercase font-semibold">Role</th>
              <th class="uppercase font-semibold">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id_user" class="border-b hover:bg-gray-100">
              <td class="px-4 py-2 text-center">{{ user.employer.nom }}</td>
              <td class="px-4 py-2 text-center">{{ user.employer.prenom }}</td>
              <td class="px-4 py-2 text-center">{{ user.username }}</td>
              <td class="px-4 py-2 text-center">{{ user.email }}</td>
              <td class="px-4 py-2 text-center">{{ user.role }}</td>
              <td class="place-items-center text-center ">
                <div class="flex">
                  <button class="bg-yellow-500 px-4 py-2  my-1 text-black shadow font-bold rounded-lg" @click="toggleUser(user.id_user)">Modifier</button>
                  <button class="bg-red-500 ml-2 my-1 px-4 py-2 text-white shadow font-bold rounded-lg" @click="deleteUser(user.id_user)">Supprimer</button>
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
        Aucun utilisateur trouvé 
      </div>
    </div>
    <ModifyUser :showUser="showUser" :toggleUser="toggleUser" :idUser="idUser"/>
    <AddUser :showAddUser="showAddUser" :toggleAddUser="toggleAddUser"/>
  </div>
</template>

<script setup>
  import ModifyUser from "./ModifyUser.vue"
  import AddUser from "./AddUser.vue"
  import { ref, onMounted , computed} from "vue"
  import axios from "axios"

  const showUser = ref(false)
  const showAddUser = ref(false)
  const idUser = ref(0)
  const users = ref([])
  const pagination = ref({})
  let result = ref('')

  const toggleAddUser = () => {
    loadusers()
    showAddUser.value = !showAddUser.value
  }
  const toggleUser = (id) => {
    idUser.value = id
    console.log(idUser.value);
    showUser.value = !showUser.value
    loadusers()
  }

  const loadusers = async (page = 1) => {
    try {
      const response = await axios.get("viewUser", {
        params: {
          page
        }
      })
      users.value = response.data.data.data 
      pagination.value = response.data.data
    } catch (error) {
      console.error("Erreur chargement users", error)
    } 
  }

  const currentPage = computed(() => pagination.value?.current_page || 1)
  const lastPage = computed(() => pagination.value?.last_page || 1) 


  const filter = () => {
    loadusers(1) // toujours revenir a la premiere page
  }

  const prevPage = () => {
    if (pagination.value.current_page > 1) {
      loadusers(pagination.value.current_page - 1)
    }
  }

  const nextPage = () => {
    if (pagination.value.current_page < pagination.value.last_page) {
      loadusers(pagination.value.current_page + 1)
    }
  }

  const deleteUser = async (id) => {
    try {
      const response = await axios.delete(`deleteUser/${id}`)
      result.value = 'Utilisateur supprimé avec succès'
      await loadusers();
    } catch (error) {
      console.error("Erreur :", error.response?.data || error);
    }
  }

  onMounted(() => {
    loadusers()
  })
</script>