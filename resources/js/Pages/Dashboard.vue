<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import TransactionTable from './Dashboard/TransactionTable.vue';
import QuickTransactionForm from './Dashboard/QuickTransactionForm.vue';
import type { Category, Transaction } from '@/types';

defineProps<{
    transactions: Transaction[];
    categories: Category[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Quick Form -->
                <QuickTransactionForm :categories="categories" />

                <!-- AG Grid Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <TransactionTable
                        :transactions="transactions"
                        :categories="categories"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
