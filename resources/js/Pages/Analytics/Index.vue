<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import SpendingChart from '@/Components/SpendingChart.vue';

const props = defineProps<{
    summary: { totalIncome: number; totalExpense: number; balance: number };
    categoryBreakdown: Record<string, { amount: number; type: string }>;
    trends: Record<string, { income: number; expense: number }>;
    filters: { date_from: string; date_to: string };
}>();

const dateFrom = ref(props.filters.date_from);
const dateTo = ref(props.filters.date_to);

const applyFilters = () => {
    router.get(route('analytics.index'), {
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Spending Analytics
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Filters -->
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 flex items-center space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date From</label>
                        <input type="date" v-model="dateFrom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date To</label>
                        <input type="date" v-model="dateTo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>
                    <div class="pt-5">
                        <button @click="applyFilters" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500 truncate">Total Income</div>
                            <div class="mt-1 text-3xl font-semibold text-green-600">Rp {{ summary.totalIncome.toLocaleString('id-ID') }}</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500 truncate">Total Expense</div>
                            <div class="mt-1 text-3xl font-semibold text-red-600">Rp {{ summary.totalExpense.toLocaleString('id-ID') }}</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500 truncate">Net Balance</div>
                            <div class="mt-1 text-3xl font-semibold" :class="summary.balance >= 0 ? 'text-green-600' : 'text-red-600'">
                                Rp {{ summary.balance.toLocaleString('id-ID') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <SpendingChart 
                    :categoryBreakdown="categoryBreakdown"
                    :trends="trends"
                    :summary="summary"
                />

            </div>
        </div>
    </AuthenticatedLayout>
</template>
