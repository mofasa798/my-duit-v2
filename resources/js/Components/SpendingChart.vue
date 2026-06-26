<script setup lang="ts">
import type { ApexOptions } from 'apexcharts';
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps<{
    categoryBreakdown: Record<string, { amount: number; type: string }>;
    trends: Record<string, { income: number; expense: number }>;
    summary: { totalIncome: number; totalExpense: number; balance: number };
}>();

// Pie Chart (Expense by Category)
const pieChartOptions = computed<ApexOptions>(() => {
    const categories = Object.keys(props.categoryBreakdown).filter(
        (k) =>
            props.categoryBreakdown[k].type === 'expense' &&
            props.categoryBreakdown[k].amount > 0,
    );
    return {
        chart: { type: 'pie' },
        labels: categories,
        theme: { palette: 'palette2' },
        title: { text: 'Expense by Category' },
    };
});
const pieChartSeries = computed(() => {
    return Object.keys(props.categoryBreakdown)
        .filter(
            (k) =>
                props.categoryBreakdown[k].type === 'expense' &&
                props.categoryBreakdown[k].amount > 0,
        )
        .map((k) => props.categoryBreakdown[k].amount);
});

// Bar Chart (Income vs Expense)
const barChartOptions = computed<ApexOptions>(() => ({
    chart: { type: 'bar' },
    xaxis: { categories: ['Income', 'Expense'] },
    colors: ['#10B981', '#EF4444'],
    plotOptions: {
        bar: { distributed: true, borderRadius: 4 },
    },
    title: { text: 'Income vs Expense' },
}));
const barChartSeries = computed(() => [
    {
        name: 'Amount',
        data: [props.summary.totalIncome, props.summary.totalExpense],
    },
]);

// Line Chart (Spending Trends)
const lineChartOptions = computed<ApexOptions>(() => ({
    chart: { type: 'line', toolbar: { show: false } },
    xaxis: { categories: Object.keys(props.trends) },
    stroke: { curve: 'smooth' },
    colors: ['#10B981', '#EF4444'],
    title: { text: 'Daily Trends' },
}));
const lineChartSeries = computed(() => [
    {
        name: 'Income',
        data: Object.values(props.trends).map((t: any) => t.income),
    },
    {
        name: 'Expense',
        data: Object.values(props.trends).map((t: any) => t.expense),
    },
]);
</script>

<template>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Pie Chart -->
            <div class="rounded-lg border border-gray-100 bg-white p-4 shadow">
                <VueApexCharts
                    type="pie"
                    height="350"
                    :options="pieChartOptions"
                    :series="pieChartSeries"
                />
            </div>

            <!-- Bar Chart -->
            <div class="rounded-lg border border-gray-100 bg-white p-4 shadow">
                <VueApexCharts
                    type="bar"
                    height="350"
                    :options="barChartOptions"
                    :series="barChartSeries"
                />
            </div>
        </div>

        <!-- Line Chart -->
        <div class="rounded-lg border border-gray-100 bg-white p-4 shadow">
            <VueApexCharts
                type="line"
                height="350"
                :options="lineChartOptions"
                :series="lineChartSeries"
            />
        </div>
    </div>
</template>
