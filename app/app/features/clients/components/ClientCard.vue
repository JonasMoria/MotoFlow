<template>
    <article
        class="flex h-full flex-col rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
        <!-- Cabeçalho do card -->
        <div class="flex items-start gap-3">
            <img v-if="avatarUrl && !avatarFailed" :src="avatarUrl" :alt="`Foto de ${client.name}`"
                class="h-14 w-14 shrink-0 rounded-full border border-slate-200 object-cover"
                @error="avatarFailed = true" />

            <div v-else
                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand/10 text-lg font-bold text-brand"
                aria-hidden="true">
                {{ initials }}
            </div>

            <div class="min-w-0 flex-1 pt-0.5">
                <h2 class="truncate text-sm font-semibold text-slate-800" :title="client.name">
                    {{ client.name }}
                </h2>

                <span class="mt-1 inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">
                    ID {{ client.id }}
                </span>
            </div>

            <button type="button"
                class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand cursor-pointer"
                :aria-label="`Editar opções de ${client.name}`" title="Opções do cliente" @click="emit('edit', client)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"
                    aria-hidden="true">
                    <circle cx="12" cy="5" r="1.7" />
                    <circle cx="12" cy="12" r="1.7" />
                    <circle cx="12" cy="19" r="1.7" />
                </svg>
            </button>
        </div>

        <!-- Dados do cliente -->
        <div class="mt-5 flex-1 space-y-3">
            <div class="flex min-w-0 items-start gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" aria-hidden="true">
                    <path
                        d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.56 3.58.56a1 1 0 011 1V20a1 1 0 01-1 1C10.61 21 3 13.39 3 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.19 2.46.56 3.58a1 1 0 01-.25 1.01l-2.19 2.2z" />
                </svg>

                <span class="wrap-break-word text-xs leading-5 text-slate-500">
                    {{ formatPhone(client.phone) }}
                </span>
            </div>

            <div class="flex min-w-0 items-start gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" aria-hidden="true">
                    <path d="M2 6.5A2.5 2.5 0 014.5 4h15A2.5 2.5 0 0122 6.5v.3l-10 6.25L2 6.8v-.3z" />
                    <path
                        d="M2 9.15l9.47 5.92a1 1 0 001.06 0L22 9.15v8.35a2.5 2.5 0 01-2.5 2.5h-15A2.5 2.5 0 012 17.5V9.15z" />
                </svg>

                <span class="min-w-0 break-all text-xs leading-5 text-slate-500">
                    {{ client.email || 'Não informado' }}
                </span>
            </div>

            <div class="flex min-w-0 items-start gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" aria-hidden="true">
                    <path d="M6 2h9l5 5v13a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm8 1.5V8h4.5" />
                    <path d="M8 12h8v1.5H8zm0 4h8v1.5H8z" fill="#fff" />
                </svg>

                <span class="wrap-break-word text-xs leading-5 text-slate-500">
                    {{ formatDocument(client.document) }}
                </span>
            </div>

            <div class="flex min-w-0 items-start gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" aria-hidden="true">
                    <path
                        d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z" />
                </svg>

                <span class="line-clamp-2 wrap-break-word text-xs leading-5 text-slate-500">
                    {{ client.address || 'Endereço não informado' }}
                </span>
            </div>
        </div>

        <!-- Ações -->
        <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4">
            <button type="button"
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-brand transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-brand"
                @click="emit('edit', client)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" class="h-4 w-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 20h9M16.5 3.5a2.12 2.12 0 013 3L8 18l-4 1 1-4Z" />
                </svg>
                Editar
            </button>

            <button type="button"
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                @click="emit('delete', client)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" class="h-4 w-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 10v6m4-6v6" />
                </svg>
                Excluir
            </button>
        </div>
    </article>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    client: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['edit', 'delete'])

const config = useRuntimeConfig()
const avatarFailed = ref(false)

const avatarUrl = computed(() => {
    if (!props.client.avatar_path) {
        return null
    }

    const apiBaseUrl = config.public.apiBaseUrl || ''
    const baseUrl = apiBaseUrl.replace(/\/api\/?$/, '').replace(/\/$/, '')

    return `${baseUrl}/storage/${props.client.avatar_path}`
});

const initials = computed(() => {
    const name = props.client.name?.trim() || '?'

    return name
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase()
})

watch(
    () => props.client.avatar_path,
    () => {
        avatarFailed.value = false
    },
)

function formatPhone(phone) {
    if (!phone) {
        return 'Não informado'
    }

    const digits = String(phone).replace(/\D/g, '')
    const national = digits.startsWith('55') ? digits.slice(2) : digits

    if (national.length === 11) {
        return `(${national.slice(0, 2)}) ${national.slice(2, 7)}-${national.slice(7)}`
    }

    if (national.length === 10) {
        return `(${national.slice(0, 2)}) ${national.slice(2, 6)}-${national.slice(6)}`
    }

    return phone
}

function formatDocument(document) {
    if (!document) {
        return 'Não informado'
    }

    const digits = String(document).replace(/\D/g, '')

    if (digits.length === 11) {
        return digits.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
    }

    if (digits.length === 14) {
        return digits.replace(
            /(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,
            '$1.$2.$3/$4-$5',
        )
    }

    return document
}
</script>
