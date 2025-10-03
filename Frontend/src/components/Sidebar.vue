<template>
    <aside class="place-items-center text-black transition-all duration-300 bg-white col-span-1 md:col-span-2 
         w-full pt-8 gap-4  h-full">
        <div class="rounded-full bg-white border border-[#287196] hidden md:block overflow-hidden w-32 mb-8">
          <img :src="imageUrl" class="w-full h-full">
        </div>
        <ul class="flex flex-col gap-4 md:gap-2 mt-8 md:mt-0 mx-auto">
            <li v-for="link in links[role]" :key="link.path">
                <RouterLink class="flex gap-2 hover:bg-[#529ccc] md:p-2 cursor-pointer"  :to="link.path">
                    <component :is="link.icon"/>
                    <div class="lg:text-xl hidden md:block">{{link.name}}</div>
                </RouterLink>
            </li>
            <li>
                <RouterLink class="flex gap-2 hover:bg-[#529ccc] md:p-2 cursor-pointer"  to="/app/profil">
                    <IconUser/>
                    <div class="md:block hidden lg:text-xl">Profil</div>
                </RouterLink>
            </li>
            <li @click="logout" class="flex gap-2 hover:bg-[#529ccc] md:p-2 cursor-pointer">
                <IconLogout/>
                <div class="md:block hidden lg:text-xl">Se Deconnecter</div>
            </li>
        </ul>
    </aside>    
</template>
<script setup>
    import photo from '../assets/chat.png'
    import IconLogout from './icons/IconLogout.vue'
    import IconUser from './icons/IconUser.vue'
    import IconUsers from './icons/IconUsers.vue'
    import IconHome from './icons/IconHome.vue'
    import IconClock from './icons/IconClock.vue'
    import IconCalendar from './icons/IconCalendar.vue'
    import { useRouter } from 'vue-router'
    import { onMounted, ref, computed } from 'vue'
    import axios from 'axios'

    const router = useRouter();
    const user = ref({});
    const role = ref('');

    onMounted(async () => {
        const storedUser = localStorage.getItem('user');
        const user = storedUser ? JSON.parse(storedUser) : null;

        role.value =  user.role;
    })
    const imageUrl = computed(() => {
        if (user.value.photo) {
            return `${import.meta.env.VITE_API_URL}/storage/userprofile/${user.value.photo}`;
        }
        return photo; 
    });

    const logout = async  () => {

        try {
            const response = await axios.post('logout');
            localStorage.removeItem('token');
            router.push('/');
        } catch (error) {
            console.log(error);
        }
       
    }

    const links = {
        admin: [
            { name: "Tableau de Bord", path: "/app/admin/dashboard",icon: IconHome },
            { name: "Utilisateurs", path: "/app/admin/users",icon: IconUsers },
        ],
        comite: [
            { name: "Tableau de Bord", path: "/app/admin/dashboard", icon: IconHome },
            { name: "Conge", path: "/app/rh/conge", icon: IconCalendar },
            { name: "Retard", path: "/app/rh/retard", icon: IconClock },
        ],
        employe: [
            { name: 'Tableau de Bord', path: '/app/user/dashboard', icon: IconHome },
            { name: "Conge", path: "/app/user/conge", icon: IconCalendar },
            { name: "Retard", path: "/app/user/retard", icon: IconClock },
        ]
    }
</script>