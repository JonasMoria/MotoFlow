<template>
    <form class="space-y-5" @submit.prevent="handleSubmit">
        <!-- Erros de validação -->
        <Alert v-if="validationMessages.length" :variant="AlertVariant.ERROR" title="Verifique os dados informados">
            <ul class="list-inside list-disc space-y-1">
                <li v-for="(message, index) in validationMessages" :key="`${index}-${message}`">
                    {{ message }}
                </li>
            </ul>
        </Alert>

        <!-- Dados principais -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="motorcycle-brand" class="mb-1.5 block text-sm font-medium text-foreground">
                    Marca <span class="text-red-600">*</span>
                </label>

                <input id="motorcycle-brand" v-model.trim="brand" v-bind="brandAttrs" type="text" maxlength="100"
                    placeholder="Ex.: Honda" :aria-invalid="Boolean(fieldErrors.brand)"
                    :class="inputClass(fieldErrors.brand)" />
            </div>

            <div>
                <label for="motorcycle-model" class="mb-1.5 block text-sm font-medium text-foreground">
                    Modelo <span class="text-red-600">*</span>
                </label>

                <input id="motorcycle-model" v-model.trim="model" v-bind="modelAttrs" type="text" maxlength="100"
                    placeholder="Ex.: CG 160 Titan" :aria-invalid="Boolean(fieldErrors.model)"
                    :class="inputClass(fieldErrors.model)" />
            </div>

            <div>
                <label for="motorcycle-plate" class="mb-1.5 block text-sm font-medium text-foreground">
                    Placa <span class="text-red-600">*</span>
                </label>

                <input id="motorcycle-plate" v-model.trim="plate" v-bind="plateAttrs" type="text" maxlength="10"
                    placeholder="Ex.: ABC1D23" :aria-invalid="Boolean(fieldErrors.plate)" class="uppercase"
                    :class="inputClass(fieldErrors.plate)" />
            </div>

            <div>
                <label for="motorcycle-year" class="mb-1.5 block text-sm font-medium text-foreground">
                    Ano
                </label>

                <input id="motorcycle-year" v-model.trim="year" v-bind="yearAttrs" type="number" min="1900" max="9999"
                    placeholder="Ex.: 2024" :aria-invalid="Boolean(fieldErrors.year)"
                    :class="inputClass(fieldErrors.year)" />
            </div>
        </div>

        <!-- Identificação -->
        <div class="border-t border-slate-200 pt-5">
            <h3 class="mb-4 text-sm font-semibold text-foreground">
                Identificação da motocicleta
            </h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="motorcycle-renavam" class="mb-1.5 block text-sm font-medium text-foreground">
                        Renavam
                    </label>

                    <input id="motorcycle-renavam" v-model.trim="renavam" v-bind="renavamAttrs" type="text"
                        maxlength="11" placeholder="Número do Renavam" :aria-invalid="Boolean(fieldErrors.renavam)"
                        :class="inputClass(fieldErrors.renavam)" />
                </div>

                <div>
                    <label for="motorcycle-chassis" class="mb-1.5 block text-sm font-medium text-foreground">
                        Chassi
                    </label>

                    <input id="motorcycle-chassis" v-model.trim="chassis" v-bind="chassisAttrs" type="text"
                        maxlength="17" placeholder="Número do chassi" :aria-invalid="Boolean(fieldErrors.chassis)"
                        :class="inputClass(fieldErrors.chassis)" />
                </div>
            </div>
        </div>

        <!-- Informações adicionais -->
        <div class="border-t border-slate-200 pt-5">
            <h3 class="mb-4 text-sm font-semibold text-foreground">
                Informações adicionais
            </h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="motorcycle-color" class="mb-1.5 block text-sm font-medium text-foreground">
                        Cor
                    </label>

                    <input id="motorcycle-color" v-model.trim="color" v-bind="colorAttrs" type="text" maxlength="50"
                        placeholder="Ex.: Vermelha" :aria-invalid="Boolean(fieldErrors.color)"
                        :class="inputClass(fieldErrors.color)" />
                </div>

                <div>
                    <label for="motorcycle-engine" class="mb-1.5 block text-sm font-medium text-foreground">
                        Número do motor
                    </label>

                    <input id="motorcycle-engine" v-model.trim="engineNumber" v-bind="engineNumberAttrs" type="text"
                        maxlength="50" placeholder="Número do motor" :aria-invalid="Boolean(fieldErrors.engine_number)"
                        :class="inputClass(fieldErrors.engine_number)" />
                </div>
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
                        : (isEditing ? 'Salvar alterações' : 'Cadastrar moto')
                }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useForm } from 'vee-validate'
