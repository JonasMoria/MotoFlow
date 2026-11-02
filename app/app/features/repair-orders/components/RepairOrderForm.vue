<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <Alert v-if="validationMessages.length" :variant="AlertVariant.ERROR" title="Verifique os dados informados">
            <ul class="list-inside list-disc space-y-1">
                <li v-for="(message, index) in validationMessages" :key="index">
                    {{ message }}
                </li>
            </ul>
        </Alert>

        <!-- Identificação -->
        <div>
            <h3 class="mb-4 text-sm font-bold text-foreground">
                Identificação da ordem
            </h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">
                        Cliente <span class="text-red-600">*</span>
                    </label>

                    <select v-model="form.client_id" :disabled="isEditing" :class="inputClass(errors.client_id)"
                        @change="handleClientChange">
                        <option value="">
                            Selecione um cliente
                        </option>

                        <option v-for="client in clients" :key="client.id" :value="String(client.id)">
                            {{ client.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">
                        Motocicleta <span class="text-red-600">*</span>
                    </label>

                    <select v-model="form.motorcycle_id" :disabled="!form.client_id || loadingMotorcycles || isEditing"
                        :class="inputClass(errors.motorcycle_id)">
                        <option value="">
                            Selecione uma moto
                        </option>

                        <option v-for="motorcycle in motorcycles" :key="motorcycle.id" :value="String(motorcycle.id)">
                            {{ motorcycle.brand }} {{ motorcycle.model }} - {{ motorcycle.plate }}
                        </option>
                    </select>

                    <p v-if="loadingMotorcycles" class="mt-1 text-xs text-muted">
                        Carregando motocicletas...
                    </p>
                </div>
            </div>
        </div>

        <!-- Dados da OS -->
        <div class="border-t border-slate-200 pt-5">
            <h3 class="mb-4 text-sm font-bold text-foreground">
                Dados do serviço
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">
                        Título <span class="text-red-600">*</span>
                    </label>

                    <input v-model.trim="form.title" type="text" maxlength="255" placeholder="Ex.: Revisão completa"
                        :class="inputClass(errors.title)" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">
                        Descrição <span class="text-red-600">*</span>
                    </label>

                    <textarea v-model.trim="form.description" rows="3" placeholder="Descreva o serviço solicitado..."
                        :class="inputClass(errors.description)" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">
                            Status
                        </label>

                        <select v-model.number="form.status" :class="inputClass(errors.status)">
                            <option value="">
                                Padrão da oficina (Criado)
                            </option>

                            <option :value="RepairStatus.CREATED">
                                Criado
                            </option>

                            <option :value="RepairStatus.WAITING">
                                Aguardando
                            </option>

                            <option :value="RepairStatus.IN_MAINTENANCE">
                                Em manutenção
                            </option>

                            <option :value="RepairStatus.WAITING_PICKUP">
                                Aguardando retirada
                            </option>

                            <option :value="RepairStatus.DONE">
                                Concluído
                            </option>

                            <option :value="RepairStatus.CANCELED">
                                Cancelado
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">
                            Nível de urgência
                        </label>

                        <select v-model.number="form.urgency_level" :class="inputClass(errors.urgency_level)">
                            <option value="">
                                Padrão da oficina (Baixa)
                            </option>

                            <option :value="UrgencyLevel.VERY_LOW">
                                Muito baixa
                            </option>

                            <option :value="UrgencyLevel.LOW">
                                Baixa
                            </option>

                            <option :value="UrgencyLevel.MEDIUM">
                                Média
                            </option>

                            <option :value="UrgencyLevel.HIGH">
                                Alta
                            </option>

                            <option :value="UrgencyLevel.VERY_HIGH">
                                Muito alta
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">
                        Diagnóstico
                    </label>

                    <textarea v-model.trim="form.diagnosis" rows="3" placeholder="Diagnóstico técnico da motocicleta..."
                        :class="inputClass(errors.diagnosis)" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">
                        Observações
                    </label>

                    <textarea v-model.trim="form.observations" rows="3" placeholder="Observações adicionais..."
                        :class="inputClass(errors.observations)" />
                </div>
            </div>
        </div>

        <!-- Peças -->
        <div class="border-t border-slate-200 pt-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-foreground">
                        Peças utilizadas
                    </h3>

                    <p class="mt-1 text-xs text-muted">
                        Adicione as peças utilizadas na manutenção.
                    </p>
                </div>

                <button type="button"
                    class="shrink-0 rounded-lg bg-brand/10 px-3 py-2 text-xs font-semibold text-brand hover:bg-brand/20"
                    @click="addItem('parts')">
                    + Adicionar peça
                </button>
            </div>

            <div v-if="form.parts.length === 0"
                class="rounded-lg border border-dashed border-slate-300 p-5 text-center text-sm text-muted">
                Nenhuma peça adicionada.
            </div>

            <div v-for="(part, index) in form.parts" :key="`part-${index}`"
                class="mb-4 rounded-xl border border-slate-200 p-4">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-semibold text-foreground">
                        Peça {{ index + 1 }}
                    </span>

                    <button type="button" class="text-xs font-semibold text-red-600 hover:text-red-800"
                        @click="removeItem('parts', index)">
                        Remover
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-foreground">
                            Nome *
                        </label>

                        <input v-model.trim="part.name" type="text" maxlength="255" placeholder="Ex.: Filtro de óleo"
                            :class="inputClass(errors[`parts.${index}.name`])" />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-foreground">
                            Quantidade
                        </label>

                        <input v-model="part.quantity" type="number" min="0.01" step="0.01"
                            :class="inputClass(errors[`parts.${index}.quantity`])" />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-foreground">
                            Valor unitário (R$) *
                        </label>

                        <input v-model="part.unit_value" type="number" min="0" step="0.01" placeholder="0,00"
                            :class="inputClass(errors[`parts.${index}.unit_value`])" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-foreground">
                            Descrição
                        </label>

                        <textarea v-model.trim="part.description" rows="2" placeholder="Descrição opcional..."
                            :class="inputClass(errors[`parts.${index}.description`])" />
                    </div>
                </div>

                <p class="mt-3 text-right text-sm font-semibold text-foreground">
                    Subtotal: {{ formatCurrency(itemTotal(part)) }}
                </p>
            </div>
        </div>

        <!-- Serviços -->
        <div class="border-t border-slate-200 pt-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-foreground">
                        Mão de obra / Serviços
                    </h3>

                    <p class="mt-1 text-xs text-muted">
                        Adicione os serviços executados na motocicleta.
                    </p>
                </div>

                <button type="button"
                    class="shrink-0 rounded-lg bg-brand/10 px-3 py-2 text-xs font-semibold text-brand hover:bg-brand/20"
                    @click="addItem('services')">
                    + Adicionar serviço
                </button>
            </div>

            <div v-if="form.services.length === 0"
                class="rounded-lg border border-dashed border-slate-300 p-5 text-center text-sm text-muted">
                Nenhum serviço adicionado.
            </div>

            <div v-for="(service, index) in form.services" :key="`service-${index}`"
                class="mb-4 rounded-xl border border-slate-200 p-4">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-semibold text-foreground">
                        Serviço {{ index + 1 }}
                    </span>

                    <button type="button" class="text-xs font-semibold text-red-600 hover:text-red-800"
                        @click="removeItem('services', index)">
                        Remover
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-foreground">
                            Nome *
                        </label>

                        <input v-model.trim="service.name" type="text" maxlength="255" placeholder="Ex.: Troca de óleo"
                            :class="inputClass(errors[`services.${index}.name`])" />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-foreground">
                            Quantidade
                        </label>

                        <input v-model="service.quantity" type="number" min="0.01" step="0.01"
                            :class="inputClass(errors[`services.${index}.quantity`])" />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-foreground">
                            Valor unitário (R$) *
                        </label>

                        <input v-model="service.unit_value" type="number" min="0" step="0.01" placeholder="0,00"
                            :class="inputClass(errors[`services.${index}.unit_value`])" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-foreground">
                            Descrição
                        </label>

                        <textarea v-model.trim="service.description" rows="2" placeholder="Descrição opcional..."
                            :class="inputClass(errors[`services.${index}.description`])" />
                    </div>
                </div>

                <p class="mt-3 text-right text-sm font-semibold text-foreground">
                    Subtotal: {{ formatCurrency(itemTotal(service)) }}
                </p>
            </div>
        </div>

        <!-- Resumo -->
        <div class="rounded-xl bg-slate-50 p-4">
            <h3 class="mb-3 text-sm font-bold text-foreground">
                Resumo financeiro
            </h3>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted">Total de peças</span>

                    <span class="font-medium text-foreground">
                        {{ formatCurrency(partsTotal) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-muted">Total de serviços</span>

                    <span class="font-medium text-foreground">
                        {{ formatCurrency(servicesTotal) }}
                    </span>
                </div>

                <div class="flex justify-between border-t border-slate-200 pt-3">
                    <span class="font-bold text-foreground">
                        Total da OS
                    </span>

                    <span class="text-lg font-bold text-brand">
                        {{ formatCurrency(grandTotal) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
            <button type="button" :disabled="loading"
                class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-slate-50 disabled:opacity-60"
                @click="emit('cancel')">
                Cancelar
            </button>

            <button type="submit" :disabled="loading"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-60">
                {{ loading ? 'Salvando...' : (isEditing ? 'Salvar alterações' : 'Cadastrar OS') }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'

import Alert from '~/components/ui/Alert.vue'

import { AlertVariant } from '~/features/ui/types/AlertVariant'
import { RepairStatus } from '~/features/ui/types/RepairStatus'
import { UrgencyLevel } from '~/features/ui/types/UrgencyLevel'

import { getApiMessage } from '~/features/shared/messages/ApiMessages'

const props = defineProps({
    repairOrder: {
        type: Object,
        default: null,
    },

    clients: {
        type: Array,
        default: () => [],
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

const emit = defineEmits([
    'submit',
    'cancel',
])

const { $api } = useNuxtApp()

const isEditing = computed(() => Boolean(props.repairOrder?.id))

const motorcycles = ref([])
const loadingMotorcycles = ref(false)
const localErrors = ref({})

function emptyItem() {
    return {
        name: '',
        description: '',
        quantity: '1.00',
        unit_value: '0.00',
    }
}


function createForm(response) {
    const order = response?.data ?? response

    return {
        client_id: String(
            order?.client?.id
            ?? order?.client_id
            ?? order?.motorcycle?.client_id
            ?? ''
        ),

        motorcycle_id: String(
            order?.motorcycle?.id
            ?? order?.motorcycle_id
            ?? ''
        ),

        title: order?.title ?? '',
        description: order?.description ?? '',

        status: order?.status?.value ?? order?.status ?? '',

        urgency_level:
            order?.urgency_level?.value
            ?? order?.urgency_level
            ?? '',

        diagnosis: order?.diagnosis ?? '',
        observations: order?.observations ?? '',

        parts: (order?.parts ?? []).map((item) => ({
            name: item.repair_part_name ?? item.name ?? '',
            description:
                item.repair_part_description
                ?? item.description
                ?? '',

            quantity: Number(
                item.repair_part_quantity
                ?? item.quantity
                ?? 1
            ).toFixed(2),

            unit_value: Number(
                item.repair_part_unit_value
                ?? item.unit_value
                ?? 0
            ).toFixed(2),
        })),

        services: (order?.services ?? []).map((item) => ({
            name: item.repair_service_name ?? item.name ?? '',
            description:
                item.repair_service_description
                ?? item.description
                ?? '',

            quantity: Number(
                item.repair_service_quantity
                ?? item.quantity
                ?? 1
            ).toFixed(2),

            unit_value: Number(
                item.repair_service_unit_value
                ?? item.unit_value
                ?? 0
            ).toFixed(2),
        })),
    }
}


const form = ref(createForm(props.repairOrder))

const errors = computed(() => ({
    ...localErrors.value,
    ...props.errors,
}))

const validationMessages = computed(() => [
    ...new Set(
        Object.values(errors.value).filter(
            (message) => typeof message === 'string' && message.length
        )
    ),
])

const partsTotal = computed(() =>
    calculateTotal(form.value.parts)
)

const servicesTotal = computed(() =>
    calculateTotal(form.value.services)
)

const grandTotal = computed(() =>
    partsTotal.value + servicesTotal.value
)

function calculateTotal(items) {
    return items.reduce((total, item) => {
        return total + itemTotal(item)
    }, 0)
}

function itemTotal(item) {
    return Number(item.quantity || 0) * Number(item.unit_value || 0)
}

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value || 0)
}

function inputClass(error) {
    return [
        'w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-foreground outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-2 focus:ring-brand/15',
        error ? 'border-red-500 focus:border-red-500 focus:ring-red-500/15' : '',
    ]
}

function addItem(type) {
    form.value[type].push(emptyItem())
}

function removeItem(type, index) {
    form.value[type].splice(index, 1)
}

async function loadMotorcycles(clientId) {
    motorcycles.value = []

    if (!clientId) return

    loadingMotorcycles.value = true

    try {
        const token = sessionStorage.getItem('motoflow_access_token')

        if (!token) return

        const response = await $api.get(
            `/motorcycle/${clientId}/all`,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        )

        const result = response.data

        motorcycles.value = Array.isArray(result?.data?.data)
            ? result.data.data
            : Array.isArray(result?.data)
                ? result.data
                : []
    } catch (error) {
        localErrors.value = {
            ...localErrors.value,
            motorcycle_id: getApiMessage(error.response?.data?.message),
        }
    } finally {
        loadingMotorcycles.value = false
    }
}

async function handleClientChange() {
    form.value.motorcycle_id = ''
    localErrors.value = {}

    await loadMotorcycles(form.value.client_id)
}

// Atualiza o formulário quando a OS selecionada mudar.
watch(
    () => props.repairOrder,
    async (order) => {
        form.value = createForm(order)
        localErrors.value = {}

        if (import.meta.client && form.value.client_id) {
            await loadMotorcycles(form.value.client_id)
        } else {
            motorcycles.value = []
        }
    }
)

// Carregamento inicial somente no cliente.
onMounted(async () => {
    if (form.value.client_id) {
        await loadMotorcycles(form.value.client_id)
    }
})

function validate() {
    const messages = {}

    const required = (value) =>
        String(value ?? '').trim().length > 0

    if (!required(form.value.client_id)) {
        messages.client_id = 'Selecione um cliente.'
    }

    if (!required(form.value.motorcycle_id)) {
        messages.motorcycle_id = 'Selecione uma motocicleta.'
    }

    if (!required(form.value.title)) {
        messages.title = 'Informe o título da ordem de serviço.'
    } else if (form.value.title.length > 255) {
        messages.title = 'O título deve ter no máximo 255 caracteres.'
    }

    if (!required(form.value.description)) {
        messages.description = 'Informe a descrição da ordem de serviço.'
    }

    for (const type of ['parts', 'services']) {
        form.value[type].forEach((item, index) => {
            if (!required(item.name)) {
                messages[`${type}.${index}.name`] =
                    `Informe o nome do item ${index + 1}.`
            } else if (item.name.length > 255) {
                messages[`${type}.${index}.name`] =
                    'O nome deve ter no máximo 255 caracteres.'
            }

            if (
                !required(item.unit_value)
                || !Number.isFinite(Number(item.unit_value))
                || Number(item.unit_value) < 0
            ) {
                messages[`${type}.${index}.unit_value`] =
                    'Informe um valor unitário válido.'
            }

            if (
                item.quantity !== ''
                && (
                    !Number.isFinite(Number(item.quantity))
                    || Number(item.quantity) <= 0
                )
            ) {
                messages[`${type}.${index}.quantity`] =
                    'A quantidade deve ser maior que zero.'
            }
        })
    }

    localErrors.value = messages

    return Object.keys(messages).length === 0
}

function toPayload() {
    const payload = {
        motorcycle_id: Number(form.value.motorcycle_id),

        title: form.value.title.trim(),
        description: form.value.description.trim(),

        diagnosis: form.value.diagnosis.trim() || null,
        observations: form.value.observations.trim() || null,

        parts: form.value.parts.map((item) => ({
            name: item.name.trim(),
            description: item.description.trim() || null,
            quantity: Number(item.quantity || 1).toFixed(2),
            unit_value: Number(item.unit_value).toFixed(2),
        })),

        services: form.value.services.map((item) => ({
            name: item.name.trim(),
            description: item.description.trim() || null,
            quantity: Number(item.quantity || 1).toFixed(2),
            unit_value: Number(item.unit_value).toFixed(2),
        })),
    }

    if (form.value.status !== '' && form.value.status !== null) {
        payload.status = Number(form.value.status)
    }

    if (
        form.value.urgency_level !== ''
        && form.value.urgency_level !== null
    ) {
        payload.urgency_level = Number(form.value.urgency_level)
    }

    return payload
}

function handleSubmit() {
    if (props.loading || !validate()) return

    emit('submit', toPayload())
}
</script>
