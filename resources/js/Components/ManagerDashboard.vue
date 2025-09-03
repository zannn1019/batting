<template>
  <div>
    <!-- Team Info Card -->
    <div
      class="bg-gradient-to-r from-[#eff6ff] to-[#dbeafe] shadow-lg rounded-xl p-6 mb-8 flex items-center gap-6"
    >
      <div v-if="team?.logo" class="flex-shrink-0">
        <img
          :src="team.logo"
          alt="Team Logo"
          class="h-20 w-20 p-2 object-contain rounded-full border-4 border-blue-300 shadow"
        />
      </div>
      <div>
        <h3 class="text-2xl font-extrabold text-blue-800 mb-2 flex items-center gap-2">
          <span>{{ team?.team_name }}</span>
          <span class="bg-blue-200 text-blue-700 text-xs px-2 py-1 rounded-full font-semibold">
            Manager
          </span>
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
          <label for="opponent" class="block text-sm font-medium text-gray-700 mb-1">
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
          <label for="game-date" class="block text-sm font-medium text-gray-700 mb-1">
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
        <span :class="{'text-green-600': isSubmitted, 'text-yellow-600': !isSubmitted}">
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

    <!-- Batting Order Table -->
    <div class="bg-white shadow sm:rounded-lg p-6 space-y-4 mb-6">
      <h3 class="text-lg font-bold mb-4">Batting Orders</h3>
      <form @submit.prevent="submitOrder" class="space-y-6">
        <table class="table table-zebra w-full" id="batting-table">
          <thead>
            <tr>
              <th class="w-12">#</th>
              <th>Player Name</th>
              <th>Position</th>
              <th>Jersey Number</th>
              <th>Type</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(playerId, index) in selectedPlayers" :key="index">
              <td>{{ index + 1 }}</td>
              <td>
                <select
                  v-model="selectedPlayers[index]"
                  class="w-full select select-bordered"
                  :disabled="isApproved"
                >
                  <option value="">Select Player</option>
                  <option
                    v-for="p in availablePlayers(index)"
                    :key="p.id"
                    :value="p.id"
                  >
                    ({{ p.position }}) - {{ p.full_name }} - (#{{ p.back_number }})
                  </option>
                </select>
              </td>

              <td>
                <select
                  v-model="positions[index]"
                  class="w-full select select-bordered"
                  :disabled="isApproved"
                >
                  <option disabled value="">-- Select Position --</option>
                  <option v-for="n in 10" :key="n" :value="n" :disabled="isPositionTaken(n, index)">
                    {{ n }}
                  </option>
                  <option value="EP">EP (Extra Player)</option>
                </select>
              </td>

              <td>{{ getPlayerNumber(playerId) }}</td>
              <td>
                <span
                  v-if="positions[index] && positions[index] !== 'EP' && positions[index] <= 10"
                  class="badge badge-primary"
                >
                  Batting
                </span>
                <span
                  v-else-if="positions[index] === 'EP' || positions[index] > 10"
                  class="badge badge-secondary"
                >
                  EP
                </span>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="flex justify-between mt-4">
          <button type="submit" v-if="!isApproved" class="btn btn-primary">
            {{ isSubmitted ? "Update Batting Order" : "Submit Batting Order" }}
          </button>
          <button type="button" class="btn btn-success" @click="exportBattingPDF">
            Export PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

const props = defineProps({
  team: { type: Object, required: true },
  opponents: { type: Array, default: () => [] },
  submittedOrders: { type: Array, default: () => [] },
  selectedOpponentId: { type: Number, default: null },
  gameDate: { type: String, default: "" },
});

const selectedOpponent = ref(props.selectedOpponentId || "");
const gameDate = ref(
  props.submittedOrders.find((o) => o.opponent_id === props.selectedOpponentId)?.game_date ||
    props.gameDate ||
    ""
);

const players = computed(() => props.team?.players || []);
const selectedPlayers = ref(Array(15).fill(null));
const positions = ref(Array(15).fill(null));

const currentMatchOrder = computed(() =>
  props.submittedOrders.find(
    (o) => o.opponent_id === selectedOpponent.value && o.team_id === props.team.id
  )
);

const isSubmitted = computed(() => !!currentMatchOrder.value);
const isApproved = computed(() => currentMatchOrder.value?.status === "approved");

function isPositionTaken(pos, currentIndex) {
  return positions.value.some((p, idx) => idx !== currentIndex && p === pos);
}

// Load existing order if available
watch(
  currentMatchOrder,
  () => {
    if (currentMatchOrder.value) {
      selectedPlayers.value = Array(15).fill(null);
      positions.value = Array(15).fill(null);

      (currentMatchOrder.value.batting_orders || []).forEach((bo, idx) => {
        selectedPlayers.value[idx] = bo.id_player;
        positions.value[idx] = bo.batting_position;
      });

      gameDate.value = currentMatchOrder.value.game_date || gameDate.value;
    }
  },
  { immediate: true }
);

function availablePlayers(slotIndex) {
  const usedIds = selectedPlayers.value.filter((id, idx) => idx !== slotIndex && id);
  return players.value.filter((p) => !usedIds.includes(p.id));
}

function getPlayerNumber(playerId) {
  const p = players.value.find((p) => p.id === playerId);
  return p ? p.back_number : "";
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
      .map((id, idx) => ({ id, batting_position: positions.value[idx] }))
      .filter((p) => p.id !== null && p.batting_position !== null),
  };

  if (currentMatchOrder.value) {
    router.put(route("batting.update", currentMatchOrder.value.id), payload, {
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

// ---------------- PDF Export ----------------
function exportBattingPDF() {
  if (!selectedOpponent.value) {
    alert("Please select an opponent first.");
    return;
  }

  const doc = new jsPDF();
  const matchOpponent = props.opponents.find((o) => o.id === selectedOpponent.value);

  // Title
  doc.setFontSize(16);
  doc.text(`Batting Order - ${props.team.team_name}`, 14, 20);

  // Opponent and Date
  doc.setFontSize(12);
  doc.text(`Opponent: ${matchOpponent ? matchOpponent.team_name : "—"}`, 14, 30);
  doc.text(`Game Date: ${gameDate.value || "—"}`, 14, 38);

  // Table headers
  const tableColumn = ["#", "Player Name", "Position", "Jersey Number"];
  const tableRows = [];

  selectedPlayers.value.forEach((playerId, index) => {
    if (!playerId) return;

    const player = players.value.find((p) => p.id === playerId);
    const pos = positions.value[index];
    if (!player) return;

    tableRows.push([
      index + 1,
      player.full_name,
      pos,
      "#" + player.back_number,
    ]);
  });

  autoTable(doc, {
    head: [tableColumn],
    body: tableRows.length ? tableRows : [["-", "No players selected", "-", "-", "-"]],
    startY: 50,
    theme: "grid",
    styles: { fontSize: 10 },
    headStyles: { fillColor: [41, 128, 185] },
  });

  doc.save(`Batting_${props.team.team_name}.pdf`);
}
</script>
