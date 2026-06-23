<script setup lang="ts">
import { ref, watch } from 'vue';
import { AgGridVue } from 'ag-grid-vue3';
import { router } from '@inertiajs/vue3';
import type { ColDef, ValueFormatterParams, ValueSetterParams, CellValueChangedEvent, GridReadyEvent } from 'ag-grid-community';
import 'ag-grid-community/styles/ag-grid.css';
import 'ag-grid-community/styles/ag-theme-quartz.css';
import type { Category, Transaction } from '@/types';

const props = defineProps<{
    transactions: Transaction[];
    categories: Category[];
}>();

// AG Grid setup
const gridApi = ref();

const dateFormatter = (params: ValueFormatterParams) => {
    if (!params.value) return '';
    return new Date(params.value).toLocaleDateString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric'
    });
};

const currencyFormatter = (params: ValueFormatterParams) => {
    if (params.value == null) return '';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(Number(params.value));
};

const categoryFormatter = (params: ValueFormatterParams) => {
    if (!params.value) return '';
    const cat = props.categories.find(c => c.id === Number(params.value));
    return cat ? `${cat.name} (${cat.type})` : '';
};

const categoryEditorParams = {
    values: props.categories.map(c => String(c.id)),
    valueListFormatter: (id: string) => {
        const cat = props.categories.find(c => c.id === Number(id));
        return cat ? `${cat.name} (${cat.type})` : id;
    }
};

const columnDefs = ref<ColDef[]>([
    {
        field: 'date',
        headerName: 'Date',
        sortable: true,
        filter: 'agDateColumnFilter',
        editable: true,
        valueFormatter: dateFormatter,
        cellEditor: 'agDateCellEditor',
        width: 150,
    },
    {
        field: 'description',
        headerName: 'Description',
        sortable: true,
        filter: 'agTextColumnFilter',
        editable: true,
        flex: 1,
    },
    {
        field: 'category_id',
        headerName: 'Category',
        sortable: true,
        filter: 'agSetColumnFilter',
        editable: true,
        cellEditor: 'agSelectCellEditor',
        cellEditorParams: categoryEditorParams,
        valueFormatter: categoryFormatter,
        width: 200,
    },
    {
        field: 'amount',
        headerName: 'Amount',
        sortable: true,
        filter: 'agNumberColumnFilter',
        editable: true,
        valueFormatter: currencyFormatter,
        cellEditor: 'agNumberCellEditor',
        type: 'numericColumn',
        width: 150,
    }
]);

const defaultColDef: ColDef = {
    resizable: true,
};

const onGridReady = (params: GridReadyEvent) => {
    gridApi.value = params.api;
    params.api.sizeColumnsToFit();
};

const onCellValueChanged = (event: CellValueChangedEvent) => {
    if (event.oldValue === event.newValue) return;

    const data = event.data;
    router.patch(route('dashboard.transactions.inline', data.id), {
        category_id: data.category_id,
        amount: data.amount,
        date: data.date,
        description: data.description,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Optional: Show success message or rely on page flash props
        },
        onError: (errors) => {
            // Revert changes on error
            alert('Failed to update: ' + Object.values(errors).join(', '));
            event.node.setDataValue(event.column.getColId(), event.oldValue);
        }
    });
};
</script>

<template>
    <div class="ag-theme-quartz h-[600px] w-full">
        <AgGridVue
            class="h-full w-full"
            :columnDefs="columnDefs"
            :rowData="transactions"
            :defaultColDef="defaultColDef"
            :pagination="true"
            :paginationPageSize="20"
            @grid-ready="onGridReady"
            @cell-value-changed="onCellValueChanged"
        />
    </div>
</template>
