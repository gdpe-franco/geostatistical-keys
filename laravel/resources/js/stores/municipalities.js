import { defineStore } from 'pinia';

const STORAGE_KEY = 'geostatistical-keys.municipalities';
const TTL = 24 * 60 * 60 * 1000;

function entries() {
    try {
        const stored = JSON.parse(localStorage.getItem(STORAGE_KEY));

        return stored && typeof stored === 'object' && !Array.isArray(stored) ? stored : {};
    } catch {
        return {};
    }
}

export const useMunicipalityStore = defineStore('municipalities', {
    state: () => ({ entries: entries() }),

    actions: {
        cached(stateCode) {
            const entry = this.entries[stateCode];

            if (!entry || !Array.isArray(entry.data) || typeof entry.expiresAt !== 'number' || entry.expiresAt <= Date.now()) {
                delete this.entries[stateCode];
                this.persist();

                return null;
            }

            return entry.data;
        },

        remember(stateCode, data) {
            this.entries[stateCode] = {
                data,
                expiresAt: Date.now() + TTL,
            };
            this.persist();
        },

        persist() {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(this.entries));
            } catch {
                // The in-memory cache remains usable when browser storage is unavailable.
            }
        },
    },
});
