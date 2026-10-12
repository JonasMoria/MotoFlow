<template>
    <section>
        <h1 class="text-2xl font-bold text-foreground">
            Meu Perfil
        </h1>

        <p class="mt-2 text-sm text-muted">
            Configure sua Conta.
        </p>
    </section>
</template>

<script setup>
definePageMeta({
    layout: 'dashboard',
});

useHead({
    title: 'Meu Perfil | MotoFlow',
})
</script>