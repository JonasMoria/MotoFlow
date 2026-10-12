<template>
    <section>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-sm md:text-2xl font-bold text-foreground">
                    Clientes
                </h1>
                <p class="mt-2 text-[9px] md:text-sm text-muted">
                    Gerencie os clientes cadastrados na oficina.
                </p>
            </div>

            <button type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs md:text-sm font-semibold text-white transition hover:bg-brand focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2"
                @click="openModal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                </svg>

                Cadastrar Cliente
            </button>
        </div>

        <Alert v-if="alert.visible" :variant="alert.variant" :message="alert.message" @close="alert.visible = false" />

        <Modal v-model="modalOpen" title="Cadastrar Cliente"
            description="Preencha os dados para cadastrar um novo cliente." size="lg" @close="handleModalClose">
            <ClientForm :loading="saving" :errors="formErrors" @submit="handleSubmit" @cancel="closeModal" />
        </Modal>
    </section>
</template>

<script setup>
import { ref } from 'vue'
import Alert from '~/components/ui/Alert.vue'
import Modal from '~/components/ui/Modal.vue'
import ClientForm from '~/features/clients/components/ClientForm.vue'
import { getApiMessage } from '~/features/shared/messages/ApiMessages'
import { HttpStatus } from '~/features/shared/types/HttpStatus'
import { AlertVariant } from '~/features/ui/types/AlertVariant'

const { $api } = useNuxtApp()

const alert = ref({
    visible: false,
    variant: AlertVariant.ERROR,
    message: '',
});

definePageMeta({
    layout: 'dashboard',
})

useHead({
    title: 'Meus Clientes | MotoFlow',
})

const modalOpen = ref(false)
const saving = ref(false)
const formErrors = ref({})

function openModal() {
    formErrors.value = {}
    modalOpen.value = true
}

function closeModal() {
    if (saving.value) {
        return
    }

    modalOpen.value = false
    formErrors.value = {}
}

function handleModalClose() {
    formErrors.value = {}
}

async function handleSubmit(client) {
    alert.value.visible = false
    saving.value = true
    formErrors.value = {}

    try {
        const token = sessionStorage.getItem('motoflow_access_token');
        const response = await $api.post('/client', client.toFormData(), {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        if (response.status === HttpStatus.CREATED) {
            alert.value = {
                visible: true,
                variant: AlertVariant.SUCCESS,
                message: 'Cliente cadastrado com sucesso.',
            }

            modalOpen.value = false
            return
        }

        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(response.data?.message),
        }
    } catch (error) {
        const responseData = error.response?.data
        const messageFlag = responseData?.message

        alert.value = {
            visible: true,
            variant: AlertVariant.ERROR,
            message: getApiMessage(messageFlag),
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
</script>