<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    managers: Array,
});

const players = ref([]);

function addPlayer() {
    players.value.push({
        name: "",
        position: "",
        number: "",
        birthdate: "",
    });
}

function removePlayer(index) {
    players.value.splice(index, 1);
}

const form = useForm({
    team_name: "",
    head_coach: "",
    coach: "",
    id_manager: usePage().props.auth.user.id,
    logo: null,
    players: [],
});

function handleLogoUpload(event) {
    form.logo = event.target.files[0];
}

function submit() {
    form.players = players.value;
    form.post(route("team.store"), {
        forceFormData: true,
        onSuccess: () => {
            players.value = [];
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex flex-col">
                    <h2
                        class="text-xl font-semibold leading-tight text-gray-800"
                    >
                        Create New Team
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Create your teams and players here.
                    </p>
                </div>
            </div>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
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
                                required
                                autofocus
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
                                required
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.head_coach"
                            />
                        </div>

                        <div class="mb-4">
                            <InputLabel
                                for="coach"
                                value="Assistant Coach (Optional)"
                            />
                            <TextInput
                                id="coach"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.coach"
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
                                required
                            >
                                <option disabled value="">
                                    Select a manager
                                </option>
                                <option
                                    v-for="manager in managers"
                                    :key="manager.id"
                                    :value="manager.id"
                                >
                                    {{ manager.name }}
                                </option>
                            </select>
                            <InputError
                                class="mt-2"
                                :message="form.errors.id_manager"
                            />
                        </div>

                        <div class="form-control mb-4">
                            <InputLabel
                                for="logo"
                                value="Team Logo"
                                class="mb-2"
                            />
                            <input
                                id="logo"
                                type="file"
                                class="file-input file-input-bordered w-full"
                                @change="handleLogoUpload"
                                accept="image/*"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.logo"
                            />
                            <label v-if="form.logo" class="label">
                                <span class="label-text-alt"
                                    >Selected: {{ form.logo.name }}</span
                                >
                            </label>
                        </div>

                        <hr class="my-5" />

                        <h3 class="text-xl font-semibold mb-4">
                            Player Information
                        </h3>

                        <!-- Player Form Repeater -->
                        <div
                            v-for="(player, index) in players"
                            :key="index"
                            class="mb-6 border border-gray-200 p-4 rounded-lg relative"
                        >
                            <button
                                type="button"
                                @click="removePlayer(index)"
                                class="btn btn-sm btn-circle btn-error absolute top-2 right-2 text-white"
                                title="Remove Player"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>

                            <h4 class="text-lg font-medium mb-3">
                                Player {{ index + 1 }}
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <InputLabel
                                        :for="`player_name_${index}`"
                                        value="Player Name"
                                    />
                                    <TextInput
                                        :id="`player_name_${index}`"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="player.name"
                                        required
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors[`players.${index}.name`]
                                        "
                                    />
                                </div>

                                <div class="mb-4">
                                    <InputLabel
                                        :for="`player_position_${index}`"
                                        value="Position"
                                    />
                                    <TextInput
                                        :id="`player_position_${index}`"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="player.position"
                                        required
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors[
                                                `players.${index}.position`
                                            ]
                                        "
                                    />
                                </div>

                                <div class="mb-4">
                                    <InputLabel
                                        :for="`player_number_${index}`"
                                        value="Jersey Number"
                                    />
                                    <TextInput
                                        :id="`player_number_${index}`"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="player.number"
                                        required
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors[
                                                `players.${index}.number`
                                            ]
                                        "
                                    />
                                </div>

                                <div class="mb-4">
                                    <InputLabel
                                        :for="`player_birthdate_${index}`"
                                        value="Birthdate"
                                    />
                                    <TextInput
                                        :id="`player_birthdate_${index}`"
                                        type="date"
                                        class="mt-1 block w-full"
                                        v-model="player.birthdate"
                                        required
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors[
                                                `players.${index}.birthdate`
                                            ]
                                        "
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <button
                                type="button"
                                @click="addPlayer"
                                class="btn btn-outline btn-info"
                            >
                                Add Player
                            </button>
                        </div>

                        <hr class="my-5" />
                        <div class="flex justify-center mt-8">
                            <button
                                type="submit"
                                class="btn btn-primary btn-lg"
                            >
                                Create Team & Players
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
