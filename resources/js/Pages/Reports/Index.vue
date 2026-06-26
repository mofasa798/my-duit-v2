<script setup lang="ts">
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import EmptyState from '@/Components/EmptyState.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    reports: Array<{
        id: number;
        start_date: string;
        end_date: string;
        total_income: string | number;
        total_expense: string | number;
        balance: string | number;
        created_at: string;
    }>;
}>();

const form = useForm({
    start_date: '',
    end_date: '',
});

const generateReport = () => {
    form.post(route('reports.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// ConfirmDialog state
const confirmOpen = ref(false);
const pendingDeleteId = ref<number | null>(null);

const deleteReport = (id: number) => {
    pendingDeleteId.value = id;
    confirmOpen.value = true;
};

const onConfirmDelete = () => {
    if (pendingDeleteId.value !== null) {
        router.delete(route('reports.destroy', pendingDeleteId.value), {
            preserveScroll: true,
        });
    }
    confirmOpen.value = false;
    pendingDeleteId.value = null;
};

const onCancelDelete = () => {
    confirmOpen.value = false;
    pendingDeleteId.value = null;
};

const formatCurrency = (value: string | number) => {
    return 'Rp ' + Number(value).toLocaleString('id-ID');
};

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="Reports" />

    <!-- Confirm delete dialog -->
    <ConfirmDialog
        :open="confirmOpen"
        title="Hapus Report"
        message="Yakin ingin menghapus report ini? Tindakan ini tidak bisa dibatalkan."
        confirm-label="Ya, Hapus"
        cancel-label="Batal"
        type="danger"
        @confirm="onConfirmDelete"
        @cancel="onCancelDelete"
    />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-2xl font-bold leading-tight text-gray-800">
                Financial Reports
            </h1>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Generate Report Form -->
                <div class="rounded-xl bg-white p-5 shadow-sm print:hidden">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800">
                        Generate New Report
                    </h2>
                    <form
                        @submit.prevent="generateReport"
                        class="flex flex-col gap-4 sm:flex-row sm:items-end"
                    >
                        <div class="flex-1">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Start Date</label
                            >
                            <input
                                type="date"
                                v-model="form.start_date"
                                required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                :class="{
                                    'border-red-500': form.errors.start_date,
                                }"
                            />
                            <p
                                v-if="form.errors.start_date"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.start_date }}
                            </p>
                        </div>
                        <div class="flex-1">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >End Date</label
                            >
                            <input
                                type="date"
                                v-model="form.end_date"
                                required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                :class="{
                                    'border-red-500': form.errors.end_date,
                                }"
                            />
                            <p
                                v-if="form.errors.end_date"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.end_date }}
                            </p>
                        </div>
                        <div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
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
                                {{
                                    form.processing
                                        ? 'Generating...'
                                        : 'Generate'
                                }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Report History -->
                <div
                    v-if="reports.length > 0"
                    class="overflow-hidden rounded-xl bg-white shadow-sm"
                >
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Report History
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Date Range
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Total Income
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Total Expense
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Balance
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 print:hidden"
                                    >
                                        Generated On
                                    </th>
                                    <th
                                        scope="col"
                                        class="relative px-6 py-3 print:hidden"
                                    >
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="report in reports"
                                    :key="report.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"
                                    >
                                        {{ report.start_date }} to
                                        {{ report.end_date }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-sm text-green-600"
                                    >
                                        {{
                                            formatCurrency(report.total_income)
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-sm text-red-600"
                                    >
                                        {{
                                            formatCurrency(report.total_expense)
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-sm font-medium"
                                        :class="
                                            Number(report.balance) >= 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{ formatCurrency(report.balance) }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 print:hidden"
                                    >
                                        {{
                                            new Date(
                                                report.created_at,
                                            ).toLocaleDateString()
                                        }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium print:hidden"
                                    >
                                        <div
                                            class="flex items-center justify-end gap-3"
                                        >
                                            <a
                                                :href="
                                                    route('reports.export', {
                                                        report: report.id,
                                                        format: 'xlsx',
                                                    })
                                                "
                                                class="text-indigo-600 transition hover:text-indigo-900"
                                                >XLSX</a
                                            >
                                            <a
                                                :href="
                                                    route('reports.export', {
                                                        report: report.id,
                                                        format: 'csv',
                                                    })
                                                "
                                                class="text-indigo-600 transition hover:text-indigo-900"
                                                >CSV</a
                                            >
                                            <a
                                                :href="
                                                    route('reports.export', {
                                                        report: report.id,
                                                        format: 'pdf',
                                                    })
                                                "
                                                target="_blank"
                                                class="text-indigo-600 transition hover:text-indigo-900"
                                                >PDF</a
                                            >
                                            <button
                                                @click="printReport"
                                                class="text-gray-600 transition hover:text-gray-900"
                                            >
                                                Print
                                            </button>
                                            <button
                                                @click="deleteReport(report.id)"
                                                class="font-medium text-red-600 transition hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <EmptyState
                    v-else
                    icon="📋"
                    title="Belum ada report"
                    description="Generate report keuangan pertamamu dengan mengisi form di atas."
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
