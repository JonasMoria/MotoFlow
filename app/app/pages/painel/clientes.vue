<template>
    <section>
        <h1 class="text-2xl font-bold text-foreground">
            Clientes
        </h1>

        <p class="mt-2 text-sm text-muted">
            Gerencie os clientes cadastrados na oficina.
        </p>
    </section>
</template>

<script setup>
definePageMeta({
    layout: 'dashboard',
})

useHead({
    title: 'Meus Clientes | MotoFlow',
})
</script>