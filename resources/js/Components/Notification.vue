<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
    message: String,
    type: { type: String, default: "success" },
    duration: { type: Number, default: 3000 },
    position: { type: String, default: "top-right" },
});

const visible = ref(true);

onMounted(() => {
    setTimeout(() => {
        visible.value = false;
    }, props.duration);
});

const positionClasses = {
    "top-right": "top-4 right-4",
    "top-left": "top-4 left-4",
    "bottom-right": "bottom-4 right-4",
    "bottom-left": "bottom-4 left-4",
    "top-center": "top-4 left-1/2 -translate-x-1/2",
    "bottom-center": "bottom-4 left-1/2 -translate-x-1/2",
};

const typeClasses = {
    success: "bg-green-50 border-green-200 text-green-800",
    error: "bg-red-50 border-red-200 text-red-800",
    info: "bg-blue-50 border-blue-200 text-blue-800",
    warning: "bg-yellow-50 border-yellow-200 text-yellow-800",
};

const iconClasses = {
    success: "text-green-500",
    error: "text-red-500",
    info: "text-blue-500",
    warning: "text-yellow-500",
};
</script>
<template>
    <transition name="fade">
        <div
            v-if="visible"
            class="fixed p-4 rounded-lg shadow-lg border-l-4 z-50 flex items-center space-x-3 transform transition-all duration-300 ease-out"
            :class="[typeClasses[type], positionClasses[position]]"
        >
            <div :class="iconClasses[type]">
                <svg
                    v-if="type === 'success'"
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <svg
                    v-if="type === 'error'"
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <svg
                    v-if="type === 'info'"
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <svg
                    v-if="type === 'warning'"
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                    ></path>
                </svg>
            </div>
            <span class="font-medium">{{ message }}</span>
        </div>
    </transition>
</template>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s, transform 0.5s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-20px) scale(0.9);
}
.fade-leave-to {
    transform: translateY(20px) scale(0.9);
}
</style>
