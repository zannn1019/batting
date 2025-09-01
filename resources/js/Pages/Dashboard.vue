<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";

const auth = usePage().props.auth.user;

console.log(auth);

// Dummy stats for Admin
const stats = { teams: 8, players: 120, orders: 6, pending: 2 };

// Dummy orders for Admin
const orders = [
    {
        id: 1,
        team: "Team A",
        status: "submitted",
        submitted_at: "2025-08-20 10:00",
    },
    { id: 2, team: "Team B", status: "pending", submitted_at: null },
];

// Dummy team data for Team Manager
const team = {
    name: "Team Dragons",
    submitted: false,
    players: [
        { id: 1, name: "Alice", jersey: 7 },
        { id: 2, name: "Bob", jersey: 12 },
        { id: 3, name: "Charlie", jersey: 23 },
    ],
};

// Dummy submit function
function submitOrder() {
    alert("Batting order submitted!");
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{
                    auth.role === "admin" ? "Admin Dashboard" : "Team Dashboard"
                }}
            </h2>
        </template>
        <pre></pre>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- ✅ ADMIN DASHBOARD -->
                <div v-if="auth.role === 'admin'">
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500">
                                Teams
                            </h3>
                            <p class="text-2xl font-bold">{{ stats.teams }}</p>
                        </div>
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500">
                                Players
                            </h3>
                            <p class="text-2xl font-bold">
                                {{ stats.players }}
                            </p>
                        </div>
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500">
                                Orders Submitted
                            </h3>
                            <p class="text-2xl font-bold">{{ stats.orders }}</p>
                        </div>
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500">
                                Pending
                            </h3>
                            <p class="text-2xl font-bold">
                                {{ stats.pending }}
                            </p>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="bg-white shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4">Batting Orders</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border">Team</th>
                                        <th class="px-4 py-2 border">Status</th>
                                        <th class="px-4 py-2 border">
                                            Submitted At
                                        </th>
                                        <th class="px-4 py-2 border">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="order in orders"
                                        :key="order.id"
                                        class="text-center"
                                    >
                                        <td class="px-4 py-2 border">
                                            {{ order.team }}
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <span
                                                :class="
                                                    order.status === 'submitted'
                                                        ? 'text-green-600'
                                                        : 'text-yellow-600'
                                                "
                                            >
                                                {{ order.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border">
                                            {{ order.submitted_at ?? "-" }}
                                        </td>
                                        <td class="px-4 py-2 border space-x-2">
                                            <button
                                                class="px-3 py-1 text-sm bg-blue-500 text-white rounded"
                                            >
                                                View
                                            </button>
                                            <button
                                                class="px-3 py-1 text-sm bg-gray-200 rounded"
                                            >
                                                Resend
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ✅ TEAM DASHBOARD -->
                <div v-else>
                    <!-- Team Info -->
                    <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-bold mb-2">
                            Team: {{ team.name }}
                        </h3>
                        <p>Players Registered: {{ team.players.length }}</p>
                        <p>
                            Status:
                            <span
                                :class="
                                    team.submitted
                                        ? 'text-green-600'
                                        : 'text-yellow-600'
                                "
                            >
                                {{
                                    team.submitted
                                        ? "Batting Order Submitted"
                                        : "Not Submitted"
                                }}
                            </span>
                        </p>
                    </div>

                    <!-- Player List -->
                    <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-bold mb-2">Players</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border">#</th>
                                        <th class="px-4 py-2 border">Name</th>
                                        <th class="px-4 py-2 border">Jersey</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(player, i) in team.players"
                                        :key="player.id"
                                        class="text-center"
                                    >
                                        <td class="px-4 py-2 border">
                                            {{ i + 1 }}
                                        </td>
                                        <td class="px-4 py-2 border">
                                            {{ player.name }}
                                        </td>
                                        <td class="px-4 py-2 border">
                                            #{{ player.jersey }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Batting Order Form -->
                    <div class="bg-white shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4">Batting Order</h3>
                        <form @submit.prevent="submitOrder">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="i in 15"
                                    :key="i"
                                    class="form-control"
                                >
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Slot {{ i }}
                                    </label>
                                    <select
                                        class="select select-bordered w-full"
                                    >
                                        <option value="">Select Player</option>
                                        <option
                                            v-for="p in team.players"
                                            :key="p.id"
                                            :value="p.id"
                                        >
                                            {{ p.name }} (#{{ p.jersey }})
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="mt-4 btn btn-primary">
                                Submit Batting Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
