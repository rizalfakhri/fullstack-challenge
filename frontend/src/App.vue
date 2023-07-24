<script setup lang="ts">
import { RouterLink, RouterView } from "vue-router";
</script>

<template>
  <header class="h-20 my-auto text-white bg-primary">
    <div class="h-full max-w-4xl mx-auto font-mono wrapper">
      <nav class="flex items-center justify-start h-full my-auto text-xl">
        <RouterLink to="/">Home</RouterLink>
      </nav>
    </div>
  </header>
  <body class="h-auto min-h-screen pt-20 text-white bg-primary">
    <main class="max-w-4xl mx-auto">
      <RouterView />
    </main>
  </body>
</template>

<style scoped></style>
