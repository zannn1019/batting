<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, ref, useId } from "vue";

defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const selectedUser = ref(null);

function deleteUser(userId) {
    router.delete(route("user.destroy", userId), {
        onSuccess: () => {
            selectedUser.value = null;
        },
        onError: () => {},
    });
}
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Users
                </h2>
                <Link class="btn btn-neutral" :href="route('user.create')">
                    Add User
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
                                    <th class="px-4 py-2 border">Name</th>
                                    <th class="px-4 py-2 border">Email</th>
                                    <th class="px-4 py-2 border">Role</th>
                                    <th class="px-4 py-2 border">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(user, i) in users"
                                    :key="user.id"
                                    class="text-center"
                                >
                                    <td class="px-4 py-2 border">
                                        {{ i + 1 }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ user.name }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-4 py-2 border capitalize">
                                        {{ user.role }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <Link
                                            class="btn btn-sm btn-info mr-2 text-white"
                                            :href="route('user.show', user.id)"
                                        >
                                            Detail
                                        </Link>
                                        <Link
                                            class="btn btn-sm btn-primary mr-2"
                                            :href="route('user.edit', user.id)"
                                        >
                                            Edit
                                        </Link>
                                        <label
                                            for="delete-modal"
                                            class="btn btn-sm btn-error text-white cursor-pointer"
                                            @click="selectedUser = user"
                                        >
                                            Delete
                                        </label>

                                        <input
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
                                                    <span
                                                        class="font-semibold"
                                                        >{{
                                                            selectedUser?.name
                                                        }}</span
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
                                                            deleteUser(
                                                                selectedUser.id
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
