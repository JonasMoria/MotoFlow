<template>
    <section>
        <!-- Cabeçalho -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-sm font-bold text-foreground md:text-2xl">
                    Clientes
                </h1>
                <p class="mt-2 text-[9px] text-muted md:text-sm">
                    Gerencie os clientes cadastrados na oficina.
                </p>
            </div>

            <button type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-semibold text-white transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 md:text-sm"
                @click="openModal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                </svg>

                Cadastrar Cliente
            </button>
        </div>

        <!-- Feedback -->
        <div class="mt-5">
            <Alert v-if="alert.visible" :variant="alert.variant" :message="alert.message"
                @close="alert.visible = false" />
        </div>

        <!-- Listagem -->
        <div class="mt-6">
            <div v-if="loadingClients" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <div v-for="item in 8" :key="item"
                    class="h-64 animate-pulse rounded-xl border border-slate-200 bg-white" />
            </div>

            <div v-else-if="clientsError" class="rounded-xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
                <p>{{ clientsError }}</p>

                <button type="button" class="mt-3 font-semibold underline"
                    @click="fetchClients(pagination.current_page)">
                    Tentar novamente
                </button>
            </div>

            <div v-else-if="clients.length === 0" class="rounded-xl border border-slate-200 bg-white p-8 text-center">
                <p class="text-sm font-semibold text-foreground">
                    Nenhum cliente cadastrado
                </p>
                <p class="mt-1 text-sm text-muted">
                    Cadastre seu primeiro cliente para começar.
                </p>
            </div>

            <template v-else>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                    <ClientCard v-for="client in clients" :key="client.id" :client="client" @edit="handleEdit"
                        @delete="handleDelete" />
                </div>

                <!-- Paginação -->
                <div class="mt-6 flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <p class="text-sm text-muted">
                        {{ pagination.total }}
                        {{ pagination.total === 1 ? 'cliente encontrado' : 'clientes encontrados' }}
                    </p>

                    <div class="flex items-center gap-2">
                        <button type="button"
                            class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 px-3 text-sm text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="pagination.current_page <= 1 || loadingClients" aria-label="Página anterior"
                            @click="changePage(pagination.current_page - 1)">
                            Anterior
                        </button>

                        <span class="min-w-16 text-center text-sm font-medium text-slate-600">
                            {{ pagination.current_page }} / {{ pagination.last_page }}
                        </span>

                        <button type="button"
                            class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 px-3 text-sm text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="pagination.current_page >= pagination.last_page || loadingClients"
                            aria-label="Próxima página" @click="changePage(pagination.current_page + 1)">
                            Próxima
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <Modal v-model="modalOpen" :title="selectedClient ? 'Editar Cliente' : 'Cadastrar Cliente'" :description="selectedClient
            ? 'Atualize os dados do cliente.'
            : 'Preencha os dados para cadastrar um novo cliente.'
            " size="lg" @close="handleModalClose">
            <ClientForm :key="selectedClient?.id ?? 'new-client'" :client="selectedClient" :loading="saving"
                :errors="formErrors" :reset-key="formResetKey" @submit="handleSubmit" @cancel="closeModal" />
        </Modal>

        <ConfirmationModal v-model="confirmationOpen" title="Excluir cliente" :message="`Tem certeza de que deseja excluir o cliente '${clientToDelete?.name ?? ''}'? Esta ação não poderá ser desfeita.`
            " confirm-text="Sim, excluir" cancel-text="Cancelar" loading-text="Excluindo..." :loading="deletingClient"
            @confirm="confirmDeleteClient" @cancel="cancelDeleteClient" />
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import Alert from '~/components/ui/Alert.vue'
import ConfirmationModal from '~/components/ui/ConfirmationModal.vue'
import Modal from '~/components/ui/Modal.vue'
import ClientCard from '~/features/clients/components/ClientCard.vue'
import ClientForm from '~/features/clients/components/ClientForm.vue'
import { getApiMessage } from '~/features/shared/messages/ApiMessages'
import { HttpStatus } from '~/features/shared/types/HttpStatus'
import { AlertVariant } from '~/features/ui/types/AlertVariant'

const { $api } = useNuxtApp()

definePageMeta({
    layout: 'dashboard',
})

useHead({
    title: 'Meus Clientes | MotoFlow',
})

const alert = ref({
    visible: false,
    variant: AlertVariant.INFO,
    message: '',
})

const modalOpen = ref(false)
const saving = ref(false)
const formErrors = ref({})
const formResetKey = ref(0)
const selectedClient = ref(null)

