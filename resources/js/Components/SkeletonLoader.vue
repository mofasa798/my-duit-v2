<script setup lang="ts">
withDefaults(defineProps<{
    type?: 'card' | 'table' | 'text' | 'chart';
    count?: number;
}>(), {
    type: 'card',
    count: 1,
});
</script>

<template>
    <!-- Card skeleton -->
    <template v-if="type === 'card'">
        <div
            v-for="i in count"
            :key="i"
            class="animate-pulse rounded-xl bg-white p-6 shadow-sm"
        >
            <div class="mb-3 h-4 w-1/3 rounded bg-gray-200"></div>
            <div class="h-8 w-2/3 rounded bg-gray-200"></div>
        </div>
    </template>

    <!-- Table skeleton -->
    <template v-else-if="type === 'table'">
        <div class="animate-pulse overflow-hidden rounded-xl bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <div class="h-4 w-1/4 rounded bg-gray-200"></div>
            </div>
            <div
                v-for="i in count"
                :key="i"
                class="flex gap-6 border-b border-gray-50 px-6 py-4"
            >
                <div class="h-4 w-24 rounded bg-gray-200"></div>
                <div class="h-4 flex-1 rounded bg-gray-200"></div>
                <div class="h-4 w-28 rounded bg-gray-200"></div>
                <div class="h-4 w-20 rounded bg-gray-200"></div>
            </div>
        </div>
    </template>

    <!-- Text skeleton -->
    <template v-else-if="type === 'text'">
        <div class="animate-pulse space-y-2">
            <div
                v-for="i in count"
                :key="i"
                :class="i === count ? 'w-3/4' : 'w-full'"
                class="h-4 rounded bg-gray-200"
            ></div>
        </div>
    </template>

    <!-- Chart skeleton -->
    <template v-else-if="type === 'chart'">
        <div class="animate-pulse rounded-xl bg-white p-6 shadow-sm">
            <div class="mb-4 h-4 w-1/4 rounded bg-gray-200"></div>
            <div class="flex items-end gap-3 h-48">
                <div
                    v-for="i in (count || 7)"
                    :key="i"
                    class="flex-1 rounded-t bg-gray-200"
                    :style="{ height: `${Math.random() * 60 + 30}%` }"
                ></div>
            </div>
        </div>
    </template>
</template>
