<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import type { Category, Transaction } from '@/types';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTransactions {
    data: Transaction[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
}

const props = defineProps<{
    transactions: PaginatedTransactions;
    categories: Category[];
    filters: {
        search?: string;
        type?: string;
        category_id?: string;
        date_from?: string;
        date_to?: string;
    };
}>();

const search = ref(props.filters.search ?? '');
const type = ref(props.filters.type ?? '');
const categoryId = ref(props.filters.category_id ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    router.get(
        route('transactions.index'),
        {
            search: search.value || undefined,
            type: type.value || undefined,
            category_id: categoryId.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([type, categoryId, dateFrom, dateTo], applyFilters);

// ConfirmDialog state
const confirmOpen = ref(false);
const pendingDeleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    pendingDeleteId.value = id;
    confirmOpen.value = true;
}

function onConfirmDelete() {
    if (pendingDeleteId.value !== null) {
        router.delete(route('transactions.destroy', pendingDeleteId.value));
    }
    confirmOpen.value = false;
    pendingDeleteId.value = null;
}

function onCancelDelete() {
    confirmOpen.value = false;
    pendingDeleteId.value = null;
}

function formatCurrency(amount: string | number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(amount));
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Transactions" />

    <!-- Confirm delete dialog -->
    <ConfirmDialog
        :open="confirmOpen"
        title="Hapus Transaksi"
        message="Yakin ingin menghapus transaksi ini? Tindakan ini tidak bisa dibatalkan."
        confirm-label="Ya, Hapus"
        cancel-label="Batal"
        type="danger"
        @confirm="onConfirmDelete"
        @cancel="onCancelDelete"
    />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold leading-tight text-gray-800">
                    Transactions
                </h1>
                <Link
                    :href="route('transactions.create')"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    + Add Transaction
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Filters -->
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        <!-- Search -->
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search description..."
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />

                        <!-- Type filter -->
                        <select
                            v-model="type"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            <option value="">All Types</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>

                        <!-- Category filter -->
                        <select
                            v-model="categoryId"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            <option value="">All Categories</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.name }} ({{ cat.type }})
                            </option>
                        </select>

                        <!-- Date From -->
                        <input
                            v-model="dateFrom"
                            type="date"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />

                        <!-- Date To -->
                        <input
                            v-model="dateTo"
                            type="date"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                    </div>
                </div>

                <!-- Table or Empty State -->
                <div v-if="transactions.data.length > 0" class="overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="tx in transactions.data"
                                    :key="tx.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ formatDate(tx.date) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ tx.description ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ tx.category?.name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            :class="tx.category?.type === 'income'
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-red-100 text-red-700'"
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                        >
                                            {{ tx.category?.type ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold"
                                        :class="tx.category?.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                    >
                                        {{ formatCurrency(tx.amount) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-3">
                                            <Link
                                                :href="route('transactions.edit', tx.id)"
                                                class="font-medium text-indigo-600 transition hover:text-indigo-800"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="font-medium text-red-600 transition hover:text-red-800"
                                                @click="confirmDelete(tx.id)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="transactions.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-6 py-3">
                        <p class="text-sm text-gray-600">
                            Showing {{ transactions.from }}–{{ transactions.to }} of {{ transactions.total }}
                        </p>
                        <div class="flex gap-1">
                            <template v-for="link in transactions.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="rounded px-3 py-1 text-sm"
                                    :class="link.active
                                        ? 'bg-indigo-600 text-white'
                                        : 'text-gray-600 hover:bg-gray-100'"
                                    preserve-scroll
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="rounded px-3 py-1 text-sm text-gray-400"
                                />
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <EmptyState
                    v-else
                    icon="💰"
                    title="Belum ada transaksi"
                    description="Belum ada transaksi yang sesuai filter. Coba ubah filter atau tambahkan transaksi baru."
                    action-label="+ Add Transaction"
                    :action-route="route('transactions.create')"
                />

            </div>
        </div>
    </AuthenticatedLayout>
</template>
