<script setup lang="ts">
import EmptyState from '@/Components/EmptyState.vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Category, Transaction } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import QuickTransactionForm from './Dashboard/QuickTransactionForm.vue';
import TransactionTable from './Dashboard/TransactionTable.vue';

const props = defineProps<{
    transactions: Transaction[];
    categories: Category[];
}>();

const loading = ref(true);
onMounted(() => {
    // Simulate brief loading state so skeleton is visible on navigation
    setTimeout(() => {
        loading.value = false;
    }, 400);
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-2xl font-bold leading-tight text-gray-800">
                Dashboard
            </h1>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Skeleton while loading -->
                <template v-if="loading">
                    <SkeletonLoader type="card" :count="1" />
                    <SkeletonLoader type="table" :count="5" />
                </template>

                <template v-else>
                    <!-- Quick Form -->
                    <QuickTransactionForm :categories="categories" />

                    <!-- Transaction Table or Empty State -->
                    <div
                        v-if="transactions.length > 0"
                        class="overflow-hidden rounded-xl bg-white shadow-sm"
                    >
                        <div class="overflow-x-auto">
                            <TransactionTable
                                :transactions="transactions"
                                :categories="categories"
                            />
                        </div>
                    </div>
                    <EmptyState
                        v-else
                        icon="💸"
                        title="Belum ada transaksi"
                        description="Tambahkan transaksi pertamamu menggunakan form di atas!"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
