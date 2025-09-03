<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { onMounted, ref, watch } from "vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    mode: { type: String, default: "create" }, // create | edit | detail
    managers: Array,
    team: { type: Object, default: null },
    teamPlayers: { type: Array, default: () => [] },
});

const players = ref([]);
const logoPreview = ref(null);

watch(
    () => props.teamPlayers,
    (val) => {
        if (val && val.length > 0) {
            players.value = val.map((p) => ({
                id: p.id ?? null,
                name: p.full_name,
                position: p.position,
                number: p.back_number,
                birthdate: p.birth_date,
            }));
        }
    },
    { immediate: true }
);

function isPositionTaken(position, currentIndex) {
    return players.value.some(
        (p, i) => i !== currentIndex && p.position == position
    );
}

const form = useForm({
    team_name: props.team?.team_name ?? "",
    head_coach: props.team?.head_coach ?? "",
    coach: props.team?.coach ?? "",
    id_manager: props.team?.id_manager ?? usePage().props.auth.user.id,
    logo: null,
    players: props.team?.players || [],
});

function handleLogoUpload(event) {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        form.logo = null;
        logoPreview.value = null;
    }
}

function addPlayer() {
    players.value.push({
        id: null,
        name: "",
        position: "utility",
        number: null,
        birthdate: "",
    });
}

function isNumberTaken(number, currentIndex) {
    return players.value.some(
        (p, i) => i !== currentIndex && p.number == number && number !== ""
    );
}

function removePlayer(index) {
    players.value.splice(index, 1);
}
function submit() {
    if (!form.team_name || !form.head_coach || !form.id_manager) {
        alert("Team name, head coach, and manager are required.");
        return;
    }

    if (players.value.length === 0) {
        alert("Please add at least one player.");
        return;
    }

    // Check for duplicate jersey numbers
    const numbers = players.value.map((p) => p.number).filter((n) => n !== "");
    const hasDuplicateNumber = numbers.length !== new Set(numbers).size;
    if (hasDuplicateNumber) {
        alert("There are players with the same jersey number. Please ensure each player has a unique number.");
        return;
    }

    form.players = players.value.map((p) => ({
        id: p.id,
        full_name: p.name,
        position: String(p.position),
        back_number: p.number,
        birth_date: p.birthdate,
    }));

    if (props.mode === "create") {
        router.post(route("team.store"), {
            ...form,
        });
    } else if (props.mode === "edit") {
        router.post(route("team.update", props.team.id), {
            _method: "put",
            ...form,
        });
    }
}

