<template>
  <div class="root-page">
    <div class="root-panel">
      <p class="text-uppercase text-primary fw-semibold mb-3">Cost Management System</p>
      <h1 class="display-3 mb-3">Hello World</h1>
      <p class="mb-4">
        Your App is ready. Use the login page to continue or visit the dashboard directly.
      </p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <RouterLink to="/login" class="btn btn-primary btn-lg px-4">Login</RouterLink>
        <RouterLink to="/dashboard" class="btn btn-outline-primary btn-lg px-4">Dashboard</RouterLink>
      </div>
    </div>
  </div>
</template>
