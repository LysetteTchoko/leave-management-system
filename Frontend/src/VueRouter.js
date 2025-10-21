import { createWebHistory, createRouter } from 'vue-router'

import LogIn from './views/main/Login.vue'
import Register from './views/main/Register.vue'
import Dashboard from './views/main/Dashboard.vue'
import Profil from './views/main/Profil.vue'
import DashUser from './views/user/UserDashboard.vue'
import DashAdmin from './views/admin/AdminDashboard.vue'
import ManageUser from './views/admin/ManageUser.vue'
import DashComite from "./views/rh/DashboardRh.vue";
import CongeUser from "./views/rh/CongeUser.vue"; 
import RetardUser from "./views/rh/RetardUser.vue";
import Employe from "./views/rh/Employe.vue";
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
      { path: 'comite/dashboard', component: DashComite , meta: { requiresAuth: true }},
      { path: 'comite/conge', component: CongeUser , meta: { requiresAuth: true }},
      { path: 'comite/retard', component: RetardUser , meta: { requiresAuth: true }},
      { path: 'comite/employe', component: Employe , meta: { requiresAuth: true }},
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

// Navigation Guard pour proteger les routes
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  const isAuthenticated = !!token;

  // Si la route necessite l'authentification
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      // Pas de token, rediriger vers login
      next({ path: '/' });
    } else {
      // Token present, autoriser
      next();
    }
  } // Si l'utilisateur est connecte et essaie d'accéder à login/register
  else if ((to.path === '/' || to.path === '/register') && isAuthenticated) {
    // Rediriger vers le dashboard selon le role
    if (user.role === 'admin') {
      next({ path: '/app/admin/dashboard' });
    } else if (user.role === 'comite') {
      next({ path: '/app/comite/dashboard' });
    } else {
      next({ path: '/app/user/dashboard' });
    }
  } 
  else {
    // Route publique, autoriser
    next();
  }
});

export default router;