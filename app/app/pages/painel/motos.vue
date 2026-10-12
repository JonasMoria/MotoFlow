<template>
    <section>
        <h1 class="text-2xl font-bold text-foreground">
            Motos
        </h1>

        <p class="mt-2 text-sm text-muted">
            Gerencie as motos cadastradas na oficina.
        </p>
    </section>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard',
})

useHead({
    title: 'Motos dos Clientes | MotoFlow',
})
</script>