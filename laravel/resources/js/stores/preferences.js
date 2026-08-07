import { defineStore } from 'pinia';

const STORAGE_KEY = 'geostatistical-keys.theme';

function dark() {
    try {
        return localStorage.getItem(STORAGE_KEY) === 'dark';
    } catch {
        return false;
    }
}

export const usePreferenceStore = defineStore('preferences', {
    state: () => ({ dark: dark() }),

    actions: {
        toggleTheme() {
            this.dark = !this.dark;

            try {
                localStorage.setItem(STORAGE_KEY, this.dark ? 'dark' : 'light');
            } catch {
                // The selected mode remains active when browser storage is unavailable.
            }
        },
    },
});
