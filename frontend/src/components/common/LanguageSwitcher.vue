<template>
    <div class="language-switcher" role="group" :aria-label="t('common.language')">
        <button v-for="item in locales" :key="item.code" type="button" class="language-switcher__option"
            :class="{ 'language-switcher__option--active': item.code === locale }" :title="item.label"
            :aria-pressed="item.code === locale" @click="setLocale(item.code)">
            {{ item.short }}
        </button>
    </div>
</template>

<script setup>
import { useI18n } from '@/i18n'

const { t, locale, locales, setLocale } = useI18n()
</script>

<style scoped>
.language-switcher {
    background-color: #ecedfd;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    padding: 2px;
}

.language-switcher__option {
    font-size: 12px;
    padding: 2px 10px;
    border-radius: 30px;
    line-height: 1.6;
    color: #333333;
}

.language-switcher__option--active {
    background-color: #464deb;
    color: #ffffff;
}
</style>
