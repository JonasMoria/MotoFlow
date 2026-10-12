<template>
    <form class="space-y-5" @submit.prevent="handleSubmit">
        <!-- Alertas de validação -->
        <Alert v-if="validationMessages.length" :variant="AlertVariant.ERROR" title="Verifique os dados informados">
            <ul class="list-inside list-disc space-y-1">
                <li v-for="(message, index) in validationMessages" :key="`${index}-${message}`">
                    {{ message }}
                </li>
            </ul>
        </Alert>

        <!-- Avatar -->
        <div class="flex flex-col items-center gap-3 border-b border-slate-200 pb-5 sm:flex-row">
            <div
                class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                <img v-if="avatarPreview" :src="avatarPreview" alt="Pré-visualização do avatar"
                    class="h-full w-full object-cover" />

                <img v-else-if="client?.avatar_path" :src="client.avatar_path" alt="Avatar do cliente"
                    class="h-full w-full object-cover" />

                <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5" class="h-9 w-9 text-slate-400" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.1a7.5 7.5 0 0 1 15 0A17.9 17.9 0 0 1 12 21.75c-2.7 0-5.25-.6-7.5-1.65Z" />
                </svg>
            </div>

            <div class="min-w-0 flex-1 text-center sm:text-left">
                <p class="text-sm font-semibold text-foreground">
                    Foto do cliente
                </p>

                <p class="mt-1 text-xs text-muted">
                    JPG, JPEG, PNG ou WEBP. Tamanho máximo de 5 MB.
                </p>

                <label
                    class="mt-2 inline-flex cursor-pointer items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-foreground transition hover:bg-slate-50">
                    Selecionar imagem

                    <input type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="sr-only"
                        @change="handleAvatarChange" />
                </label>

                <button v-if="avatar" type="button" class="ml-3 text-sm font-medium text-red-700 hover:text-red-800"
                    @click="removeAvatar">
                    Remover seleção
                </button>
            </div>
        </div>

        <!-- Dados do cliente -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Nome -->
            <div class="sm:col-span-2">
                <label for="client-name" class="mb-1.5 block text-sm font-medium text-foreground">
                    Nome completo <span class="text-red-600">*</span>
                </label>

                <input id="client-name" v-model.trim="name" v-bind="nameAttrs" type="text" maxlength="255"
                    autocomplete="name" placeholder="Ex.: João da Silva" :aria-invalid="Boolean(fieldErrors.name)"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-foreground outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-2 focus:ring-brand/15"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500/15': fieldErrors.name,
                    }" />
            </div>

            <!-- Telefone -->
            <div>
                <label for="client-phone" class="mb-1.5 block text-sm font-medium text-foreground">
                    Telefone <span class="text-red-600">*</span>
                </label>

                <input id="client-phone" v-model.trim="phone" v-bind="phoneAttrs" type="tel" maxlength="20"
                    autocomplete="tel" placeholder="(11) 99999-9999" :aria-invalid="Boolean(fieldErrors.phone)"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-foreground outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-2 focus:ring-brand/15"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500/15': fieldErrors.phone,
                    }" />
            </div>

            <!-- E-mail -->
            <div>
                <label for="client-email" class="mb-1.5 block text-sm font-medium text-foreground">
                    E-mail
                </label>

                <input id="client-email" v-model.trim="email" v-bind="emailAttrs" type="email" maxlength="255"
                    autocomplete="email" placeholder="cliente@exemplo.com" :aria-invalid="Boolean(fieldErrors.email)"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-foreground outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-2 focus:ring-brand/15"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500/15': fieldErrors.email,
                    }" />
            </div>

            <!-- Documento -->
            <div>
                <label for="client-document" class="mb-1.5 block text-sm font-medium text-foreground">
                    CPF/CNPJ
                </label>

                <input id="client-document" v-model.trim="document" v-bind="documentAttrs" type="text" maxlength="20"
                    placeholder="Documento do cliente" :aria-invalid="Boolean(fieldErrors.document)"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-foreground outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-2 focus:ring-brand/15"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500/15': fieldErrors.document,
                    }" />
            </div>

            <!-- Endereço -->
            <div class="sm:col-span-2">
                <label for="client-address" class="mb-1.5 block text-sm font-medium text-foreground">
                    Endereço
                </label>

                <textarea id="client-address" v-model.trim="address" v-bind="addressAttrs" rows="3"
                    placeholder="Rua, número, bairro, cidade..." :aria-invalid="Boolean(fieldErrors.address)"
                    class="w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-foreground outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-2 focus:ring-brand/15"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500/15': fieldErrors.address,
                    }" />
            </div>
        </div>

        <!-- Ações -->
        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
            <button type="button" :disabled="loading"
                class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-foreground transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                @click="emit('cancel')">
                Cancelar
            </button>

            <button type="submit" :disabled="loading"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60">
                <svg v-if="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z" />
                </svg>

                {{
                    loading
                        ? 'Salvando...'
                        : (isEditing ? 'Salvar alterações' : 'Cadastrar cliente')
                }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { useForm } from 'vee-validate'
import * as yup from 'yup'

import { Client } from '~/features/clients/models/Client'
import Alert from '~/components/ui/Alert.vue'
import { AlertVariant } from '~/features/ui/types/AlertVariant'

const props = defineProps({
    client: {
        type: Object,
        default: null,
    },

    loading: {
        type: Boolean,
        default: false,
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

const emit = defineEmits(['submit', 'cancel'])

const avatar = ref(null)
const avatarPreview = ref(null)
const avatarError = ref('')

const isEditing = computed(() => Boolean(props.client?.id))

const validationSchema = yup.object({
    name: yup
        .string()
        .required('Informe o nome do cliente.')
        .max(255, 'O nome deve ter no máximo 255 caracteres.'),

    phone: yup
        .string()
        .required('Informe o telefone do cliente.')
        .max(20, 'O telefone deve ter no máximo 20 caracteres.')
        .test(
            'brazilian-mobile',
            'Informe um celular brasileiro válido com DDD.',
            (value) => {
                if (!value) return true

                const digits = value.replace(/\D/g, '')
                const nationalNumber =
                    digits.startsWith('55') && digits.length === 13
                        ? digits.slice(2)
                        : digits

                return /^\d{2}9\d{8}$/.test(nationalNumber)
            },
        ),

    email: yup
        .string()
        .transform((value) => value?.trim() || '')
        .email('Informe um endereço de e-mail válido.')
        .max(255, 'O e-mail deve ter no máximo 255 caracteres.'),

    document: yup
        .string()
        .max(20, 'O documento deve ter no máximo 20 caracteres.'),

    address: yup.string(),
})

const {
    defineField,
    errors: fieldErrors,
    handleSubmit: validateAndSubmit,
    resetForm,
} = useForm({
    validationSchema,
    initialValues: createForm(props.client),
})

const [name, nameAttrs] = defineField('name')
const [phone, phoneAttrs] = defineField('phone')
const [email, emailAttrs] = defineField('email')
const [document, documentAttrs] = defineField('document')
const [address, addressAttrs] = defineField('address')

function createForm(client) {
    return {
        name: client?.name ?? '',
        phone: client?.phone ?? '',
        email: client?.email ?? '',
        document: client?.document ?? '',
        address: client?.address ?? '',
    }
}

const validationMessages = computed(() => {
    const messages = [
        ...Object.values(fieldErrors.value),
        ...Object.values(props.errors),
        avatarError.value,
    ]

    return [
        ...new Set(
            messages.filter(
                (message) =>
                    typeof message === 'string' && message.length > 0,
            ),
        ),
    ]
})

watch(
    () => props.client,
    (client) => {
        resetForm({
            values: createForm(client),
        })

        avatar.value = null
        avatarError.value = ''
        clearAvatarPreview()
    },
    { immediate: true },
)

function handleAvatarChange(event) {
    const file = event.target.files?.[0]

    avatar.value = null
    avatarError.value = ''
    clearAvatarPreview()

    if (!file) return

    const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ]

    if (!allowedTypes.includes(file.type)) {
        avatarError.value = 'Selecione uma imagem JPG, JPEG, PNG ou WEBP.'
        event.target.value = ''
        return
    }

    if (file.size > 5 * 1024 * 1024) {
        avatarError.value = 'A imagem deve ter no máximo 5 MB.'
        event.target.value = ''
        return
    }

    avatar.value = file
    avatarPreview.value = URL.createObjectURL(file)
}

function removeAvatar() {
    avatar.value = null
    avatarError.value = ''
    clearAvatarPreview()
}

function clearAvatarPreview() {
    if (avatarPreview.value?.startsWith('blob:')) {
        URL.revokeObjectURL(avatarPreview.value)
    }

    avatarPreview.value = null
}

onBeforeUnmount(clearAvatarPreview)

const handleSubmit = validateAndSubmit((values) => {
    if (avatarError.value || props.loading) return

    const client = new Client({
        id: props.client?.id ?? null,
        ...values,
        avatar: avatar.value,
        avatar_path: props.client?.avatar_path ?? null,
    })

    emit('submit', client)
})
</script>
