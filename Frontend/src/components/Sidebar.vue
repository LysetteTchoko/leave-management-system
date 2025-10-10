<template>
    <aside @mouseenter="onHover(true)" @mouseleave="onHover(false)"
        :class="['flex flex-col items-center text-black transition-all duration-300 bg-white shadow-lg z-40',
         'fixed top-12 left-0 h-[calc(100vh-3rem)] pt-6','w-64','md:w-20 md:hover:w-64','lg:w-64',
         isOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0']">
        <div class="rounded-full bg-white border border-[#287196] hidden md:block overflow-hidden mb-6 w-12 h-12 lg:w-32 lg:h-32 flex-shrink-0">
          <img :src="imageUrl" class="w-full h-full object-cover">
        </div>
        <ul class="flex flex-col gap-1 w-full px-2">
            <li v-for="link in links[role]" :key="link.path" class="w-full">
                <RouterLink class="flex items-center hover:bg-[#529ccc] rounded-lg p-2 cursor-pointer transition-colors duration-200"  
                    :class="[
                        showSidebarText ? 'gap-3' : 'justify-center md:justify-center lg:gap-3',
                        $route.path === link.path ? 'bg-[#529ccc] text-white' : ''
                    ]"
                    :to="link.path" @click="closeSidebar">
                    <component :is="link.icon" class="flex-shrink-0"/>
                    <span class="transition-all duration-300 overflow-hidden text-base whitespace-nowrap lg:text-lg"
                         :class="showSidebarText ? 'opacity-100 w-auto' : 'opacity-0 w-0 lg:opacity-100 lg:w-auto'">{{link.name}}</span>
                </RouterLink>
            </li>
            <li class="w-full">
                <RouterLink class="flex items-center hover:bg-[#529ccc] rounded-lg p-2 cursor-pointer transition-colors duration-200"  
                            :class="[
                                showSidebarText ? 'gap-3' : 'justify-center md:justify-center lg:gap-3',
                                $route.path === '/app/profil' ? 'bg-[#529ccc] text-white' : ''
                            ]"
                            to="/app/profil" @click="closeSidebar">
                    <IconUser class="flex-shrink-0"/>
                    <span class="transition-all duration-300 overflow-hidden text-base whitespace-nowrap lg:text-lg"
                         :class="showSidebarText ? 'opacity-100 w-auto' : 'opacity-0 w-0 lg:opacity-100 lg:w-auto'">Profil</span>
                </RouterLink>
            </li>
            <li @click="logout" class="flex items-center hover:bg-[#529ccc] rounded-lg p-2 cursor-pointer transition-colors duration-200 w-full"
                :class="showSidebarText ? 'gap-3' : 'justify-center md:justify-center lg:gap-3'">
                <IconLogout class="flex-shrink-0"/>
                <span class="transition-all duration-300 overflow-hidden text-base whitespace-nowrap lg:text-lg"
                         :class="showSidebarText ? 'opacity-100 w-auto' : 'opacity-0 w-0 lg:opacity-100 lg:w-auto'">Se Deconnecter</span>
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
    import { onMounted, ref, computed, inject } from 'vue'
    import axios from 'axios'

    const isOpen = inject('sidebarOpen')
    const showSidebarText = ref(false)

    const router = useRouter();
    const user = ref({});
    const role = ref('');

    onMounted(async () => {
        const storedUser = localStorage.getItem('user');
        const user = storedUser ? JSON.parse(storedUser) : null;

        role.value =  user.role;
        
        onHover(false)
    })
    const imageUrl = computed(() => {
        if (user.value.photo) {
            return `${import.meta.env.VITE_API_URL}/storage/userprofile/${user.value.photo}`;
        }
        return photo; 
    });

    const onHover = (value) => {
        if (window.innerWidth >= 768 && window.innerWidth < 1024) {
            showSidebarText.value = value
        } else if (window.innerWidth >= 1024) {
            showSidebarText.value = true
        } else {
            showSidebarText.value = isOpen.value
        }
    }

    const closeSidebar = () => {
        if (window.innerWidth < 768) {
            isOpen.value = false
        }
    }

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