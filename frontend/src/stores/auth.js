// stores/auth.js
import axios from "axios";
import { defineStore } from "pinia";

// The backend already returns flat `roles` and `permissions` arrays of
// name strings on the user object (see UserResource) — nothing to compute here.
function setAuthToken(token) {
    if (token) {
        localStorage.setItem('auth_token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    } else {
        localStorage.removeItem('auth_token')
        delete axios.defaults.headers.common['Authorization']
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
                    setAuthToken(res.data.data.token)
                    this.user = res.data.data.user
                    this.isAuthenticated = true
                }
                return res.data
            } catch (err) {
                setAuthToken(null)
                this.user = null
                this.isAuthenticated = false
                return err.response?.data ?? { success: false, message: 'Network error. Please try again.' }
            }
        },
        async logout() {
            try {
                await axios.post('/api/logout')
            } finally {
                setAuthToken(null)
                this.user = null
                this.isAuthenticated = false
            }
        },


        async checkAuth() {
            if (!localStorage.getItem('auth_token')) {
                this.isAuthenticated = false
                this.authChecked = true
                return
            }

            try {
                const res = await axios.get('/api/user')
                if (res.data.success) {
                    this.user = res.data.data
                    this.isAuthenticated = true
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
