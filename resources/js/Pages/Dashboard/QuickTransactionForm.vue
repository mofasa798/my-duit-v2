<script setup lang="ts">
import type { Category } from '@/types';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    categories: Category[];
}>();

const form = useForm({
    category_id: '',
    amount: '',
    description: '',
    date: new Date().toISOString().slice(0, 10),
});

function submit() {
    form.post(route('transactions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('amount', 'description');
        },
    });
}
</script>

<template>
    <div class="mb-6 rounded-xl bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-base font-semibold text-gray-700">
            Tambah Transaksi Cepat
        </h2>
        <form
            @submit.prevent="submit"
            class="flex flex-col gap-4 sm:flex-row sm:items-end"
        >
            <!-- Date -->
            <div class="flex-1">
                <label
                    for="q_date"
                    class="block text-xs font-medium text-gray-700"
                    >Date</label
                >
                <input
                    id="q_date"
                    v-model="form.date"
                    type="date"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                />
            </div>

            <!-- Category -->
            <div class="flex-1">
                <label
                    for="q_category"
                    class="block text-xs font-medium text-gray-700"
                    >Category</label
                >
                <select
                    id="q_category"
                    v-model="form.category_id"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                >
                    <option value="" disabled>Select category</option>
                    <optgroup label="Income">
                        <option
                            v-for="cat in categories.filter(
                                (c) => c.type === 'income',
                            )"
                            :key="cat.id"
                            :value="cat.id"
                        >
                            {{ cat.name }}
                        </option>
                    </optgroup>
                    <optgroup label="Expense">
                        <option
                            v-for="cat in categories.filter(
                                (c) => c.type === 'expense',
                            )"
                            :key="cat.id"
                            :value="cat.id"
                        >
                            {{ cat.name }}
                        </option>
                    </optgroup>
                </select>
            </div>

            <!-- Amount -->
            <div class="flex-1">
                <label
                    for="q_amount"
                    class="block text-xs font-medium text-gray-700"
                    >Amount</label
                >
                <input
                    id="q_amount"
                    v-model="form.amount"
                    type="number"
                    min="0"
                    step="0.01"
                    placeholder="0"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                />
            </div>

            <!-- Description -->
            <div class="flex-1">
                <label
                    for="q_desc"
                    class="block text-xs font-medium text-gray-700"
                    >Description</label
                >
                <input
                    id="q_desc"
                    v-model="form.description"
                    type="text"
                    placeholder="Notes..."
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>

            <!-- Submit -->
            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="mt-1 inline-flex w-full items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 sm:w-auto"
                >
                    <svg
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        />
                    </svg>
                    {{ form.processing ? 'Menyimpan...' : 'Tambah' }}
                </button>
            </div>
        </form>
    </div>
</template>
