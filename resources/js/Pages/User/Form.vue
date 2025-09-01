<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";

function generateRandomPassword(length = 12) {
    const chars =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}

const props = defineProps({
    roles: Array,
    user: Object, // user prop for edit/detail
    mode: {
        type: String,
        default: "create", // "create", "edit", "detail"
    },
});

const isDetail = computed(() => props.mode === "detail");
const isEdit = computed(() => props.mode === "edit");

const form = useForm({
    name: props.user?.name || "",
    email: props.user?.email || "",
    password: props.user ? "" : generateRandomPassword(),
    password_confirmation: "",
    role: props.user?.role || "",
});

const randomizePassword = () => {
    if (!props.user) {
        const newPass = generateRandomPassword();
        form.password = newPass;
        form.password_confirmation = newPass;
    }
};

const submit = () => {
    if (isEdit.value) {
        form.put(route("user.update", props.user.id), {
            onFinish: () => form.reset("password", "password_confirmation"),
        });
    } else {
        form.post(route("user.store"), {
            onFinish: () => form.reset("password", "password_confirmation"),
        });
    }
};

onMounted(() => {
    randomizePassword();
});
</script>

<template>
    <AuthenticatedLayout>
        <Head
            :title="
                isEdit ? 'Edit User' : isDetail ? 'User Detail' : 'Create User'
            "
        />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{
                    isEdit
                        ? "Edit User"
                        : isDetail
                        ? "User Detail"
                        : "Create New User"
                }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form v-if="!isDetail" @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    :readonly="isDetail"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.name"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    :readonly="isDetail"
                                    required
                                    autocomplete="username"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.email"
                                />
                            </div>

                            <div class="mb-4" v-if="!props.user">
                                <InputLabel for="password" value="Password" />
                                <TextInput
                                    id="password"
                                    type="text"
                                    readonly
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.password"
                                />
                            </div>

                            <div class="mb-4" v-if="!props.user">
                                <InputLabel
                                    for="password_confirmation"
                                    value="Confirm Password"
                                />
                                <TextInput
                                    id="password_confirmation"
                                    type="text"
                                    readonly
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.password_confirmation"
                                />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="role" value="Role" />
                                <select
                                    id="role"
                                    class="select select-bordered w-full mt-1"
                                    v-model="form.role"
                                    :disabled="isDetail"
                                    required
                                >
                                    <option disabled value="">
                                        Select a role
                                    </option>
                                    <option
                                        v-for="(roleName, roleKey) in roles"
                                        :key="roleKey"
                                        :value="roleKey"
                                    >
                                        {{ roleName }}
                                    </option>
                                </select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.role"
                                />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    {{ isEdit ? "Update User" : "Create User" }}
                                </PrimaryButton>
                            </div>
                        </form>
                        <div v-else>
                            <div class="mb-4">
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :model-value="form.name"
                                    readonly
                                />
                            </div>
                            <div class="mb-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    :model-value="form.email"
                                    readonly
                                />
                            </div>
                            <div class="mb-4">
                                <InputLabel for="role" value="Role" />
                                <TextInput
                                    id="role"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :model-value="roles[form.role]"
                                    readonly
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
