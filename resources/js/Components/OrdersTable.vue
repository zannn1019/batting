<template>
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <h3 class="text-2xl font-extrabold mb-6 text-gray-900 border-b pb-4">
            Upcoming Games & Batting Orders
        </h3>

        <div v-if="orders.length > 0" class="grid gap-6">
            <div
                v-for="order in orders"
                :key="order.id"
                class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 border border-gray-200 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all duration-200 ease-in-out cursor-pointer"
            >
                <div
                    class="flex flex-col md:flex-row items-start md:items-center flex-grow mb-4 md:mb-0"
                >
                    <div
                        class="flex items-center space-x-3 mb-3 md:mb-0 md:mr-6"
                    >
                        <img
                            v-if="order.team?.logo"
                            :src="order.team.logo"
                            :alt="`${order.team.team_name} Logo`"
                            class="w-12 h-12 rounded-full object-contain border-2 border-blue-400 p-0.5"
                        />
                        <span class="font-bold text-lg text-gray-800">{{
                            order.team?.team_name
                        }}</span>
                    </div>

                    <span
                        class="text-gray-500 font-extrabold text-xl mx-4 hidden md:block"
                        >VS</span
                    >
                    <span
                        class="text-gray-500 font-extrabold text-lg block md:hidden mb-3"
                        >vs</span
                    >

                    <div class="flex items-center space-x-3">
                        <img
                            v-if="order.opponent?.logo"
                            :src="order.opponent.logo"
                            :alt="`${order.opponent.team_name} Logo`"
                            class="w-12 h-12 rounded-full object-contain border-2 border-red-400 p-0.5"
                        />
                        <span class="font-bold text-lg text-gray-800">{{
                            order.opponent?.team_name
                        }}</span>
                    </div>

                    <div
                        class="ml-0 md:ml-8 mt-4 md:mt-0 flex items-center space-x-2 text-md font-medium text-gray-600 bg-gray-100 px-3 py-1.5 rounded-full"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-blue-500"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                stroke="none"
                                d="M0 0h24v24H0z"
                                fill="none"
                            ></path>
                            <circle cx="12" cy="12" r="9"></circle>
                            <polyline points="12 7 12 12 15 15"></polyline>
                        </svg>
                        <span>
                            {{ $formatGameTime(order.game_date) }}
                        </span>
                    </div>
                </div>

                <div class="min-w-[100px] text-right mt-4 md:mt-0">
                    <span
                        :class="[
                            'px-4 py-2 rounded-full text-sm font-bold',
                            order.status === 'approved'
                                ? 'bg-green-100 text-green-700'
                                : order.status === 'rejected'
                                ? 'bg-red-100 text-red-700'
                                : 'bg-yellow-100 text-yellow-700 animate-pulse',
                        ]"
                    >
                        {{
                            order.status.charAt(0).toUpperCase() +
                            order.status.slice(1)
                        }}
                    </span>
                </div>
            </div>
        </div>

        <div
            v-else
            class="text-center text-gray-500 py-10 border-t border-gray-200 mt-4"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mx-auto h-12 w-12 text-gray-400 mb-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                />
            </svg>
            <p class="text-lg font-medium">
                No upcoming games with pending orders.
            </p>
            <p class="text-sm text-gray-400">
                Check back later or add new games.
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    orders: {
        type: Array,
        required: true,
    },
});
</script>
