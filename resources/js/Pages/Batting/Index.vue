<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const battingOrders = [
    {
        id: 1,
        team: "Team Dragons",
        status: "submitted",
        submitted_at: "2025-08-20 10:00",
        slots: [
            { order: 1, player: "Alice", position: "1B" },
            { order: 2, player: "Bob", position: "2B" },
        ],
    },
    {
        id: 2,
        team: "Team Tigers",
        status: "pending",
        submitted_at: null,
        slots: [],
    },
];
</script>

<template>
    <Head title="Batting Orders" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex flex-col">
                    <h2
                        class="text-xl font-semibold leading-tight text-gray-800"
                    >
                        Batting Orders
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage your batting orders and players here.
                    </p>
                </div>
                <Link class="btn btn-neutral" :href="route('batting.create')">
                    Add Batting Order
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">#</th>
                                    <th class="px-4 py-2 border">Team</th>
                                    <th class="px-4 py-2 border">Status</th>
                                    <th class="px-4 py-2 border">
                                        Submitted At
                                    </th>
                                    <th class="px-4 py-2 border">
                                        Batting Order
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(order, i) in battingOrders"
                                    :key="order.id"
                                >
                                    <td class="px-4 py-2 border text-center">
                                        {{ i + 1 }}
                                    </td>
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
                                    <td class="px-4 py-2 border">
                                        <ul
                                            v-if="order.slots.length"
                                            class="list-decimal pl-5"
                                        >
                                            <li
                                                v-for="s in order.slots"
                                                :key="s.order"
                                            >
                                                {{ s.order }}.
                                                {{ s.player }} ({{
                                                    s.position
                                                }})
                                            </li>
                                        </ul>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
