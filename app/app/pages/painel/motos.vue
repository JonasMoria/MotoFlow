<template>
    <section>
        <!-- Cabeçalho -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-sm font-bold text-foreground md:text-2xl">
                    Motos
                </h1>

                <p class="mt-2 text-[9px] text-muted md:text-sm">
                    Gerencie as motos cadastradas na oficina.
                </p>
            </div>

            <button type="button" :disabled="!selectedClientId"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                @click="openModal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                </svg>

                Cadastrar Moto
            </button>
        </div>

        <!-- Feedback -->
        <div class="mt-5">
            <Alert v-if="alert.visible" :variant="alert.variant" :message="alert.message"
                @close="alert.visible = false" />
        </div>

        <!-- Seleção de cliente -->
        <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4">
            <label for="motorcycle-client" class="mb-2 block text-sm font-semibold text-foreground">
                Selecione o cliente
            </label>

            <select id="motorcycle-client" v-model="selectedClientId" :disabled="loadingClients || clients.length === 0"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-foreground outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/15 sm:max-w-md disabled:opacity-60"
                @change="handleClientChange">
                <option value="" disabled>
                    Selecione um cliente
                </option>

                <option v-for="client in clients" :key="client.id" :value="String(client.id)">
                    {{ client.name }}
                </option>
            </select>

            <p v-if="loadingClients" class="mt-2 text-xs text-muted">
                Carregando clientes...
            </p>

            <p v-else-if="clients.length === 0" class="mt-2 text-xs text-muted">
                Nenhum cliente cadastrado. Cadastre um cliente antes de adicionar uma moto.
            </p>
        </div>

        <!-- Listagem -->
        <div class="mt-6">
            <div v-if="loadingMotorcycles" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <div v-for="item in 8" :key="item"
                    class="h-56 animate-pulse rounded-xl border border-slate-200 bg-white" />
            </div>

            <div v-else-if="motorcyclesError"
                class="rounded-xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
                <p>{{ motorcyclesError }}</p>

                <button type="button" class="mt-3 font-semibold underline" @click="fetchMotorcycles">
                    Tentar novamente
                </button>
            </div>

            <div v-else-if="!selectedClientId" class="rounded-xl border border-slate-200 bg-white p-8 text-center">
                <p class="text-sm font-semibold text-foreground">
                    Selecione um cliente
                </p>

                <p class="mt-1 text-sm text-muted">
                    Escolha um cliente para visualizar suas motos.
                </p>
            </div>

            <div v-else-if="motorcycles.length === 0"
                class="rounded-xl border border-slate-200 bg-white p-8 text-center">
                <p class="text-sm font-semibold text-foreground">
                    Nenhuma moto cadastrada
                </p>

                <p class="mt-1 text-sm text-muted">
                    Cadastre a primeira moto deste cliente para começar.
                </p>
            </div>

            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <MotorcycleCard v-for="motorcycle in motorcycles" :key="motorcycle.id" :motorcycle="motorcycle"
                    @edit="handleEdit" @delete="handleDelete" />
            </div>

            <p v-if="!loadingMotorcycles && !motorcyclesError && motorcycles.length > 0"
                class="mt-6 text-sm text-muted">
                {{ motorcycles.length }}
                {{ motorcycles.length === 1 ? 'moto encontrada' : 'motos encontradas' }}
            </p>
        </div>

        <!-- Modal de cadastro/edição -->
        <Modal v-model="modalOpen" :title="selectedMotorcycle ? 'Editar Moto' : 'Cadastrar Moto'" :description="selectedMotorcycle
            ? 'Atualize os dados da moto.'
            : 'Preencha os dados para cadastrar uma nova moto.'" size="lg" @close="handleModalClose">
            <MotorcycleForm :key="selectedMotorcycle?.id ?? 'new-motorcycle'" :motorcycle="selectedMotorcycle"
                :loading="saving" :errors="formErrors" @submit="handleSubmit" @cancel="closeModal" />
        </Modal>

        <!-- Confirmação de exclusão -->
        <ConfirmationModal v-model="confirmationOpen" title="Excluir moto"
            :message="`Tem certeza de que deseja excluir a moto '${motorcycleToDelete?.brand ?? ''} ${motorcycleToDelete?.model ?? ''}'? Esta ação não poderá ser desfeita.`"
            confirm-text="Sim, excluir" cancel-text="Cancelar" loading-text="Excluindo..." :loading="deletingMotorcycle"
            @confirm="confirmDeleteMotorcycle" @cancel="cancelDeleteMotorcycle" />
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'

import Alert from '~/components/ui/Alert.vue'
import ConfirmationModal from '~/components/ui/ConfirmationModal.vue'
import Modal from '~/components/ui/Modal.vue'

import MotorcycleCard from '~/features/motorcycles/components/MotorcycleCard.vue'
import MotorcycleForm from '~/features/motorcycles/components/MotorcycleForm.vue'

import { getApiMessage } from '~/features/shared/messages/ApiMessages'
import { HttpStatus } from '~/features/shared/types/HttpStatus'
import { AlertVariant } from '~/features/ui/types/AlertVariant'

const { $api } = useNuxtApp()

definePageMeta({
    layout: 'dashboard',
})

useHead({
    title: 'Motos dos Clientes | MotoFlow',
})

const alert = ref({
    visible: false,
    variant: AlertVariant.INFO,
    message: '',
})

const clients = ref([])
const loadingClients = ref(false)
const selectedClientId = ref('')

