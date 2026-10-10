<template>
    <main class="grid min-h-screen bg-white lg:grid-cols-2">
        <!-- Painel da marca -->
        <section class="hidden relative md:flex min-h-75 flex-col overflow-hidden
             bg-sidebar px-7 py-8 text-white sm:px-12 sm:py-10
             lg:min-h-screen lg:px-16 lg:py-12 xl:px-20">
            <!-- Elementos decorativos -->
            <div class="pointer-events-none absolute -right-40 bottom-12
               size-105 rounded-full border border-white/5
               sm:size-130" />

            <div class="pointer-events-none absolute -right-20 bottom-32
               size-70 rounded-full border border-white/5
               sm:size-85" />

            <div class="relative z-10 flex flex-1 flex-col">
                <!-- Mensagem de marca -->
                <div class="my-auto max-w-lg py-12 lg:py-16">
                    <span class="mb-6 inline-block text-xs font-bold
                   tracking-[0.18em] text-orange-300">
                        GESTÃO INTELIGENTE
                    </span>

                    <h1 class="text-4xl font-extrabold leading-tight
                   tracking-tight sm:text-5xl xl:text-7xl">
                        Sua oficina.
                        <br />
                        <span class="text-accent">Em outro ritmo.</span>
                    </h1>

                    <p class="mt-6 max-w-md text-base leading-8
                   text-slate-300 sm:text-lg">
                        Organize clientes, acompanhe cada manutenção e tenha
                        o controle da sua oficina em um só lugar.
                    </p>

                    <div class="mt-8 h-1 w-14 rounded-full bg-accent" />

                    <div class="mt-6 flex items-center gap-3
                   text-sm text-slate-200">
                        <span class="grid size-6 place-items-center rounded-full
                     bg-orange-500/15 font-bold text-orange-300">
                            ✓
                        </span>

                        Mais controle. Menos complicação.
                    </div>
                </div>

                <footer class="text-xs text-slate-400">
                    Feito para quem vive de duas rodas.
                </footer>
            </div>
        </section>

        <!-- Painel do formulário -->
        <section class="flex min-w-0 flex-col justify-center
             px-6 py-10 sm:px-12 lg:px-16 xl:px-24">
            <div class="mx-auto w-full max-w-md">
                <LoginForm />
            </div>

            <footer class="mx-auto mt-12 flex w-full max-w-md flex-wrap
               justify-between gap-3 text-xs text-text-muted">
                <span>
                    © {{ new Date().getFullYear() }} MotoFlow
                </span>

                <span>Gestão feita para evoluir.</span>
            </footer>
        </section>
    </main>
</template>

<script setup>
import LoginForm from '~/features/auth/components/LoginForm.vue'

definePageMeta({
    layout: 'auth',
});

useHead({
    title: 'Login | MotoFlow',
})
</script>