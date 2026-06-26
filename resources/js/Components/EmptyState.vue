<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

withDefaults(defineProps<{
    icon?: string;
    title: string;
    description: string;
    actionLabel?: string;
    actionRoute?: string;
}>(), {
    icon: '📭',
});
</script>

<template>
    <div class="flex flex-col items-center justify-center rounded-xl bg-white py-16 px-6 text-center shadow-sm">
        <div class="mb-4 text-5xl">{{ icon }}</div>
        <h3 class="mb-2 text-lg font-semibold text-gray-800">{{ title }}</h3>
        <p class="mb-6 max-w-sm text-sm text-gray-500">{{ description }}</p>
        <Link
            v-if="actionLabel && actionRoute"
            :href="actionRoute"
            class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            {{ actionLabel }}
        </Link>
    </div>
</template>
