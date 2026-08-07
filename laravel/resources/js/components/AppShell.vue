<template>
    <div class="app-shell">
        <header class="app-header">
            <div class="container d-flex align-items-center justify-content-between py-3">
                <a class="brand" href="/" aria-label="Geostatistical Keys home">
                    <span class="brand-mark">MX</span>
                    <span>
                        <strong>Geostatistical Keys</strong>
                        <small>Mexico data catalog</small>
                    </span>
                </a>
                <span class="source-chip"><span aria-hidden="true"></span> INEGI · Census 2020</span>
            </div>
        </header>

        <main class="container py-5">
            <section class="catalog-intro mb-4">
                <p class="eyebrow">Territorial reference · Mexico</p>
                <div class="row align-items-end g-4">
                    <div class="col-lg-8">
                        <h1>Explore Mexico, one state at a time.</h1>
                        <p class="lead mb-0">
                            A lightweight public catalog of state-level population data,
                            ready to support geographic exploration.
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <div class="catalog-stat">
                            <strong>32</strong>
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
                Source: INEGI, Population and Housing Census 2020.
            </div>
        </footer>
    </div>
</template>
