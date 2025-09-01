<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    teams: Array,
});

const teams = computed(() => props.teams || []);
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
                                    <th class="px-4 py-2 border">Players</th>
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
                                        <ul
                                            v-if="
                                                team.players &&
                                                team.players.length
                                            "
                                            class="list-disc pl-5"
                                        >
                                            <li
                                                v-for="p in team.players"
                                                :key="p.id"
                                            >
                                                {{ p.name }} (#{{ p.jersey }})
                                            </li>
                                        </ul>
                                        <span v-else class="text-gray-400">
                                            No players
                                        </span>
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
