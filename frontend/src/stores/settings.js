import axios from "axios";
import { defineStore } from "pinia";

export const useSettingStore = defineStore('setting', {
    state: () => ({
        static_image_path : '/../contents/image/',
        image_path : '',
        // Company branding from GET /api/settings (name, contact info, logo_url).
        company: {},
        companyLoaded: false,
    }),

    actions: {
        async fetchCompany() {
            try {
                const { data } = await axios.get('/api/settings')
                if (data.success) {
                    this.company = data.data
                }
            } finally {
                this.companyLoaded = true
            }
        },

        setCompany(company) {
            this.company = company
        },
    }
});
