<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';

withDefaults(
    defineProps<{
        title?: string;
        message?: string;
        confirmLabel?: string;
        cancelLabel?: string;
        type?: 'danger' | 'warning';
        open: boolean;
    }>(),
    {
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin?',
        confirmLabel: 'Ya, Hapus',
        cancelLabel: 'Batal',
        type: 'danger',
    },
);

const emit = defineEmits<{
    (e: 'confirm'): void;
    (e: 'cancel'): void;
}>();

function onKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') emit('cancel');
}

onMounted(() => document.addEventListener('keydown', onKeydown));
onUnmounted(() => document.removeEventListener('keydown', onKeydown));
</script>

<template>
    <Teleport to="body">
        <Transition name="dialog">
            <div
                v-if="open"
                class="fixed inset-0 z-50 flex items-center justify-center"
                role="dialog"
                aria-modal="true"
            >
                <!-- Overlay -->
                <div
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    @click="emit('cancel')"
                ></div>

                <!-- Dialog box -->
                <div
                    class="relative z-10 mx-4 w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                >
                    <!-- Icon -->
                    <div
                        :class="
                            type === 'danger'
                                ? 'bg-red-100 text-red-600'
                                : 'bg-yellow-100 text-yellow-600'
                        "
                        class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full text-2xl"
                    >
                        {{ type === 'danger' ? '🗑️' : '⚠️' }}
                    </div>

                    <!-- Title -->
                    <h3
                        class="mb-2 text-center text-lg font-bold text-gray-800"
                    >
                        {{ title }}
                    </h3>
                    <p class="mb-6 text-center text-sm text-gray-500">
                        {{ message }}
                    </p>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button
                            type="button"
                            class="flex-1 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300"
                            @click="emit('cancel')"
                        >
                            {{ cancelLabel }}
                        </button>
                        <button
                            type="button"
                            :class="
                                type === 'danger'
                                    ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
                                    : 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400'
                            "
                            class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                            @click="emit('confirm')"
                        >
                            {{ confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.dialog-enter-active,
.dialog-leave-active {
    transition: opacity 0.2s ease;
}
.dialog-enter-active .relative,
.dialog-leave-active .relative {
    transition:
        transform 0.2s ease,
        opacity 0.2s ease;
}
.dialog-enter-from {
    opacity: 0;
}
.dialog-enter-from .relative {
    transform: scale(0.9);
    opacity: 0;
}
.dialog-leave-to {
    opacity: 0;
}
.dialog-leave-to .relative {
    transform: scale(0.9);
    opacity: 0;
}
</style>
