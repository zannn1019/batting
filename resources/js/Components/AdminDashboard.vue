<template>
    <div>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Teams</h3>
                <p class="text-2xl font-bold">{{ stats.teams }}</p>
            </div>
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Players</h3>
                <p class="text-2xl font-bold">{{ stats.players }}</p>
            </div>
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">
                    Orders Submitted
                </h3>
                <p class="text-2xl font-bold">{{ stats.orders }}</p>
            </div>
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                <p class="text-2xl font-bold">{{ stats.pending }}</p>
            </div>
        </div>
        <OrdersTable :orders="orders" />
    </div>
</template>

<script setup>
import OrdersTable from "./OrdersTable.vue";

defineProps({
    orders: { type: Array, required: true },
    stats: { type: Object, required: true },
});
</script>
