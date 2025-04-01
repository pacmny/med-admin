import { createRouter, createWebHistory } from 'vue-router'
import MedicationAdminDemo from '../views/MedicationAdminDemo.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/administration'
    },
    {
      path: '/administration',
      name: 'Administration',
      component: MedicationAdminDemo
    }
  ]
})

export default router