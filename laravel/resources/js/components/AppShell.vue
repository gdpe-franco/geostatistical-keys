<script setup>
import { onMounted, ref } from 'vue';

const dark = ref(false);
const total = ref(null);
const source = ref(null);

onMounted(async () => {
    try {
        const response = await fetch('/api/v1/summary');

        if (!response.ok) {
            return;
        }

        const summary = await response.json();
        total.value = typeof summary.total === 'number' ? summary.total : null;
        source.value = typeof summary.source === 'string' ? summary.source : null;
    } catch {
        // The local catalog remains useful when the optional source is unavailable.
    }
});
</script>

<template>
    <div class="app-shell" :class="{ 'app-shell--dark': dark }">
        <header class="app-header">
            <div class="app-header__content container d-flex align-items-center justify-content-between py-3">
                <a class="brand" href="/" aria-label="Geostatistical Keys home">
                    <span class="brand-mark">MX</span>
                    <strong>Geostatistical Keys</strong>
                </a>
                <div class="header-actions">
                    <span class="source-chip"><span aria-hidden="true"></span> {{ source ?? 'Source unavailable' }}</span>
                    <button
                        class="theme-toggle"
                        type="button"
                        :aria-label="dark ? 'Use light mode' : 'Use dark mode'"
                        :aria-pressed="dark"
                        @click="dark = !dark"
                    >
                        <span aria-hidden="true">{{ dark ? '☀' : '☾' }}</span>
                        {{ dark ? 'Light' : 'Dark' }}
                    </button>
                </div>
            </div>
        </header>

        <main class="app-main container py-5">
            <section class="catalog-intro mb-4">
                <p class="eyebrow">Territorial reference · Mexico</p>
                <div class="row align-items-end g-4">
                    <div class="col-lg-8">
                        <h1>Explore <span role="img" aria-label="Mexico">🇲🇽</span>, one state at a time.</h1>
                        <p class="lead mb-0">
                            A lightweight public catalog of state/municipality-level population data.
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <div class="catalog-stat">
                            <strong>{{ total ?? '—' }}</strong>
                            <span>federal entities in the catalog</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="data-surface" aria-labelledby="states-heading">
                <header class="data-surface__header">
                    <div>
                        <p class="eyebrow">State index</p>
                        <h2 id="states-heading">Mexican states</h2>
                    </div>
                    <small>Search, sort, or browse the catalog.</small>
                </header>
                <slot />
            </section>
        </main>

        <footer class="app-footer">
            <div class="container py-3">
                Source: {{ source ?? 'Unavailable from INEGI.' }}
            </div>
        </footer>
    </div>
</template>