const clients = ref([])
const loadingClients = ref(false)
const clientsError = ref('')

const confirmationOpen = ref(false)
const deletingClient = ref(false)
const clientToDelete = ref(null)

const pagination = ref({
    current_page: 1,
    per_page: 15,
    total: 0,
    last_page: 1,
    from: null,
    to: null,
})

onMounted(() => {
    fetchClients()
})

async function fetchClients(page = 1) {
    loadingClients.value = true
    clientsError.value = ''

    try {
        const token = sessionStorage.getItem('motoflow_access_token')

        if (!token) {
            clientsError.value = getApiMessage('USER.UNAUTHENTICATED')
            return
        }

        const response = await $api.get('/client', {
            params: { page },
            headers: {
                Authorization: `Bearer ${token}`,
            },
        })

        const result = response.data

        clients.value = Array.isArray(result?.data?.data)
            ? result.data.data
            : []

        pagination.value = result?.pagination ?? {
            current_page: page,
            per_page: clients.value.length,
            total: clients.value.length,
            last_page: 1,
            from: clients.value.length ? 1 : null,
            to: clients.value.length || null,
        }
    } catch (error) {
        clientsError.value = getApiMessage(error.response?.data?.message)
    } finally {
        loadingClients.value = false
    }
}

function openModal() {
    alert.value.visible = false
    formErrors.value = {}
    selectedClient.value = null
    modalOpen.value = true
}

function closeModal() {
    if (saving.value) {
        return
    }

    modalOpen.value = false
    formErrors.value = {}
    selectedClient.value = null
}

function handleModalClose() {
    if (!saving.value) {
        formErrors.value = {}
        selectedClient.value = null
    }
}

function handleEdit(client) {
    alert.value.visible = false
    formErrors.value = {}
    selectedClient.value = { ...client }
    modalOpen.value = true
}

async function handleSubmit(client) {
    alert.value.visible = false
    saving.value = true
    formErrors.value = {}

    try {
        const token = sessionStorage.getItem('motoflow_access_token')

        if (!token) {
            alert.value = {
                visible: true,
                variant: AlertVariant.ERROR,
                message: getApiMessage('USER.UNAUTHENTICATED'),
            }
            return
        }

        let response

        if (selectedClient.value) {
            response = await $api.patch(
                `/client/${selectedClient.value.id}`,
                client.toFormData(),
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                },
            )
        } else {
            response = await $api.post('/client', client.toFormData(), {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
        }

        const httpStatus = response.status;

        const isCreated = !selectedClient.value
            && httpStatus === HttpStatus.CREATED
        const isUpdated = Boolean(selectedClient.value)
            && httpStatus >= 200
            && httpStatus < 300

        if (isCreated || isUpdated) {
            alert.value = {
                visible: true,
                variant: AlertVariant.SUCCESS,
                message: isCreated
                    ? 'Cliente cadastrado com sucesso.'
                    : 'Cliente atualizado com sucesso.',
            }

            modalOpen.value = false
            selectedClient.value = null
            formResetKey.value++

            await fetchClients(pagination.value.current_page)
            return
        }

        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(response.message),
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
                ]),
            )
        }
    } finally {
        saving.value = false
    }
}

function changePage(page) {
    if (
        page < 1
        || page > pagination.value.last_page
        || loadingClients.value
    ) {
        return
    }

    fetchClients(page)
}

function handleDelete(client) {
    clientToDelete.value = client
    confirmationOpen.value = true
}

function cancelDeleteClient() {
    if (deletingClient.value) {
        return
    }

    confirmationOpen.value = false
    clientToDelete.value = null
}

async function confirmDeleteClient() {
    if (!clientToDelete.value || deletingClient.value) {
        return
    }

    deletingClient.value = true

    try {
        const token = sessionStorage.getItem('motoflow_access_token')

        if (!token) {
            alert.value = {
                visible: true,
                variant: AlertVariant.ERROR,
                message: getApiMessage('USER.UNAUTHENTICATED'),
            }

            return
        }

        await $api.delete(`/client/${clientToDelete.value.id}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        })

        confirmationOpen.value = false

        alert.value = {
            visible: true,
            variant: AlertVariant.SUCCESS,
            message: 'Cliente excluído com sucesso.',
        }

        const page = clients.value.length === 1
            && pagination.value.current_page > 1
            ? pagination.value.current_page - 1
            : pagination.value.current_page

        clientToDelete.value = null

        await fetchClients(page)
    } catch (error) {
        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(error.response?.data?.message),
        }
    } finally {
        deletingClient.value = false
    }
}
</script>