import * as yup from 'yup'

import Alert from '~/components/ui/Alert.vue'
import { AlertVariant } from '~/features/ui/types/AlertVariant'

const props = defineProps({
    motorcycle: {
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

const isEditing = computed(() => Boolean(props.motorcycle?.id))

const validationSchema = yup.object({
    brand: yup
        .string()
        .required('Informe a marca da moto.')
        .max(100, 'A marca deve ter no máximo 100 caracteres.'),

    model: yup
        .string()
        .required('Informe o modelo da moto.')
        .max(100, 'O modelo deve ter no máximo 100 caracteres.'),

    plate: yup
        .string()
        .required('Informe a placa da moto.')
        .max(10, 'A placa deve ter no máximo 10 caracteres.'),

    renavam: yup
        .string()
        .max(11, 'O Renavam deve ter no máximo 11 caracteres.'),

    chassis: yup
        .string()
        .max(17, 'O chassi deve ter no máximo 17 caracteres.'),

    year: yup
        .string()
        .test(
            'year',
            'Informe um ano válido com 4 dígitos, a partir de 1900.',
            (value) => {
                if (!value) return true

                return /^\d{4}$/.test(value)
                    && Number(value) >= 1900
            }
        ),

    color: yup
        .string()
        .max(50, 'A cor deve ter no máximo 50 caracteres.'),

    engine_number: yup
        .string()
        .max(50, 'O número do motor deve ter no máximo 50 caracteres.'),
})

const {
    defineField,
    errors: fieldErrors,
    handleSubmit: validateAndSubmit,
    resetForm,
} = useForm({
    validationSchema,
    initialValues: createForm(props.motorcycle),
})

const [brand, brandAttrs] = defineField('brand')
const [model, modelAttrs] = defineField('model')
const [plate, plateAttrs] = defineField('plate')
const [renavam, renavamAttrs] = defineField('renavam')
const [chassis, chassisAttrs] = defineField('chassis')
const [year, yearAttrs] = defineField('year')
const [color, colorAttrs] = defineField('color')
const [engineNumber, engineNumberAttrs] = defineField('engine_number')

function createForm(motorcycle) {
    return {
        brand: motorcycle?.brand ?? '',
        model: motorcycle?.model ?? '',
        plate: motorcycle?.plate ?? '',
        renavam: motorcycle?.renavam ?? '',
        chassis: motorcycle?.chassis ?? '',
        year: motorcycle?.year ? String(motorcycle.year) : '',
        color: motorcycle?.color ?? '',
        engine_number: motorcycle?.engine_number ?? '',
    }
}

const validationMessages = computed(() => {
    const messages = [
        ...Object.values(fieldErrors.value),
        ...Object.values(props.errors),
    ]

    return [
        ...new Set(
            messages.filter(
                (message) =>
                    typeof message === 'string' && message.length > 0
            )
        ),
    ]
})

function inputClass(error) {
    return [
        'w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-foreground outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-2 focus:ring-brand/15',
        error
            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/15'
            : '',
    ]
}

watch(
    () => props.motorcycle,
    (motorcycle) => {
        resetForm({
            values: createForm(motorcycle),
        })
    },
    { immediate: true }
)

const handleSubmit = validateAndSubmit((values) => {
    if (props.loading) return

    const motorcycle = {
        brand: values.brand.trim(),
        model: values.model.trim(),
        plate: values.plate.trim(),
        renavam: values.renavam?.trim() || null,
        chassis: values.chassis?.trim() || null,
        year: values.year ? Number(values.year) : null,
        color: values.color?.trim() || null,
        engine_number: values.engine_number?.trim() || null,
    }

    emit('submit', motorcycle)
})
</script>
