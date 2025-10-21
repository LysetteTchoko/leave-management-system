<template>
  <div class="mx-auto flex flex-col w-full p-8 gap-4" >
    <h3 class="text-4xl p-4 mb-4 shadow bg-white">Profil</h3>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="flex flex-col gap-4 shadow p-4 bg-white">
        <p class="md:text-2xl text-xl  mb-2" >Nom d'utilisateur : {{ user.username }}</p>
        <p class="md:text-2xl text-xl mb-2">Email : {{ user.email }}</p>
        <p class="md:text-2xl text-xl mb-2">Role : {{ user.role }}</p>
        <p class="md:text-2xl text-xl mb-2">Departement : {{ employer.departement }}</p>
        <hr class="border-1 border-[#287196]">
        <div class="w-full">
          <button @click="toggleModify" class="p-2 shadow rounded text-xl text-white bg-green-500">
            Modifier son Profile
          </button>
        </div>
      </div>
      <div class="relative shadow-lg bg-white p-4 flex items-center justify-center">
        <div class="relative">
          <div class="rounded-full bg-white border-2 border-[#287196] overflow-hidden w-32 h-32 md:w-64 md:h-64 flex items-center justify-center">
            <img :src="imageUrl" class="w-full h-full object-cover" >
          </div>
          <form @submit.prevent="uploadImage" class="absolute bottom-0 right-0 md:bottom-1 md:right-1">
            <input type="file" id="fileInput" class="hidden" @change="handleFile" accept="image/*"/>
            <label for="fileInput" class="bg-blue-600 hover:bg-blue-700 text-white p-2 md:p-3 rounded-full
              shadow-lg cursor-pointer flex items-center justify-center">
              <IconModify class="w-6 h-6 md:w-6 md:h-6"/>
            </label>
          </form>
        </div>
      </div>
    </div>
  </div>      
</template>

<script setup>
  import { ref,onMounted, computed } from "vue";
  import defaultUser from '@/assets/default-user.svg'
  import  ModifyProfil from "../../components/ModifyProfil.vue"
  import IconModify from "../../components/icons/IconModify.vue"
  import axios  from "axios";

  const showModif = ref(false);
  const user = ref({});
  const result = ref('');
  const employer = ref({});
  const imageFile = ref(null);

   const toggleModify = () => {
    showModif.value = !showModif.value
  }

  onMounted(async () => {
    const response = await axios.get('getUser');
    const storedUser = localStorage.getItem('user');
    user.value = storedUser ? JSON.parse(storedUser) : null;
    employer.value = response.data.employer || {};
  })

  const imageUrl = computed(() => {
    if (user.value.photo) {
      return `${import.meta.env.VITE_API_URL}/storage/userProfile/${user.value.photo}`;
    }
    return defaultUser; 
  });
 
  //recuperer l'image et le stoker dans imageFile
  function handleFile(event) {
    const file = event.target.files[0]
    if (file) {
      imageFile.value = file
      uploadImage()
    }
  }

  async function uploadImage() {
    if (!imageFile.value) {
      result.value = 'Veuillez sélectionner une image.'
      return
    }
    
    // Cree un objet FormData et ajouter le fichier.
    const formData = new FormData()
    formData.append('photo', imageFile.value)

    try {
      const response = await axios.post('updatePhoto', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      if (response.data.data) {
        // Update user data avec la nouvelle photo
        user.value = response.data.data
        localStorage.setItem('user', JSON.stringify(response.data.data))
        result.value = 'Photo mise à jour avec succès!'
        
        // Recharger la page après 2 secondes
        setTimeout(() => {
          window.location.reload()
          result.value = ''
        }, 2000)
      } else {
        result.value = 'Pas de données dans la réponse'
      }
    } catch (error) 
    {
      result.value = 'Erreur lors de la mise à jour de la photo: ' + (error.response?.data?.message || error.message)
    }
  }

</script>
