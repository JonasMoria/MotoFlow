<template>
    <Modal :model-value="modelValue" :title="title" size="sm" @update:model-value="handleVisibilityChange"
        @close="handleCancel">
        <div class="space-y-5">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600"
                    aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.3 3.86 2.82 17a2 2 0 0 0 1.74 3h14.88a2 2 0 0 0 1.74-3L13.72 3.86a2 2 0 0 0-3.42 0Z" />
                    </svg>
                </div>

                <p class="pt-1 text-sm leading-6 text-slate-600">
                    {{ message }}
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
                <button type="button" :disabled="loading"
                    class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                    @click="handleCancel">
                    {{ cancelText }}
                </button>

                <button type="button" :disabled="loading"
                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                    @click="handleConfirm">
                    <svg v-if="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z" />
                    </svg>

                    {{ loading ? loadingText : confirmText }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '~/components/ui/Modal.vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirmar ação',
    },
    message: {
        type: String,
        required: true,
    },
    confirmText: {
        type: String,
        default: 'Confirmar',
    },
    cancelText: {
        type: String,
        default: 'Cancelar',
    },
    loadingText: {
        type: String,
        default: 'Aguarde...',
    },
    loading: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

function handleVisibilityChange(visible) {
    emit('update:modelValue', visible)

    if (!visible) {
        emit('cancel')
    }
}

function handleCancel() {
    if (props.loading) {
        return
    }

    emit('update:modelValue', false)
    emit('cancel')
}

function handleConfirm() {
    if (props.loading) {
        return
    }

    emit('confirm')
}
</script>
