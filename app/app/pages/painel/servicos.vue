<template>
    <section>
        <!-- Cabeçalho -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-sm font-bold text-foreground md:text-2xl">
                    Serviços
                </h1>
                <p class="mt-2 text-[9px] text-muted md:text-sm">
                    Gerencie as ordens de serviço da oficina.
                </p>
            </div>

            <button type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-semibold text-white transition hover:opacity-90 md:text-sm"
                @click="openCreateModal">
                <span class="text-lg leading-none">+</span>
                Nova Ordem de Serviço
            </button>
        </div>

        <!-- Feedback -->
        <div class="mt-5">
            <Alert v-if="alert.visible" :variant="alert.variant" :message="alert.message"
                @close="alert.visible = false" />
        </div>

        <!-- Filtros -->
        <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="filter-client" class="mb-1.5 block text-sm font-medium text-foreground">
                        Cliente
                    </label>

                    <select id="filter-client" v-model="filterClientId"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/15">
                        <option value="">Todos os clientes</option>
                        <option v-for="client in clients" :key="client.id" :value="String(client.id)">
                            {{ client.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label for="filter-search" class="mb-1.5 block text-sm font-medium text-foreground">
                        Buscar ordem
                    </label>

                    <input id="filter-search" v-model.trim="search" type="search" placeholder="Título, moto ou placa..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/15" />
                </div>
            </div>
        </div>

        <!-- Listagem -->
        <div class="mt-6">
            <div v-if="loading" class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div v-for="item in 4" :key="item"
                    class="h-64 animate-pulse rounded-xl border border-slate-200 bg-white" />
            </div>

            <div v-else-if="loadError" class="rounded-xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
                <p>{{ loadError }}</p>
                <button type="button" class="mt-3 font-semibold underline" @click="fetchRepairOrders">
                    Tentar novamente
                </button>
            </div>

            <div v-else-if="filteredOrders.length === 0"
                class="rounded-xl border border-slate-200 bg-white p-8 text-center">
                <p class="text-sm font-semibold text-foreground">
                    Nenhuma ordem de serviço encontrada
                </p>
                <p class="mt-1 text-sm text-muted">
                    Cadastre uma nova ordem de serviço para começar.
                </p>
            </div>

            <div v-else class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <RepairOrderCard v-for="order in filteredOrders" :key="order.id" :repair-order="order"
                    @edit="handleEdit" @delete="handleDelete" @download="handleDownload" @send="handleSendToClient" />
            </div>

            <p v-if="!loading && !loadError && filteredOrders.length" class="mt-5 text-sm text-muted">
                {{ filteredOrders.length }}
                {{ filteredOrders.length === 1 ? 'ordem encontrada' : 'ordens encontradas' }}
            </p>
        </div>

        <!-- Modal -->
        <Modal v-model="modalOpen" :title="selectedOrder ? 'Editar Ordem de Serviço' : 'Nova Ordem de Serviço'"
            :description="selectedOrder
                ? 'Atualize os dados, peças e serviços da ordem.'
                : 'Preencha os dados da ordem de serviço.'" size="lg" @close="handleModalClose">
            <RepairOrderForm :key="selectedOrder?.id ?? 'new-order'" :repair-order="selectedOrder" :clients="clients"
                :loading="saving" :errors="formErrors" @submit="handleSubmit" @cancel="closeModal" />
        </Modal>

        <!-- Confirmação -->
        <ConfirmationModal v-model="confirmationOpen" title="Excluir ordem de serviço"
            :message="`Tem certeza de que deseja excluir a ordem '${orderToDelete?.title ?? ''}'? Esta ação não poderá ser desfeita.`"
            confirm-text="Sim, excluir" cancel-text="Cancelar" loading-text="Excluindo..." :loading="deleting"
            @confirm="confirmDelete" @cancel="cancelDelete" />
    </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'

import Alert from '~/components/ui/Alert.vue'
import Modal from '~/components/ui/Modal.vue'
import ConfirmationModal from '~/components/ui/ConfirmationModal.vue'

import RepairOrderCard from '~/features/repair-orders/components/RepairOrderCard.vue'
import RepairOrderForm from '~/features/repair-orders/components/RepairOrderForm.vue'

import { getApiMessage } from '~/features/shared/messages/ApiMessages'
import { HttpStatus } from '~/features/shared/types/HttpStatus'
import { AlertVariant } from '~/features/ui/types/AlertVariant'

const { $api } = useNuxtApp()

definePageMeta({
    layout: 'dashboard',
})

useHead({
    title: 'Meus Serviços | MotoFlow',
})

const alert = ref({
    visible: false,
    variant: AlertVariant.INFO,
    message: '',
})

const clients = ref([])
const orders = ref([])

const loading = ref(false)
const loadError = ref('')

const search = ref('')
const filterClientId = ref('')

const modalOpen = ref(false)
const saving = ref(false)
const formErrors = ref({})
const selectedOrder = ref(null)

const confirmationOpen = ref(false)
const deleting = ref(false)
const orderToDelete = ref(null)

const filteredOrders = computed(() => {
    return orders.value.filter((order) => {
        const clientId = String(
            order.client_id
            ?? order.motorcycle?.client_id
            ?? order.motorcycle?.client?.id
            ?? ''
        )

        const matchesClient = !filterClientId.value
            || clientId === filterClientId.value

        const term = search.value.toLowerCase()

        const searchable = [
            order.title,
            order.motorcycle?.brand,
            order.motorcycle?.model,
            order.motorcycle?.plate,
            order.client?.name,
            order.motorcycle?.client?.name,
        ].filter(Boolean).join(' ').toLowerCase()

        return matchesClient && searchable.includes(term)
    })
})

onMounted(async () => {
    await Promise.all([
        fetchClients(),
        fetchRepairOrders(),
    ])
})

function getList(response) {
    const result = response?.data

    if (Array.isArray(result?.data?.data)) return result.data.data
    if (Array.isArray(result?.data)) return result.data
    if (Array.isArray(result)) return result

    return []
}

function getToken() {
    return sessionStorage.getItem('motoflow_access_token')
}

function authConfig(token) {
    return {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    }
}

async function fetchClients() {
    try {
        const token = getToken()

        if (!token) return

        const response = await $api.get('/client', {
            ...authConfig(token),
            params: {
                page: 1,
                per_page: 100,
            },
        })

        clients.value = getList(response)
    } catch (error) {
        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(error.response?.data?.message),
        }
    }
}

async function fetchRepairOrders() {
    loading.value = true
    loadError.value = ''

    try {
        const token = getToken()

        if (!token) {
            loadError.value = getApiMessage('USER.UNAUTHENTICATED')
            return
        }

        const response = await $api.get(
            '/repair-order/list-actives',
            authConfig(token)
        )

        orders.value = getList(response)
    } catch (error) {
        loadError.value = getApiMessage(error.response?.data?.message)
    } finally {
        loading.value = false
    }
}

function openCreateModal() {
    alert.value.visible = false
    formErrors.value = {}
    selectedOrder.value = null
    modalOpen.value = true
}

function closeModal() {
    if (saving.value) return

    modalOpen.value = false
    selectedOrder.value = null
    formErrors.value = {}
}

function handleModalClose() {
    if (!saving.value) {
        selectedOrder.value = null
        formErrors.value = {}
    }
}

async function handleEdit(order) {
    alert.value.visible = false
    formErrors.value = {}

    try {
        const token = getToken()

        if (!token) {
            throw new Error('USER.UNAUTHENTICATED')
        }

        const response = await $api.get(
            `/repair-order/${order.id}`,
            authConfig(token)
        )

        selectedOrder.value = response.data?.data ?? response.data
        modalOpen.value = true
    } catch (error) {
        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(
                error.response?.data?.message
                ?? error.message
            ),
        }
    }
}

