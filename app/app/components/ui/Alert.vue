<template>
    <div :role="role" class="flex w-full items-start gap-3 rounded-lg border p-3 text-sm sm:p-4"
        :class="currentStyle.container">
        <svg class="mt-0.5 size-5 shrink-0" :class="currentStyle.icon" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <template v-if="variant === 'success'">
                <circle cx="12" cy="12" r="10" />
                <path d="m8 12 3 3 5-6" />
            </template>

            <template v-else-if="variant === 'info'">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 16v-4" />
                <path d="M12 8h.01" />
            </template>

            <template v-else-if="variant === 'warning'">
                <path d="M10.3 3.9 1.8 18.2A2 2 0 0 0 3.5 21h17a2 2 0 0 0 1.7-2.8L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                <path d="M12 9v4" />
                <path d="M12 17h.01" />
            </template>

            <template v-else>
                <circle cx="12" cy="12" r="10" />
                <path d="m15 9-6 6" />
                <path d="m9 9 6 6" />
            </template>
        </svg>

        <div class="min-w-0 flex-1 wrap-break-word mt-1 flex">
            <p v-if="title" class="font-semibold">
                {{ title }}
            </p>
            <p v-if="message" class="ml-2">
                {{ message }}
            </p>
            <div v-if="$slots.default" class="leading-6" :class="{ 'mt-1': title }">
                <slot />
            </div>
        </div>

        <button v-if="dismissible" type="button"
            class="-mr-1 -mt-1 grid size-8 shrink-0 place-items-center rounded-md transition-colors hover:bg-black/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-current"
            aria-label="Fechar alerta" @click="emit('close')">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" aria-hidden="true">
                <path d="m18 6-12 12M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>


<script setup>
import { computed } from 'vue'
import { AlertVariant } from '~/features/ui/types/AlertVariant'

const props = defineProps({
    variant: {
        type: String,
        default: AlertVariant.INFO,
        validator: (value) => Object.values(AlertVariant).includes(value),
    },
    title: {
        type: String,
        default: '',
    },
    message: {
        type: String,
        default: '',
    },
    dismissible: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['close'])

const styles = {
    [AlertVariant.SUCCESS]: {
        container: 'border-green-200 bg-green-50 text-success',
        icon: 'text-success',
    },
    [AlertVariant.INFO]: {
        container: 'border-blue-200 bg-blue-50 text-info',
        icon: 'text-info',
    },
    [AlertVariant.WARNING]: {
        container: 'border-amber-200 bg-amber-50 text-warning',
        icon: 'text-warning',
    },
    [AlertVariant.ERROR]: {
        container: 'border-red-200 bg-red-50 text-danger',
        icon: 'text-danger',
    },
}

const currentStyle = computed(() => styles[props.variant])

const role = computed(() =>
    [AlertVariant.WARNING, AlertVariant.ERROR].includes(props.variant)
        ? 'alert'
        : 'status',
)
</script>
