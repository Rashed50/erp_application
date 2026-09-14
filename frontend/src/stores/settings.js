import { defineStore } from "pinia";

export const useSettingStore = defineStore('setting', {
    state: () => ({
        static_image_path : '/../contents/image/',
        image_path : ''
    })


});
