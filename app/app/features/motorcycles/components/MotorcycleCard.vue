<template>
    <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
        <div class="flex items-start justify-between border-b border-slate-100 p-4">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-brand">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.6" class="h-6 w-6" aria-hidden="true">
                        <circle cx="5.5" cy="17.5" r="3.5" />
                        <circle cx="18.5" cy="17.5" r="3.5" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m15 6 2 2h-4l-3 6H5.5m6.5-6 3 6h3.5m-6.5-6H9" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h3 class="truncate text-sm font-bold text-foreground">
                        {{ motorcycle.brand }} {{ motorcycle.model }}
                    </h3>

                    <p class="mt-1 text-xs text-muted">
                        {{ motorcycle.year || 'Ano não informado' }}
                    </p>
                </div>
            </div>

            <span
                class="shrink-0 rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-slate-700">
                {{ motorcycle.plate }}
            </span>
        </div>

        <div class="space-y-3 p-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-xs text-muted">Cor</p>
                    <p class="mt-1 text-sm font-medium text-foreground">
                        {{ motorcycle.color || 'Não informada' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-muted">Renavam</p>
                    <p class="mt-1 truncate text-sm font-medium text-foreground">
                        {{ motorcycle.renavam || 'Não informado' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-muted">Chassi</p>
                    <p class="mt-1 truncate text-sm font-medium text-foreground">
                        {{ motorcycle.chassis || 'Não informado' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-muted">Motor</p>
                    <p class="mt-1 truncate text-sm font-medium text-foreground">
                        {{ motorcycle.engine_number || 'Não informado' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50 p-3">
            <button type="button"
                class="rounded-lg px-3 py-2 text-xs font-semibold text-brand transition hover:bg-blue-50"
                @click="emit('edit', motorcycle)">
                Editar
            </button>

            <button type="button"
                class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                @click="emit('delete', motorcycle)">
                Excluir
            </button>
        </div>
    </article>
</template>

<script setup>
defineProps({
    motorcycle: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['edit', 'delete'])
</script>
