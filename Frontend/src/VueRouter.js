import { createWebHistory, createRouter } from 'vue-router'

import LogIn from './views/main/Login.vue'
import Register from './views/main/Register.vue'
import Dashboard from './views/main/Dashboard.vue'
import Profil from './views/main/Profil.vue'
import DashUser from './views/user/UserDashboard.vue'
import DashAdmin from './views/admin/AdminDashboard.vue'
import ManageUser from './views/admin/ManageUser.vue'
import DashRh from "./views/rh/DashboardRh.vue";
import CongeUser from "./views/rh/CongeUser.vue"; 
import RetardUser from "./views/rh/RetardUser.vue";
import Conge from "./views/user/Conge.vue"; 
import Retard from "./views/user/Retard.vue";
import AddConge from "./views/user/AddConge.vue";


const routes = [
  { path: '/', component: LogIn},
  { path: '/register', component: Register},
  { path:'/app', component: Dashboard ,
    children:
    [
      { path: 'user/dashboard', component:  DashUser, meta: { requiresAuth: true } },
      { path: 'profil', component: Profil  , meta: { requiresAuth: true }},
      { path: 'admin/dashboard', component: DashAdmin  , meta: { requiresAuth: true }},
      { path: 'admin/users', component: ManageUser  , meta: { requiresAuth: true }},
      { path: 'rh/dashboard', component: DashRh , meta: { requiresAuth: true }},
      { path: 'rh/conge', component: CongeUser , meta: { requiresAuth: true }},
      { path: 'rh/retard', component: RetardUser , meta: { requiresAuth: true }},
      { path: 'user/conge', component: Conge , meta: { requiresAuth: true }},
      { path: 'user/retard', component: Retard , meta: { requiresAuth: true }},
      { path: 'user/addConge', component: AddConge , meta: { requiresAuth: true }},
    ]
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router;