const motorcycles = ref([])
const loadingMotorcycles = ref(false)
const motorcyclesError = ref('')

const modalOpen = ref(false)
const saving = ref(false)
const formErrors = ref({})
const selectedMotorcycle = ref(null)

const confirmationOpen = ref(false)
const deletingMotorcycle = ref(false)
const motorcycleToDelete = ref(null)

onMounted(() => {
    fetchClients()
})

async function getToken() {
    return sessionStorage.getItem('motoflow_access_token')
}

async function fetchClients() {
    loadingClients.value = true

    try {
        const token = await getToken()

        if (!token) {
            motorcyclesError.value = getApiMessage('USER.UNAUTHENTICATED')
            return
        }

        const response = await $api.get('/client', {
            params: {
                page: 1,
                per_page: 100,
            },
            headers: {
                Authorization: `Bearer ${token}`,
            },
        })

        const result = response.data

        clients.value = Array.isArray(result?.data?.data)
            ? result.data.data
            : []

        if (clients.value.length > 0) {
            selectedClientId.value = String(clients.value[0].id)
            await fetchMotorcycles()
        }
    } catch (error) {
        motorcyclesError.value = getApiMessage(
            error.response?.data?.message
        )
    } finally {
        loadingClients.value = false
    }
}

async function handleClientChange() {
    alert.value.visible = false
    selectedMotorcycle.value = null
    motorcycles.value = []

    await fetchMotorcycles()
}

async function fetchMotorcycles() {
    if (!selectedClientId.value) {
        motorcycles.value = []
        return
    }

    loadingMotorcycles.value = true
    motorcyclesError.value = ''

    try {
        const token = await getToken()

        if (!token) {
            motorcyclesError.value = getApiMessage('USER.UNAUTHENTICATED')
            return
        }

        const response = await $api.get(
            `/motorcycle/${selectedClientId.value}/all`,
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
        motorcyclesError.value = getApiMessage(
            error.response?.data?.message
        )
    } finally {
        loadingMotorcycles.value = false
    }
}

function openModal() {
    alert.value.visible = false
    formErrors.value = {}
    selectedMotorcycle.value = null
    modalOpen.value = true
}

function closeModal() {
    if (saving.value) return

    modalOpen.value = false
    formErrors.value = {}
    selectedMotorcycle.value = null
}

function handleModalClose() {
    if (!saving.value) {
        formErrors.value = {}
        selectedMotorcycle.value = null
    }
}

function handleEdit(motorcycle) {
    alert.value.visible = false
    formErrors.value = {}
    selectedMotorcycle.value = { ...motorcycle }
    modalOpen.value = true
}

async function handleSubmit(motorcycle) {
    alert.value.visible = false
    saving.value = true
    formErrors.value = {}

    try {
        const token = await getToken()

        if (!token) {
            alert.value = {
                visible: true,
                variant: AlertVariant.ERROR,
                message: getApiMessage('USER.UNAUTHENTICATED'),
            }
            return
        }

        let response

        if (selectedMotorcycle.value) {
            response = await $api.patch(
                `/motorcycle/${selectedClientId.value}/${selectedMotorcycle.value.id}`,
                motorcycle,
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            )
        } else {
            response = await $api.post(
                `/motorcycle/${selectedClientId.value}`,
                motorcycle,
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            )
        }

        const httpStatus = response.status

        const isCreated = !selectedMotorcycle.value
            && httpStatus === HttpStatus.CREATED

        const isUpdated = Boolean(selectedMotorcycle.value)
            && httpStatus >= 200
            && httpStatus < 300

        if (isCreated || isUpdated) {
            alert.value = {
                visible: true,
                variant: AlertVariant.SUCCESS,
                message: isCreated
                    ? 'Moto cadastrada com sucesso.'
                    : 'Moto atualizada com sucesso.',
            }

            modalOpen.value = false
            selectedMotorcycle.value = null

            await fetchMotorcycles()
            return
        }

        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(response.data?.message),
        }
    } catch (error) {
        const responseData = error.response?.data

        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(responseData?.message),
        }

        if (responseData?.errors) {
            formErrors.value = Object.fromEntries(
                Object.entries(responseData.errors).map(([field, messages]) => [
                    field,
                    Array.isArray(messages)
                        ? getApiMessage(messages[0])
                        : getApiMessage(messages),
                ])
            )
        }
    } finally {
        saving.value = false
    }
}

function handleDelete(motorcycle) {
    motorcycleToDelete.value = motorcycle
    confirmationOpen.value = true
}

function cancelDeleteMotorcycle() {
    if (deletingMotorcycle.value) return

    confirmationOpen.value = false
    motorcycleToDelete.value = null
}

async function confirmDeleteMotorcycle() {
    if (!motorcycleToDelete.value || deletingMotorcycle.value) return

    deletingMotorcycle.value = true

    try {
        const token = await getToken()

        if (!token) {
            alert.value = {
                visible: true,
                variant: AlertVariant.ERROR,
                message: getApiMessage('USER.UNAUTHENTICATED'),
            }
            return
        }

        await $api.delete(
            `/motorcycle/${selectedClientId.value}/${motorcycleToDelete.value.id}`,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        )

        confirmationOpen.value = false

        alert.value = {
            visible: true,
            variant: AlertVariant.SUCCESS,
            message: 'Moto excluída com sucesso.',
        }

        motorcycleToDelete.value = null

        await fetchMotorcycles()
    } catch (error) {
        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(error.response?.data?.message),
        }
    } finally {
        deletingMotorcycle.value = false
    }
}
</script>
