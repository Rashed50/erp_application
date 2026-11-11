import axios from 'axios'
import { computed, ref } from 'vue'
import en from './locales/en'
import bn from './locales/bn'

// Only English and Bangla are supported. The chosen locale is kept in
// localStorage and sent to the API as X-Locale so backend messages match.
export const SUPPORTED_LOCALES = [
    { code: 'en', label: 'English', short: 'EN', intl: 'en-US' },
    { code: 'bn', label: 'বাংলা', short: 'বাং', intl: 'bn-BD' },
]

const DEFAULT_LOCALE = 'en'
const STORAGE_KEY = 'locale'
const messages = { en, bn }

const isSupported = (code) => SUPPORTED_LOCALES.some((l) => l.code === code)

const readStoredLocale = () => {
    try {
        const stored = localStorage.getItem(STORAGE_KEY)
        return isSupported(stored) ? stored : DEFAULT_LOCALE
    } catch {
        return DEFAULT_LOCALE
    }
}

export const locale = ref(readStoredLocale())

const lookup = (dictionary, key) => {
    // Dotted keys ("nav.dashboard") walk the nested objects…
    const nested = key.split('.').reduce((node, part) => (node && typeof node === 'object' ? node[part] : undefined), dictionary)
    if (typeof nested === 'string') return nested

    // …plain English text ("Add Customer") is looked up in the phrase table.
    const phrase = dictionary.phrases?.[key]
    return typeof phrase === 'string' ? phrase : undefined
}

const interpolate = (text, params) =>
    params ? text.replace(/\{(\w+)\}/g, (match, name) => (name in params ? params[name] : match)) : text

/**
 * Translate a key or an English phrase into the active locale.
 * Falls back to English, then to the key itself.
 */
export function t(key, params) {
    if (key === null || key === undefined || key === '') return ''
    const text = lookup(messages[locale.value], key) ?? lookup(messages.en, key) ?? key
    return interpolate(text, params)
}

/** The Intl locale tag (en-US / bn-BD) for the active locale, for date and number formatting. */
export const intlLocale = () => SUPPORTED_LOCALES.find((l) => l.code === locale.value)?.intl ?? 'en-US'

/** Format a number with the active locale's digits (e.g. ১,২৩৪ in Bangla). */
export function n(value, options) {
    const number = Number(value)
    if (Number.isNaN(number)) return value
    return new Intl.NumberFormat(intlLocale(), options).format(number)
}

const applyLocale = (code) => {
    axios.defaults.headers.common['X-Locale'] = code
    document.documentElement.lang = code
}

export function setLocale(code) {
    if (!isSupported(code) || code === locale.value) return
    locale.value = code
    try {
        localStorage.setItem(STORAGE_KEY, code)
    } catch {
        // Storage can be unavailable (private mode); the choice still applies for this session.
    }
    applyLocale(code)
}

export function useI18n() {
    return {
        t,
        n,
        locale: computed(() => locale.value),
        locales: SUPPORTED_LOCALES,
        setLocale,
    }
}

export default {
    install(app) {
        applyLocale(locale.value)
        app.config.globalProperties.$t = t
        app.config.globalProperties.$n = n
    },
}
