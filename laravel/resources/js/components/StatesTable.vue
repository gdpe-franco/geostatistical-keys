<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net-bs5';

DataTable.use(DataTablesLib);

const population = new Intl.NumberFormat('es-MX');
const table = ref();

let activeRow;
let activeRowNode;
let municipalityTable;
let municipalityRequest;

function formatPopulation(value, type) {
    if (!['display', 'filter'].includes(type)) {
        return value;
    }

    return value === null ? '—' : population.format(Number(value));
}

const columns = [
    { data: 'state_code' },
    { data: 'name' },
    { data: 'short_name' },
    {
        data: 'total_population',
        render: formatPopulation,
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
    createdRow: (row, state) => {
        row.setAttribute('aria-expanded', 'false');
        row.setAttribute('aria-label', `Show municipalities for ${state.name}`);
        row.tabIndex = 0;
    },
};

const municipalityColumns = [
    { data: 'municipality_code' },
    { data: 'name' },
    {
        data: 'total_population',
        render: formatPopulation,
    },
];

function closeMunicipalities() {
    municipalityRequest?.abort();
    municipalityRequest = undefined;
    municipalityTable?.destroy();
    municipalityTable = undefined;

    if (activeRow) {
        activeRow.child.remove();
        activeRowNode?.setAttribute('aria-expanded', 'false');
    }

    activeRow = undefined;
    activeRowNode = undefined;
}

function message(content, text, retry) {
    content.replaceChildren();

    const paragraph = document.createElement('p');
    paragraph.className = 'municipalities-detail__message';
    paragraph.textContent = text;
    content.append(paragraph);

    if (retry) {
        const button = document.createElement('button');
        button.className = 'municipalities-detail__retry';
        button.textContent = 'Try again';
        button.type = 'button';
        button.addEventListener('click', retry);
        content.append(button);
    }
}

function municipalityTableElement() {
    const nestedTable = document.createElement('table');
    nestedTable.className = 'table table-striped table-hover align-middle mb-0';

    const header = nestedTable.createTHead().insertRow();

    ['Municipality code', 'Municipality name', 'Total population'].forEach((label) => {
        const cell = document.createElement('th');
        cell.textContent = label;
        header.append(cell);
    });

    return nestedTable;
}

async function loadMunicipalities(state, row, content) {
    municipalityRequest = new AbortController();
    message(content, 'Loading municipalities...');

    try {
        const response = await fetch(`/api/v1/states/${state.state_code}/municipalities`, {
            signal: municipalityRequest.signal,
        });

        if (!response.ok) {
            throw new Error('Unable to load municipalities.');
        }

        const municipalities = await response.json();

        if (row.node() !== activeRowNode) {
            return;
        }

        if (!Array.isArray(municipalities) || municipalities.length === 0) {
            message(content, 'No municipalities are available for this state.');
            return;
        }

        const nestedTable = municipalityTableElement();
        content.replaceChildren(nestedTable);
        municipalityTable = new DataTablesLib(nestedTable, {
            columns: municipalityColumns,
            data: municipalities,
            order: [[0, 'asc']],
            pageLength: 10,
            language: {
                emptyTable: 'No municipalities are available for this state.',
            },
        });
    } catch (error) {
        if (error.name === 'AbortError' || row.node() !== activeRowNode) {
            return;
        }

        message(content, 'Municipalities could not be loaded.', () => loadMunicipalities(state, row, content));
    }
}

function toggleMunicipalities(row) {
    if (!row.data()) {
        return;
    }

    if (row.node() === activeRowNode) {
        closeMunicipalities();
        return;
    }

    closeMunicipalities();
    activeRow = row;
    activeRowNode = row.node();
    activeRowNode.setAttribute('aria-expanded', 'true');

    const content = document.createElement('div');
    content.className = 'municipalities-detail__content';
    row.child(content, 'municipalities-detail').show();

    void loadMunicipalities(row.data(), row, content);
}

function activateRow(event) {
    if (event.target.closest('.municipalities-detail')) {
        return;
    }

    const row = table.value.dt.row(event.currentTarget);

    toggleMunicipalities(row);
}

function activateRowWithKeyboard(event) {
    if (!['Enter', ' '].includes(event.key)) {
        return;
    }

    event.preventDefault();
    activateRow(event);
}

onMounted(() => {
    const stateTable = table.value.dt;

    stateTable.on('click.municipalities', 'tbody tr', activateRow);
    stateTable.on('keydown.municipalities', 'tbody tr', activateRowWithKeyboard);
});

onBeforeUnmount(() => {
    closeMunicipalities();
    table.value?.dt.off('.municipalities');
});
</script>

<template>
    <DataTable
        ref="table"
        class="table table-striped table-hover align-middle mb-0"
        :columns="columns"
        :options="options"
    >
        <thead>
            <tr>
                <th>State code</th>
                <th>State name</th>
                <th>Short name</th>
                <th>Total population</th>
            </tr>
        </thead>
    </DataTable>
</template>
