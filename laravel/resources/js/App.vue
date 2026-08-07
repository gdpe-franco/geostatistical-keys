<script setup>
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net-bs5';

DataTable.use(DataTablesLib);

const population = new Intl.NumberFormat('es-MX');

const columns = [
    { data: 'state_code' },
    { data: 'name' },
    {
        data: 'total_population',
        render: (value, type) => ['display', 'filter'].includes(type)
            ? population.format(Number(value))
            : value,
    },
];

const options = {
    ajax: '/api/states',
    serverSide: true,
    processing: true,
    pageLength: 10,
    order: [[0, 'asc']],
    language: {
        emptyTable: 'No states have been imported yet.',
        processing: 'Loading states...',
    },
};
</script>

<template>
    <main class="container py-5">
        <h1 class="mb-4">Mexican states</h1>

        <DataTable
            class="table table-striped table-hover align-middle"
            :columns="columns"
            :options="options"
        >
            <thead>
                <tr>
                    <th>State code</th>
                    <th>State name</th>
                    <th>Total population</th>
                </tr>
            </thead>
        </DataTable>
    </main>
</template>
