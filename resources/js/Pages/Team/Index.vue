<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    teams: Array,
});

const teams = computed(() => props.teams || []);
const isModalOpen = ref(false);
const selectedTeam = ref(null);

function deleteTeam(id) {
    router.delete(route("team.destroy", id), {
        onSuccess: () => {
            selectedTeam.value = null;
            isModalOpen.value = false;
        },
        onError: () => {
            isModalOpen.value = false;
        },
    });
}
</script>

<template>
    <Head title="Teams" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex flex-col">
                    <h2
                        class="text-xl font-semibold leading-tight text-gray-800"
                    >
                        Teams
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage your teams and players here.
                    </p>
                </div>
                <Link class="btn btn-neutral" :href="route('team.create')">
                    Add Team
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
                                    <th class="px-4 py-2 border">Team Name</th>
                                    <th class="px-4 py-2 border">Head Coach</th>
                                    <th class="px-4 py-2 border">Coach</th>
                                    <th class="px-4 py-2 border">
                                        Total Players
                                    </th>
                                    <th class="px-4 py-2 border">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(team, i) in teams"
                                    :key="team.id"
                                    class="align-top"
                                >
                                    <td class="px-4 py-2 border text-center">
                                        {{ i + 1 }}
                                    </td>
                                    <td class="px-4 py-2 border font-semibold">
                                        {{ team.team_name }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ team.head_coach }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ team.coach }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <div
                                            v-if="
                                                team.players &&
                                                team.players.length
                                            "
                                            class="list-disc pl-5"
                                        >
                                            {{ team.players.length }}
                                        </div>
                                        <span v-else class="text-gray-400">
                                            No players
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-2 border whitespace-nowrap items-center justify-center flex"
                                    >
                                        <Link
                                            class="btn btn-sm btn-info mr-2 text-white"
                                            :href="route('team.show', team.id)"
                                        >
                                            Detail
                                        </Link>
                                        <Link
                                            class="btn btn-sm btn-primary mr-2"
                                            :href="route('team.edit', team.id)"
                                        >
                                            Edit
                                        </Link>
                                        <label
                                            for="delete-modal"
                                            class="btn btn-sm btn-error text-white cursor-pointer"
                                            @click="selectedTeam = team"
                                        >
                                            Delete
                                        </label>

                                        <input
                                            v-model="isModalOpen"
                                            type="checkbox"
                                            id="delete-modal"
                                            class="modal-toggle"
                                        />

                                        <div class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">
                                                    Confirm Delete
                                                </h3>
                                                <p class="py-4">
                                                    Are you sure you want to
                                                    delete
                                                    <span class="font-semibold">
                                                        {{
                                                            selectedTeam?.team_name
                                                        }} </span
                                                    >?
                                                </p>
                                                <div class="modal-action">
                                                    <label
                                                        for="delete-modal"
                                                        class="btn"
                                                        >Cancel</label
                                                    >
                                                    <button
                                                        class="btn btn-error text-white"
                                                        @click="
                                                            deleteTeam(
                                                                selectedTeam.id
                                                            )
                                                        "
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
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