async function handleSubmit(payload) {
    alert.value.visible = false
    saving.value = true
    formErrors.value = {}

    try {
        const token = getToken()

        if (!token) {
            alert.value = {
                visible: true,
                variant: AlertVariant.ERROR,
                message: getApiMessage('USER.UNAUTHENTICATED'),
            }
            return
        }

        let response

        if (selectedOrder.value) {
            const motorcycleId = payload.motorcycle_id

            const body = { ...payload }
            delete body.motorcycle_id

            response = await $api.post(
                `/repair-order/${motorcycleId}/${selectedOrder.value.id}`,
                body,
                authConfig(token)
            )
        } else {
            const motorcycleId = payload.motorcycle_id

            const body = { ...payload }
            delete body.motorcycle_id

            response = await $api.post(
                `/repair-order/${motorcycleId}`,
                body,
                authConfig(token)
            )
        }

        const isCreated = !selectedOrder.value
            && response.status === HttpStatus.CREATED

        const isUpdated = Boolean(selectedOrder.value)
            && response.status >= 200
            && response.status < 300

        if (!isCreated && !isUpdated) {
            throw new Error(response.data?.message ?? 'Erro ao salvar a ordem.')
        }

        alert.value = {
            visible: true,
            variant: AlertVariant.SUCCESS,
            message: isCreated
                ? 'Ordem de serviço cadastrada com sucesso.'
                : 'Ordem de serviço atualizada com sucesso.',
        }

        modalOpen.value = false
        selectedOrder.value = null

        await fetchRepairOrders()
    } catch (error) {
        const responseData = error.response?.data

        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(
                responseData?.message ?? error.message
            ),
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

function handleDelete(order) {
    orderToDelete.value = order
    confirmationOpen.value = true
}

function cancelDelete() {
    if (deleting.value) return

    confirmationOpen.value = false
    orderToDelete.value = null
}

async function confirmDelete() {
    if (!orderToDelete.value || deleting.value) return

    deleting.value = true

    try {
        const token = getToken()

        if (!token) {
            throw new Error('USER.UNAUTHENTICATED')
        }

        await $api.delete(
            `/repair-order/${orderToDelete.value.id}`,
            authConfig(token)
        )

        confirmationOpen.value = false
        orderToDelete.value = null

        alert.value = {
            visible: true,
            variant: AlertVariant.SUCCESS,
            message: 'Ordem de serviço excluída com sucesso.',
        }

        await fetchRepairOrders()
    } catch (error) {
        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(
                error.response?.data?.message ?? error.message
            ),
        }
    } finally {
        deleting.value = false
    }
}

async function handleDownload(order) {
    try {
        const token = getToken()

        if (!token) {
            throw new Error('USER.UNAUTHENTICATED')
        }

        const response = await $api.get(
            `/repair-order/${order.id}/download`,
            {
                ...authConfig(token),
                responseType: 'blob',
            }
        )

        const contentType = response.headers['content-type'] || 'application/pdf'
        const blob = new Blob([response.data], { type: contentType })
        const url = window.URL.createObjectURL(blob)

        const link = document.createElement('a')
        link.href = url
        link.download = `ordem-servico-${order.id}.pdf`
        document.body.appendChild(link)
        link.click()
        link.remove()

        window.URL.revokeObjectURL(url)
    } catch (error) {
        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(error.response?.data?.message ?? error.message),
        }
    }
}

async function handleSendToClient(order) {
    try {
        const token = getToken()

        if (!token) {
            throw new Error('USER.UNAUTHENTICATED')
        }

        await $api.get(
            `/repair-order/${order.id}/sent-to-client`,
            authConfig(token)
        )

        alert.value = {
            visible: true,
            variant: AlertVariant.SUCCESS,
            message: 'Ordem de serviço enviada ao cliente com sucesso.',
        }
    } catch (error) {
        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(error.response?.data?.message ?? error.message),
        }
    }
}
</script>
