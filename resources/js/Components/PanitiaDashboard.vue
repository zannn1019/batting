<template>
    <OrdersTable :orders="orders" />
</template>

<script setup>
import OrdersTable from "./OrdersTable.vue";

defineProps({
    orders: { type: Array, required: true },
});
</script>
