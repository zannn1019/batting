<template>
  <div>
    <!-- Team Info Card -->
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
          <span class="font-bold text-blue-700">{{ team?.players.length }}</span>
        </p>
        <div class="flex gap-2 mt-2 items-center">
          <span
            v-for="p in team?.players.slice(0, 5)"
            :key="p.id"
            class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium"
          >
            {{ p.full_name.split(" ")[0] }}
          </span>
          <span v-if="team?.players.length > 5" class="text-xs text-gray-500">
            +{{ team.players.length - 5 }} more
          </span>
        </div>
      </div>
    </div>

    <!-- Match Info Card -->
    <div class="bg-white shadow sm:rounded-lg p-6 space-y-4 mb-6">
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
            <option v-for="o in opponents" :key="o.id" :value="o.id">
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
      <p class="mt-2 text-sm text-gray-500" v-if="isSubmitted && !isApproved">
        Batting order already submitted for this match. You can still edit it.
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

    <!-- Batting Order Table --> <div class="bg-white shadow sm:rounded-lg p-6 space-y-4 mb-6">
      <h3 class="text-lg font-bold mb-4">Batting Orders</h3>
    <form @submit.prevent="submitOrder" class="space-y-6">
      <table class="table table-zebra w-full">
        <thead>
          <tr>
            <th class="w-12">#</th>
            <th>Player Name</th>
            <th>Position</th>
            <th>Jersey Number</th>
            <th>Birthdate</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(playerSlot, index) in selectedPlayers" :key="index">
            <td>{{ index + 1 }}</td>
            <td>
            <span v-if="players.find(p => p.id === selectedPlayers[index])?.full_name && isSlotFixed(index)">
                {{ players.find(p => p.id === selectedPlayers[index])?.full_name  }}
            </span>
              <select
                v-model="selectedPlayers[index]"
                class="w-full select select-bordered"
                :disabled="isSlotFixed(index) || isApproved"
                v-else
              >
                <option value="">Select Player</option>
                <option
                  v-for="p in availablePlayers(index)"
                  :key="p.id"
                  :value="p.id"
                  :disabled="selectedPlayers.includes(p.id) && selectedPlayers[index] !== p.id"
                >
                  {{ p.full_name }} (#{{ p.back_number }})
                </option>
              </select>
            </td>

            <td>
              <span v-if="index < 10">{{ index + 1 }}</span>
              <span v-else>EP</span>
            </td>

            <td>{{ getPlayerNumber(selectedPlayers[index]) }}</td>

            <td>{{ getPlayerBirthdate(selectedPlayers[index]) }}</td>

          </tr>
        </tbody>
      </table>

      <div v-if="!isApproved" class="flex justify-end mt-4">
        <button type="submit" class="btn btn-primary">
          {{ isSubmitted ? "Update Batting Order" : "Submit Batting Order" }}
        </button>
      </div>
    </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  team: { type: Object, required: true },
  opponents: { type: Array, default: () => [] },
  submittedOrders: { type: Array, default: () => [] },
  selectedOpponentId: { type: Number, default: null },
  gameDate: { type: String, default: "" },
});

const selectedOpponent = ref(props.selectedOpponentId || "");
const gameDate = ref(props.gameDate || "");

const selectedPlayers = ref(Array(15).fill(null));
const players = computed(() => props.team?.players || []);

const currentMatchOrder = computed(() =>
  props.submittedOrders.find(
    (o) =>
      o.opponent_id === selectedOpponent.value && o.team_id === props.team.id
  )
);

const isSubmitted = computed(() => !!currentMatchOrder.value);
const isApproved = computed(() => currentMatchOrder.value?.status === "approved");

// Fill slots 1-10 based on player positions
const fixedSlots = computed(() => {
  const map = Array(10).fill(null);
  players.value.forEach((p) => {
    if (p.position >= 1 && p.position <= 10) map[p.position - 1] = p.id;
  });
  return map;
});

// Watch to pre-fill slots and extra players
watch(
  [fixedSlots, currentMatchOrder],
  () => {
    // Fixed slots 1-10
    fixedSlots.value.forEach((id, index) => {
      selectedPlayers.value[index] = id;
    });

    // Extra slots 11-15
    const usedIds = fixedSlots.value.filter(Boolean);
    const extraPlayers = players.value
      .filter((p) => !usedIds.includes(p.id))
      .slice(0, 5);

    extraPlayers.forEach((p, i) => (selectedPlayers.value[10 + i] = p.id));
    for (let i = extraPlayers.length; i < 5; i++) selectedPlayers.value[10 + i] = null;

    // Fill from existing order if available
    if (currentMatchOrder.value) {
      (currentMatchOrder.value.batting_orders || []).forEach((bo) => {
        selectedPlayers.value[bo.batting_position - 1] = bo.id_player;
      });
    }
  },
  { immediate: true }
);

// Helpers
function isSlotFixed(index) {
  return index < 10;
}

function availablePlayers(slotIndex) {
  if (slotIndex < 10) {
    return players.value.filter((p) => p.position === slotIndex + 1);
  } else {
    const usedIds = selectedPlayers.value.slice(0, 10).filter(Boolean);
    return players.value.filter((p) => !usedIds.includes(p.id));
  }
}

function getPlayerNumber(playerId) {
  const p = players.value.find((p) => p.id === playerId);
  return p ? p.back_number : "";
}

function getPlayerBirthdate(playerId) {
  const p = players.value.find((p) => p.id === playerId);
  return p ? p.birth_date : "";
}

function removeExtraPlayer(index) {
  if (index >= 10) selectedPlayers.value[index] = null;
}

// Submit
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
      .map((id, idx) => ({
        id,
        batting_position: idx + 1,
        is_extra_player: idx >= 10,
      }))
      .filter((p) => p.id !== null),
  };

  if (currentMatchOrder.value) {
    router.post(route("batting.update", currentMatchOrder.value.id), payload, {
      onSuccess: () => router.reload({ only: ["submittedOrders"] }),
      onError: (errors) => console.error(errors),
    });
  } else {
    router.post(route("batting.store"), payload, {
      onSuccess: () => router.reload({ only: ["submittedOrders"] }),
      onError: (errors) => console.error(errors),
    });
  }
}
</script>
