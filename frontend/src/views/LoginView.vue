<template>
  <div class="login-page">
    <div class="login-shell">
      <div class="login-visual">
        <span class="badge">Finance Suite</span>
        <h2>Smart cost tracking for every decision.</h2>
        <p>
          Monitor budgets, improve spending control, and keep your business data aligned in real time.
        </p>
        <div class="mt-4 d-flex gap-3 flex-wrap text-white-50">
          <span><i class="fa-solid fa-shield-halved me-2"></i> Secure</span>
          <span><i class="fa-solid fa-chart-line me-2"></i> Insightful</span>
          <span><i class="fa-solid fa-bolt me-2"></i> Fast</span>
        </div>
      </div>

      <div class="login-card-wrap">
        <div class="login-card">
          <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width:64px;height:64px;">
              <i class="fa-solid fa-wallet fa-xl"></i>
            </div>
            <h3 class="mb-1 fw-bold">Welcome back</h3>
            <p class="text-muted mb-0">Sign in to your account</p>
          </div>

          <v-card class="border-0 shadow-none" flat>
            <v-form @submit.prevent="handleLogin">
              <v-text-field
                v-model="email"
                label="Email address"
                prepend-inner-icon="mdi-email-outline"
                variant="outlined"
                density="comfortable"
                class="mb-3"
              />

              <v-text-field
                v-model="password"
                label="Password"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                density="comfortable"
                type="password"
                class="mb-3"
              />

              <div class="d-flex justify-content-between align-items-center mb-4">
                <v-checkbox v-model="remember" label="Remember me" density="compact" hide-details />
                <a href="#" class="text-primary text-decoration-none small">Forgot password?</a>
              </div>

              <v-btn block color="primary" size="large" type="submit" class="mb-3">
                Sign In
              </v-btn>

              <v-btn block variant="outlined" color="primary" size="large" @click="goToDashboard">
                Preview Dashboard
              </v-btn>
            </v-form>
          </v-card>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const email = ref('admin@cost.io')
const password = ref('password123')
const remember = ref(true)

const router = useRouter()

const handleLogin = () => {
  router.push('/dashboard')
}

const goToDashboard = () => {
  router.push('/dashboard')
}
</script>
