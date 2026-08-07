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
    ajax: '/api/v1/states',
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
    <DataTable
        class="table table-striped table-hover align-middle mb-0"
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
</template>
