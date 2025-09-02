<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    battings: Array,
});

const battings = computed(() => props.battings || []);
const selectedBatting = ref(null);
</script>

<template>
    <Head title="Batting Orders" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex flex-col">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Batting Orders
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Approve, reject, or view details of batting orders.
                    </p>
                </div>
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
                                    <th class="px-4 py-2 border">Opponent</th>
                                    <th class="px-4 py-2 border">Game Date</th>
                                    <th class="px-4 py-2 border">Players</th>
                                    <th class="px-4 py-2 border">Status</th>
                                    <th class="px-4 py-2 border">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(batting, i) in battings"
                                    :key="batting.id"
                                    class="align-middle "
                                >
                                    <td class="px-4 py-2 border text-center">
                                        {{ i + 1 }}
                                    </td>
                                    <td class="px-4 py-2 border font-semibold">
                                        {{ batting.team?.team_name || "—" }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ batting.opponent?.team_name || "—" }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ $formatGameTime(batting.game_date) || "—" }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <div
                                            v-if="batting.batting_orders && batting.batting_orders.length"
                                            class="list-disc pl-5"
                                        >
                                            {{ batting.batting_orders.length }}
                                        </div>
                                        <span v-else class="text-gray-400">
                                            No players
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <span
                                            :class="[
                                                'inline-block px-2 py-1 rounded text-xs font-semibold',
                                                batting.status === 'approved'
                                                    ? 'bg-green-100 text-green-800'
                                                    : batting.status === 'rejected'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-yellow-100 text-yellow-800'
                                            ]"
                                        >
                                            {{ batting.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border space-x-2">
                                        <!-- Detail -->
                                        <label
                                            for="detail-modal"
                                            class="btn btn-sm btn-info text-white"
                                            @click="selectedBatting = batting"
                                        >
                                            Detail
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <input type="checkbox" id="detail-modal" class="modal-toggle" />
        <div class="modal">
            <div class="modal-box max-w-3xl">
                <h3 class="font-bold text-lg">
                    Batting Order - {{ selectedBatting?.team?.team_name }}
                </h3>
                <p class="py-2 text-sm text-gray-600">
                    Opponent: {{ selectedBatting?.opponent?.team_name }}
                </p>
                <div v-if="selectedBatting?.batting_orders?.length">
                    <table class="min-w-full border mt-4">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">#</th>
                                <th class="px-4 py-2 border">Player Name</th>
                                <th class="px-4 py-2 border">Position</th>
                                <th class="px-4 py-2 border">Batting Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(player, index) in selectedBatting.batting_orders"
                                :key="player.id_player"
                                class="align-top"
                            >
                                <td class="px-4 py-2 border text-center">{{ index + 1 }}</td>
                                <td class="px-4 py-2 border">{{ player.player.full_name }}</td>
                                <td class="px-4 py-2 border">{{ player.player.position }}</td>
                                <td class="px-4 py-2 border text-center">{{ player.batting_position }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <span v-else class="text-gray-400">No players assigned</span>

                <div class="modal-action">
                    <label for="detail-modal" class="btn">Close</label>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <input type="checkbox" id="reject-modal" class="modal-toggle" />
        <div class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">
                    Confirm Reject
                </h3>
                <p class="py-4">
                    Are you sure you want to
                    reject batting order for
                    <span class="font-semibold">
                        {{ selectedBatting?.team?.team_name }}
                    </span>?
                </p>
                <div class="modal-action">
                    <label for="reject-modal" class="btn">Cancel</label>
                    <button
                        class="btn btn-error text-white"
                        @click="rejectBatting(selectedBatting.id)"
                    >
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
