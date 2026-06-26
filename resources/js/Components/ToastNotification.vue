<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Toast {
    id: number;
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
    duration: number;
    visible: boolean;
}

const toasts = ref<Toast[]>([]);
let nextId = 0;

const icons: Record<string, string> = {
    success: '✅',
    error: '❌',
    warning: '⚠️',
    info: 'ℹ️',
};

const bgColors: Record<string, string> = {
    success: 'bg-green-50 border-green-400',
    error: 'bg-red-50 border-red-400',
    warning: 'bg-yellow-50 border-yellow-400',
    info: 'bg-blue-50 border-blue-400',
};

const textColors: Record<string, string> = {
    success: 'text-green-800',
    error: 'text-red-800',
    warning: 'text-yellow-800',
    info: 'text-blue-800',
};

export function useToast() {
    function show(type: Toast['type'], message: string, duration = 3000) {
        const id = ++nextId;
        const toast: Toast = { id, type, message, duration, visible: true };
        toasts.value.push(toast);

        setTimeout(() => {
            const t = toasts.value.find((t) => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => {
                toasts.value = toasts.value.filter((t) => t.id !== id);
            }, 400);
        }, duration);
    }

    return { show };
}

function dismiss(id: number) {
    const t = toasts.value.find((t) => t.id === id);
    if (t) t.visible = false;
    setTimeout(() => {
        toasts.value = toasts.value.filter((t) => t.id !== id);
    }, 400);
}

// Watch Inertia flash messages
const page = usePage<{
    flash?: {
        success?: string;
        error?: string;
        warning?: string;
        info?: string;
    };
}>();
const { show } = useToast();

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;
        if (flash.success) show('success', flash.success);
        if (flash.error) show('error', flash.error);
        if (flash.warning) show('warning', flash.warning);
        if (flash.info) show('info', flash.info);
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <Teleport to="body">
        <div
            class="pointer-events-none fixed right-4 top-4 z-[9999] flex flex-col gap-3"
            aria-live="polite"
        >
            <Transition v-for="toast in toasts" :key="toast.id" name="toast">
                <div
                    v-if="toast.visible"
                    :class="[
                        bgColors[toast.type],
                        'pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg',
                    ]"
                    role="alert"
                >
                    <span class="text-lg leading-none">{{
                        icons[toast.type]
                    }}</span>
                    <p
                        :class="[
                            textColors[toast.type],
                            'flex-1 text-sm font-medium',
                        ]"
                    >
                        {{ toast.message }}
                    </p>
                    <button
                        type="button"
                        :class="[
                            textColors[toast.type],
                            'ml-2 opacity-60 transition hover:opacity-100',
                        ]"
                        @click="dismiss(toast.id)"
                        aria-label="Dismiss"
                    >
                        ✕
                    </button>
                </div>
            </Transition>
        </div>
    </Teleport>
</template>

<style scoped>
.toast-enter-active {
    transition: all 0.3s ease;
}
.toast-leave-active {
    transition: all 0.35s ease;
}
.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}
.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
</style>
