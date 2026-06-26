<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import SpendingChart from '@/Components/SpendingChart.vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';
import EmptyState from '@/Components/EmptyState.vue';

const props = defineProps<{
    summary: { totalIncome: number; totalExpense: number; balance: number };
    categoryBreakdown: Record<string, { amount: number; type: string }>;
    trends: Record<string, { income: number; expense: number }>;
    filters: { date_from: string; date_to: string };
}>();

const dateFrom = ref(props.filters.date_from);
const dateTo = ref(props.filters.date_to);

const loading = ref(true);
onMounted(() => setTimeout(() => { loading.value = false; }, 400));

const hasData = computed(() => Object.keys(props.categoryBreakdown).length > 0);

const applyFilters = () => {
    loading.value = true;
    router.get(route('analytics.index'), {
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => { loading.value = false; },
    });
};
</script>

<template>
    <Head title="Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-2xl font-bold leading-tight text-gray-800">
                Spending Analytics
            </h1>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Filters -->
                <div class="flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Date From</label>
                        <input
                            type="date"
                            v-model="dateFrom"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Date To</label>
                        <input
                            type="date"
                            v-model="dateTo"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                    </div>
                    <div>
                        <button
                            @click="applyFilters"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Skeleton while loading -->
                <template v-if="loading">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <SkeletonLoader type="card" :count="1" />
                        <SkeletonLoader type="card" :count="1" />
                        <SkeletonLoader type="card" :count="1" />
                    </div>
                    <SkeletonLoader type="chart" :count="7" />
                </template>

                <template v-else>
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Total Income</div>
                                <div class="mt-2 text-3xl font-bold text-green-600">
                                    Rp {{ summary.totalIncome.toLocaleString('id-ID') }}
                                </div>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Total Expense</div>
                                <div class="mt-2 text-3xl font-bold text-red-600">
                                    Rp {{ summary.totalExpense.toLocaleString('id-ID') }}
                                </div>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Net Balance</div>
                                <div
                                    class="mt-2 text-3xl font-bold"
                                    :class="summary.balance >= 0 ? 'text-green-600' : 'text-red-600'"
                                >
                                    Rp {{ summary.balance.toLocaleString('id-ID') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts or Empty State -->
                    <SpendingChart
                        v-if="hasData"
                        :categoryBreakdown="categoryBreakdown"
                        :trends="trends"
                        :summary="summary"
                    />
                    <EmptyState
                        v-else
                        icon="📊"
                        title="Belum ada data"
                        description="Tidak ada transaksi dalam rentang tanggal ini. Coba ubah filter tanggal."
                    />
                </template>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
