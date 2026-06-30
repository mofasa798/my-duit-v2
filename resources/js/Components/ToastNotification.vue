<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

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

const { toasts, show, dismiss } = useToast();

// Watch Inertia flash messages
const page = usePage<{
    auth: { user: { id: number; name: string; email: string } };
    flash?: {
        success?: string;
        error?: string;
        warning?: string;
        info?: string;
    };
}>();

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
