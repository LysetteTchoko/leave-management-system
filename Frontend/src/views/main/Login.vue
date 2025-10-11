<template> 
  <div class="bg-gray-200 relative min-h-screen w-full flex flex-col items-center justify-center">
    <nav class="bg-white w-full h-16 absolute top-0 shadow-md z-10">
      <div class="mt-2 md:mx-16 h-14 w-32 mx-8">
        <img :src="Logo" class="w-full h-full">
      </div>
    </nav>
    <main class="bg-white shadow-lg w-5/6 h-1/2 md:w-1/2 lg:w-1/3 p-5 align-self-center">
      <h3 class="font-semibold text-4xl text-[#287196] text-center">Connexion</h3>
      <Error class="text-red-500 p-2 border bg-red-200" :error="error" v-if="error"/>
      <form class="flex flex-col gap-4" @submit.prevent="handleLogin">
        <input class="p-2 mt-4 rounded-xl bg-sky-100 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]"
            type="text" v-model="data.email" placeholder="Email" >
        <input class="p-2 text-black mt-4 rounded-xl bg-sky-100 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
            type="password" placeholder="Mot de passe" v-model="data.password" >
        <button class="bg-[#287196] p-2 mt-2 text-white rounded-xl font-bold ">Se connecter</button>
        <RouterLink to="/register" class="mx-auto text-green-600" >Creer un compte </RouterLink>
      </form>
    </main>
  </div>
</template>

<script setup>
  import {  reactive,ref } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';
  import Error from '../../components/Error.vue';
  import  Logo from '../../assets/logo.jpg'

  const router = useRouter();
  const error = ref('');
  const user = ref({})
  const role = ref('')
  const data = reactive({
    email:'',
    password: '',
    latitude: '',
    longitude: ''
  })
  
 setTimeout(() => {
    error.value = ''
  }, 3000)
  const getLocation = () => {
    return new Promise((resolve, reject) => {
      if (!navigator.geolocation) {
        reject("La géolocalisation n'est pas supportée par ce navigateur.")
        return
      }

      navigator.geolocation.getCurrentPosition(
        (position) => {
          resolve({
            latitude: position.coords.latitude,
            longitude: position.coords.longitude
          })
        },
        (error) => {
          console.log(error.message)
        }
      )
    })
  }

  const handleLogin = async () => {
    try {
      const location = await getLocation()
      data.latitude = location.latitude
      data.longitude = location.longitude

      const response = await axios.post('login', data)
        user.value = response.data.data;
        role.value = response.data.data.role;

        localStorage.setItem('user', JSON.stringify(user.value));
        const token = response.data.token
        localStorage.removeItem('token');
        localStorage.setItem('token', token );

        if (role.value == 'admin') {
          router.push('/app/admin/dashboard');
        } else if (role.value == 'comite') {
          router.push('/app/comite/dashboard');
        } else  {
          router.push('/app/user/dashboard');
        }
    } catch (e) {
      error.value = "Identifiants Incorrect"
    }
  }
</script>