onMounted(() => {
    if (props.mode === "edit" || props.mode === "detail") {
        form.players = players.value;
        form.team_name = props.team.team_name;
        form.head_coach = props.team.head_coach;
        form.coach = props.team.coach;
        if (props.team.logo_url) {
            logoPreview.value = props.team.logo_url;
        } else if (props.team.logo) {
            logoPreview.value = props.team.logo;
        }
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{
                    mode === "create"
                        ? "Create Team"
                        : mode === "edit"
                        ? "Edit Team"
                        : "Team Detail"
                }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div
                        v-if="logoPreview"
                        class="mt-4 flex justify-center items-center w-full"
                    >
                        <img
                            :src="logoPreview"
                            alt="Team Logo Preview"
                            class="w-32"
                        />
                    </div>
                    <form @submit.prevent="submit">
                        <!-- Team Info -->
                        <h3 class="text-xl font-semibold mb-4">
                            Team Information
                        </h3>

                        <div class="mb-4">
                            <InputLabel for="team_name" value="Team Name" />
                            <TextInput
                                id="team_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.team_name"
                                :disabled="mode === 'detail'"
                                required
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.team_name"
                            />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="head_coach" value="Head Coach" />
                            <TextInput
                                id="head_coach"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.head_coach"
                                :disabled="mode === 'detail'"
                                required
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.head_coach"
                            />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="coach" value="Assistant Coach" />
                            <TextInput
                                id="coach"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.coach"
                                :disabled="mode === 'detail'"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.coach"
                            />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="id_manager" value="Manager" />
                            <select
                                id="id_manager"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                v-model="form.id_manager"
                                :disabled="mode === 'detail'"
                            >
                                <option
                                    value=""
                                    selected
                                    readonly
                                    v-if="managers.length == 0"
                                >
                                    There is no manager availables
                                </option>
                                <option
                                    v-for="m in managers"
                                    :key="m.id"
                                    :value="m.id"
                                >
                                    {{ m.name }}
                                </option>
                            </select>
                            <InputError
                                class="mt-2"
                                :message="form.errors.id_manager"
                            />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="logo" value="Team Logo" />
                            <input
                                id="logo"
                                type="file"
                                class="file-input file-input-bordered w-full"
                                @change="handleLogoUpload"
                                accept="image/*"
                                :disabled="mode === 'detail'"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.logo"
                            />
                            <label v-if="form.logo" class="label">
                                <span class="label-text-alt">
                                    Selected: {{ form.logo.name }}
                                </span>
                            </label>
                        </div>

                        <hr class="my-5" />
                        <h3 class="text-xl font-semibold mb-4">Players</h3>

                        <!-- Table Players -->
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th class="w-12">#</th>
                                        <th>Player Name</th>
                                        <th>Position</th>
                                        <th>Jersey Number</th>
                                        <th>Birthdate</th>
                                        <th
                                            v-if="mode !== 'detail'"
                                            class="w-16"
                                        >
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(player, index) in players"
                                        :key="index"
                                    >
                                        <td>{{ index + 1 }}</td>

                                        <!-- Name -->
                                        <td v-if="mode === 'detail'">
                                            {{ player.name }}
                                        </td>
                                        <td v-else>
                                            <TextInput
                                                :id="`player_name_${index}`"
                                                type="text"
                                                class="w-full"
                                                v-model="player.name"
                                                required
                                            />
                                        </td>

                                        <!-- Position -->
                                        <td v-if="mode === 'detail'">
                                            {{ player.position }}
                                        </td>
                                        <td v-else>
                                            <select
                                                :id="`player_position_${index}`"
                                                v-model="player.position"
                                                class="w-full select rounded p-2"
                                                required
                                            >
                                                <option disabled value="">
                                                    -- Select Position --
                                                </option>
                                                <option value="pitcher">
                                                    Pitcher
                                                </option>
                                                <option value="catcher">
                                                    Catcher
                                                </option>
                                                <option value="infielder">
                                                    Infielder
                                                </option>
                                                <option value="outfielder">
                                                    Outfielder
                                                </option>
                                                <option value="utility">
                                                    Utility
                                                </option>
                                            </select>
                                        </td>

                                        <!-- Jersey Number -->
                                        <td v-if="mode === 'detail'">
                                            #{{ player.number }}
                                        </td>
                                        <td v-else>
                                            <TextInput
                                                :id="`player_number_${index}`"
                                                type="number"
                                                class="w-full"
                                                v-model="player.number"
                                                required
                                                :class="{ 'border-red-500': isNumberTaken(player.number, index) }"
                                            />
                                            <span
                                                v-if="isNumberTaken(player.number, index)"
                                                class="text-xs text-red-500"
                                            >
                                                Number already taken!
                                            </span>
                                        </td>

                                        <!-- Birthdate -->
                                        <td v-if="mode === 'detail'">
                                            {{ player.birthdate }}
                                        </td>
                                        <td v-else>
                                            <TextInput
                                                :id="`player_birthdate_${index}`"
                                                type="date"
                                                class="w-full"
                                                v-model="player.birthdate"
                                            />
                                        </td>

                                        <!-- Action -->
                                        <td v-if="mode !== 'detail'">
                                            <button
                                                type="button"
                                                class="btn btn-xs btn-error text-white"
                                                @click="removePlayer(index)"
                                            >
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Add Player -->
                        <div v-if="mode !== 'detail'" class="mt-4 text-center">
                            <button
                                type="button"
                                @click="addPlayer"
                                class="btn btn-outline btn-info"
                            >
                                Add Player
                            </button>
                        </div>

                        <!-- Submit -->
                        <div
                            v-if="mode !== 'detail'"
                            class="flex justify-center mt-8"
                        >
                            <button
                                type="submit"
                                class="btn btn-primary btn-lg"
                            >
                                {{
                                    mode === "create"
                                        ? "Create Team & Players"
                                        : "Update Team & Players"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
