<template> 
   <section class="bg-gray-200 relative min-h-screen w-full flex flex-col items-center justify-center">
        <nav class="bg-white w-full h-16 absolute top-0 shadow-md z-10">
          <div class="mt-2 md:mx-16 h-14 w-32 mx-8">
            <img :src="Logo" class="w-full h-full">
          </div>
        </nav>
        <main class="bg-white shadow-lg w-5/6 h-1/2 md:w-1/2 lg:w-1/3 p-5 align-self-center">
            <h3 class="font-semibold text-4xl text-[#287196] text-center mt-4">S'inscrire</h3>
            <Error :error="error" v-if="error"/>
            <form class="flex flex-col gap-4" @submit.prevent="handleLogin">
                <input class="p-2 mt-4 rounded-xl bg-sky-100 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]]"
                 type="text" v-model="data.username" placeholder="username" >
                 <p class="text-red-600" v-text="errors.username"></p>
                <input class="p-2  rounded-xl bg-sky-100 text-black focus:outline-none focus:ring-1 focus:ring-[#287196]]"
                 type="text" v-model="data.email" placeholder="Email" >
                 <p class="text-red-600" v-text="errors.email"></p>
                <input class="p-2 text-black rounded-xl bg-sky-100 focus:outline-none focus:ring-1 focus:ring-[#287196]" 
                type="password" placeholder="Mot de passe" v-model="data.password" >
                 <p v-if="passwordError" class="text-red-600">{{ passwordError }}</p>
                <button class="bg-[#287196] p-2 mt-2 text-white rounded-xl font-bold ">S'inscrire</button>
                <RouterLink to="/" class="mx-auto">Se Connecter</RouterLink>
            </form>
        </main>
    </section>
</template>

<script setup>
  import { reactive,ref } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';
  import Error from '../../components/Error.vue';
  import  Logo from '../../assets/logo.jpg'

  const router = useRouter();
  const error = ref('');
  const passwordError = ref('');
  
  const errors = reactive({
    username: '',
    email: '',
    password: ''
});
  const data = reactive({
    email:'',
    password: '',
    username: '' 
  })
  
const handleLogin = async () => {

  if (!data.username) errors.username = "Le nom d'utilisateur est requis";
  if (!data.email) errors.email = "L'email est requis";
  if (data.password.length < 6) {
    passwordError.value = 'Le mot de passe doit contenir au moins 6 caractères';
    return;
  }else{
     passwordError.value = '';
  }

  try{
    const response = await axios.post('registerUser', data);
    console.log(response);
    router.push('/');
  } catch (e) {
    error.value ='Entrez des informations Valides';
  }
}

  
</script>