<template>
    <div>
        <div
            class="bg-gradient-to-r from-blue-50 to-blue-100 shadow-lg rounded-xl p-6 mb-8 flex items-center gap-6"
        >
            <div v-if="team?.logo" class="flex-shrink-0">
                <img
                    :src="team.logo"
                    alt="Team Logo"
                    class="h-20 w-20 p-2 object-contain rounded-full border-4 border-blue-300 shadow"
                />
            </div>
            <div>
                <h3
                    class="text-2xl font-extrabold text-blue-800 mb-2 flex items-center gap-2"
                >
                    <span>{{ team?.team_name }}</span>
                    <span
                        class="bg-blue-200 text-blue-700 text-xs px-2 py-1 rounded-full font-semibold"
                        >Manager</span
                    >
                </h3>
                <p class="text-gray-700 mb-1">
                    <span class="font-medium">Players Registered:</span>
                    <span class="font-bold text-blue-700">{{
                        team?.players.length
                    }}</span>
                </p>
                <div class="flex gap-2 mt-2 items-center">
                    <span
                        v-for="p in team?.players.slice(0, 5)"
                        :key="p.id"
                        class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium"
                    >
                        {{ p.full_name.split(" ")[0] }}
                    </span>
                    <span
                        v-if="team?.players.length > 5"
                        class="text-xs text-gray-500"
                        >+{{ team.players.length - 5 }} more</span
                    >
                </div>
            </div>
        </div>

        <form @submit.prevent="submitOrder" class="space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <h3 class="text-lg font-bold mb-4">Match Info</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            for="opponent"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Opponent
                        </label>
                        <select
                            id="opponent"
                            v-model="selectedOpponent"
                            class="select select-bordered w-full"
                            required
                        >
                            <option value="">Select Opponent</option>
                            <option
                                v-for="o in opponents"
                                :key="o.id"
                                :value="o.id"
                            >
                                {{ o.team_name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            for="game-date"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Game Date
                        </label>
                        <input
                            id="game-date"
                            type="date"
                            v-model="gameDate"
                            class="input input-bordered w-full"
                            :disabled="isApproved"
                            required
                        />
                    </div>
                </div>

                <p
                    class="mt-2 text-sm text-gray-500"
                    v-if="isSubmitted && !isApproved"
                >
                    Batting order already submitted for this match. You can
                    still edit it.
                </p>
                <p v-else-if="isApproved" class="mt-2 text-sm text-green-600">
                    Batting order approved for this match. Edits are disabled.
                </p>
                <p>
                    Status:
                    <span
                        :class="{
                            'text-green-600': isSubmitted,
                            'text-yellow-600': !isSubmitted,
                        }"
                    >
                        {{
                            isSubmitted
                                ? isApproved
                                    ? "Approved"
                                    : "Submitted"
                                : "Not Submitted"
                        }}
                    </span>
                </p>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Batting Order</h3>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    <div v-for="i in 15" :key="i" class="form-control">
                        <label
                            :for="`player-slot-${i}`"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Slot {{ i }}
                            <span v-if="i > 9" class="text-xs text-gray-500"
                                >(Extra Player)</span
                            >
                        </label>
                        <select
                            :id="`player-slot-${i}`"
                            class="select select-bordered w-full"
                            v-model="selectedPlayers[i - 1]"
                            :disabled="isApproved"
                        >
                            <option value="">Select Player</option>
                            <option
                                v-for="p in players"
                                :key="p.id"
                                :value="p.id"
                                :disabled="
                                    selectedPlayers.includes(p.id) &&
                                    selectedPlayers[i - 1] !== p.id
                                "
                            >
                                {{ p.full_name }} (#{{ p.back_number }})
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="isApproved"
                >
                    {{
                        isSubmitted
                            ? "Update Batting Order"
                            : "Submit Batting Order"
                    }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    team: { type: Object, required: true },
    opponents: { type: Array, default: () => [] },
    submittedOrders: { type: Array, default: () => [] },
});

// State
const selectedOpponent = ref("");
const gameDate = ref("");
const selectedPlayers = ref(Array(15).fill(""));

const players = computed(() => props.team?.players || []);

const currentMatchOrder = computed(() =>
    props.submittedOrders.find((o) => {
        return (
            o.opponent_id == selectedOpponent.value &&
            o.team_id == props.team.id
        );
    })
);

const isSubmitted = computed(() => !!currentMatchOrder.value);
const isApproved = computed(
    () => currentMatchOrder.value?.status === "approved"
);

watch(
    selectedOpponent,
    (opponentId) => {
        if (!opponentId) {
            resetForm();
            return;
        }
        const existing = currentMatchOrder.value;

        if (existing) {
            gameDate.value = existing.game_date;
            selectedPlayers.value = Array(15)
                .fill("")
                .map((_, i) => {
                    const order = (existing.batting_orders || []).find(
                        (bo) => bo.batting_position === i + 1
                    );
                    return order ? order.id_player : "";
                });
        } else {
            resetForm();
        }
    },
    { immediate: true }
); // Run immediately to load initial state if selectedOpponent has a value

function resetForm() {
    gameDate.value = "";
    selectedPlayers.value = Array(15).fill("");
}

function submitOrder() {
    if (!selectedOpponent.value || !gameDate.value) {
        alert("Please select an opponent and game date.");
        return;
    }

    const payload = {
        team_id: props.team.id,
        opponent_id: selectedOpponent.value,
        game_date: gameDate.value,
        players: selectedPlayers.value
            .map((id, index) => ({
                id: id || null,
                batting_position: index + 1,
                is_extra_player: index >= 9,
            }))
            .filter((p) => p.id !== null),
    };

    if (currentMatchOrder.value) {
        payload.id = currentMatchOrder.value.id;
        router.post(
            route("batting.update", currentMatchOrder.value.id),
            payload,
            {
                onSuccess: () => {
                    alert("Batting order updated successfully!");
                    router.reload({ only: ["submittedOrders"] });
                },
                onError: (errors) => {
                    console.error(errors);
                    alert("Failed to update batting order!");
                },
            }
        );
    } else {
        router.post(route("batting.store"), payload, {
            onSuccess: () => {
                alert("Batting order submitted successfully!");
                router.reload({ only: ["submittedOrders"] });
            },
            onError: (errors) => {
                console.error(errors);
                alert("Failed to submit batting order!");
            },
        });
    }
}
</script>
