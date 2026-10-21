// stores/auth.js
import axios from "axios";
import { defineStore } from "pinia";
import { toast } from "vue3-toastify";
import { t } from "@/i18n";
import { setToast } from "@/helpers/toast";

// The backend signs a login out after this many minutes without an API request
// (it sends the real value on login; this is only the fallback).
const DEFAULT_IDLE_TIMEOUT_MINUTES = 30
// How long before the idle sign-out the user is warned.
const WARNING_BEFORE_MS = 60 * 1000
// While the user is active (mouse, keyboard, scroll, touch) but the page makes no
// API calls, ping the backend at most this often so its idle clock restarts too.
const KEEP_ALIVE_AFTER_MS = 2 * 60 * 1000
const CHECK_EVERY_MS = 10 * 1000
const ACTIVITY_EVENTS = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart']

let checkTimer = null
let isPinging = false
let hasWarned = false

// The backend already returns flat `roles` and `permissions` arrays of
// name strings on the user object (see UserResource) — nothing to compute here.
function setAuthToken(token, idleTimeoutMinutes = null) {
    if (token) {
        localStorage.setItem('auth_token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        if (idleTimeoutMinutes) localStorage.setItem('auth_idle_timeout', idleTimeoutMinutes)
        touchSession()
    } else {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_idle_timeout')
        localStorage.removeItem('auth_last_request')
        delete axios.defaults.headers.common['Authorization']
    }
}

/**
 * Record that the backend just accepted our token, restarting its idle clock.
 * Kept in localStorage so every open tab shares one clock.
 */
export function touchSession() {
    if (localStorage.getItem('auth_token')) {
        localStorage.setItem('auth_last_request', Date.now())
    }
}

/**
 * Milliseconds left before the backend treats the login as idle.
 */
function msUntilIdleSignOut() {
    const idleTimeoutMs = Number(localStorage.getItem('auth_idle_timeout') || DEFAULT_IDLE_TIMEOUT_MINUTES) * 60 * 1000
    const lastRequest = Number(localStorage.getItem('auth_last_request') || 0)
    return lastRequest + idleTimeoutMs - Date.now()
}

async function keepAliveOnActivity() {
    const lastRequest = Number(localStorage.getItem('auth_last_request') || 0)
    if (isPinging || Date.now() - lastRequest < KEEP_ALIVE_AFTER_MS || msUntilIdleSignOut() <= 0) return

    isPinging = true
    try {
        await axios.get('/api/user')
    } catch {
        // A 401 here is handled by the global interceptor.
    } finally {
        isPinging = false
    }
}

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isAuthenticated: false,
        authChecked: false,
    }),

    actions: {
        async login(email, password) {
            try {
                const res = await axios.post('/api/login', { email, password })
                if (res.data.success) {
                    setAuthToken(res.data.data.token, res.data.data.idle_timeout)
                    this.user = res.data.data.user
                    this.isAuthenticated = true
                    this.startIdleWatch()
                }
                return res.data
            } catch (err) {
                setAuthToken(null)
                this.user = null
                this.isAuthenticated = false
                return err.response?.data ?? { success: false, message: t('common.networkError') }
            }
        },
        async logout() {
            this.stopIdleWatch()
            try {
                await axios.post('/api/logout')
            } finally {
                setAuthToken(null)
                this.user = null
                this.isAuthenticated = false
            }
        },

        /**
         * Sign out after the idle timeout, warning a minute before. Any user
         * activity or API call restarts the clock.
         */
        startIdleWatch() {
            this.stopIdleWatch()
            hasWarned = false

            ACTIVITY_EVENTS.forEach((event) => window.addEventListener(event, keepAliveOnActivity, { passive: true }))

            checkTimer = setInterval(() => {
                const remaining = msUntilIdleSignOut()

                if (remaining <= 0) {
                    this.expireSession()
                } else if (remaining <= WARNING_BEFORE_MS && !hasWarned) {
                    hasWarned = true
                    toast.warning(t('You will be logged out in 1 minute due to inactivity.'))
                } else if (remaining > WARNING_BEFORE_MS) {
                    hasWarned = false
                }
            }, CHECK_EVERY_MS)
        },

        stopIdleWatch() {
            clearInterval(checkTimer)
            ACTIVITY_EVENTS.forEach((event) => window.removeEventListener(event, keepAliveOnActivity))
        },

        /**
         * The login is over (idle too long, or the backend answered 401): drop
         * the token locally and send the user to the login page.
         */
        async expireSession() {
            this.stopIdleWatch()

            const wasSignedIn = this.isAuthenticated
            setAuthToken(null)
            this.user = null
            this.isAuthenticated = false

            if (!wasSignedIn) return

            setToast('warning', t('You were logged out due to inactivity. Please log in again.'))
            // Imported lazily: the router itself imports this store.
            const { default: router } = await import('@/router')
            router.push({ name: 'admin_login' })
        },

        async checkAuth() {
            if (!localStorage.getItem('auth_token') || msUntilIdleSignOut() <= 0) {
                setAuthToken(null)
                this.isAuthenticated = false
                this.authChecked = true
                return
            }

            try {
                const res = await axios.get('/api/user')
                if (res.data.success) {
                    this.user = res.data.data
                    this.isAuthenticated = true
                    this.startIdleWatch()
                } else {
                    this.isAuthenticated = false
                }
            } catch {
                setAuthToken(null)
                this.isAuthenticated = false
            } finally {
                this.authChecked = true
            }
        }

    }


});
