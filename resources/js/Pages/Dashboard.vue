<template>
  <AuthenticatedLayout>
    <template #header>
      <h2>{{ headerTitle }}</h2>
    </template>
    <div class="max-w-7xl mx-auto py-12">
      <component
        :is="currentDashboard"
        :orders="submittedOrders"
        :stats="stats"
        :team="auth.team"
        :opponents="opponents"
        :submittedOrders="submittedOrders"
      />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import AdminDashboard from '@/Components/AdminDashboard.vue'
import PanitiaDashboard from '@/Components/PanitiaDashboard.vue'
import ManagerDashboard from '@/Components/ManagerDashboard.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const auth = usePage().props.auth.user
const opponents = usePage().props.opponents
const submittedOrders = usePage().props.submittedOrders

const stats = usePage().props.stats

const dashboards = {
  admin: AdminDashboard,
  panitia: PanitiaDashboard,
  manager: ManagerDashboard,
}

const currentDashboard = dashboards[auth.role]

const headerTitle =
  auth.role === 'admin'
    ? 'Admin Dashboard'
    : auth.role === 'panitia'
    ? 'Panitia Dashboard'
    : 'Team Manager Dashboard'
</script>
