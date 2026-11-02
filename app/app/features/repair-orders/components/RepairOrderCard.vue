<template>
    <article
        class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
        <!-- Cabeçalho -->
        <div class="p-5 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                        Ordem de serviço #{{ repairOrder.id }}
                    </p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-1 wrap-break">
                        {{ repairOrder.title }}
                    </h3>
                </div>

                <div class="flex flex-wrap gap-2 shrink-0">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="currentStatus.class">
                        {{ currentStatus.label }}
                    </span>

                    <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="currentUrgency.class">
                        {{ currentUrgency.label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="p-5 space-y-5">
            <!-- Motocicleta -->
            <section>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-500 flex items-center justify-center">
                        <img src="@/assets/icons/motorcycle.svg" alt="MotoCicleta">
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs text-gray-500">Motocicleta</p>
                        <p class="font-semibold text-gray-900">
                            {{ motorcycleName }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Placa</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">
                            {{ repairOrder.motorcycle_plate || 'Não informada' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Cor</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">
                            {{ repairOrder.motorcycle_color || 'Não informada' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 col-span-2 sm:col-span-1">
                        <p class="text-xs text-gray-500">ID da motocicleta</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">
                            #{{ repairOrder.motorcycle_id }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Cliente -->
            <section>
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center shrink-0">
                        <img v-if="repairOrder.profile_photo" :src="repairOrder.profile_photo"
                            :alt="repairOrder.client_name" class="w-full h-full object-cover" />

                        <span v-else class="text-sm font-semibold text-gray-600">
                            {{ clientInitials }}
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-500">Cliente</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">
                            {{ repairOrder.client_name || 'Cliente não informado' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ formattedPhone }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Descrição -->
            <section v-if="repairOrder.description">
                <h4 class="text-sm font-semibold text-gray-800 mb-1">
                    Descrição do serviço
                </h4>

                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                    {{ repairOrder.description }}
                </p>
            </section>

            <!-- Diagnóstico e observações -->
            <section v-if="repairOrder.diagnosis || repairOrder.observations"
                class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-if="repairOrder.diagnosis">
                    <h4 class="text-sm font-semibold text-gray-800 mb-1">
                        Diagnóstico
                    </h4>

                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                        {{ repairOrder.diagnosis }}
                    </p>
                </div>

                <div v-if="repairOrder.observations">
                    <h4 class="text-sm font-semibold text-gray-800 mb-1">
                        Observações
                    </h4>

                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                        {{ repairOrder.observations }}
                    </p>
                </div>
            </section>

            <!-- Datas -->
            <section class="border-t border-gray-100 pt-4">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">
                    Histórico
                </h4>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Cadastro</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            {{ formatDate(repairOrder.created_at) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Início</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            {{ formatDate(repairOrder.started_at) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Conclusão</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            {{ formatDate(repairOrder.finished_at) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Entrega</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">
                            {{ formatDate(repairOrder.delivered_at) }}
                        </p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Ações -->
        <footer class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            <div class="flex flex-wrap items-center justify-end gap-2">
                <button type="button" @click="$emit('download', repairOrder)"
                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                    Baixar OS
                </button>

                <button type="button" @click="$emit('send', repairOrder)"
                    class="px-3 py-2 text-sm font-medium text-blue-800 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors">
                    Enviar ao cliente
                </button>

                <button type="button" @click="$emit('edit', repairOrder)"
                    class="px-3 py-2 text-sm font-medium text-white bg-blue-800 rounded-lg hover:bg-blue-900 transition-colors">
                    Editar
                </button>

                <button type="button" @click="$emit('delete', repairOrder)"
                    class="px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-colors">
                    Excluir
                </button>
            </div>
        </footer>
    </article>
</template>

<script>
import { computed } from 'vue'

import { RepairStatus } from '~/features/ui/types/RepairStatus'
import { UrgencyLevel } from '~/features/ui/types/UrgencyLevel'

export default {
    name: 'RepairOrderCard',

    props: {
        repairOrder: {
            type: Object,
            required: true,
        },
    },

    emits: ['edit', 'delete', 'download', 'send'],

    setup(props) {
        const statusOptions = {
            [RepairStatus.CREATED]: {
                label: 'Criado',
                class: 'bg-blue-100 text-blue-800',
            },
            [RepairStatus.WAITING]: {
                label: 'Aguardando',
                class: 'bg-amber-100 text-amber-800',
            },
            [RepairStatus.IN_MAINTENANCE]: {
                label: 'Em manutenção',
                class: 'bg-orange-100 text-orange-800',
            },
            [RepairStatus.WAITING_PICKUP]: {
                label: 'Aguardando retirada',
                class: 'bg-purple-100 text-purple-800',
            },
            [RepairStatus.DONE]: {
                label: 'Concluído',
                class: 'bg-green-100 text-green-800',
            },
            [RepairStatus.CANCELED]: {
                label: 'Cancelado',
                class: 'bg-red-100 text-red-800',
            },
        }

        const urgencyOptions = {
            [UrgencyLevel.VERY_LOW]: {
                label: 'Muito baixa',
                class: 'bg-slate-100 text-slate-700',
            },
            [UrgencyLevel.LOW]: {
                label: 'Baixa',
                class: 'bg-blue-100 text-blue-800',
            },
            [UrgencyLevel.MEDIUM]: {
                label: 'Média',
                class: 'bg-amber-100 text-amber-800',
            },
            [UrgencyLevel.HIGH]: {
                label: 'Alta',
                class: 'bg-orange-100 text-orange-800',
            },
            [UrgencyLevel.VERY_HIGH]: {
                label: 'Muito alta',
                class: 'bg-red-100 text-red-800',
            },
        }

        const currentStatus = computed(() => {
            return statusOptions[props.repairOrder.status] || {
                label: 'Desconhecido',
                class: 'bg-gray-100 text-gray-700',
            }
        })

        const currentUrgency = computed(() => {
            return urgencyOptions[props.repairOrder.urgency_level] || {
                label: 'Não definida',
                class: 'bg-gray-100 text-gray-700',
            }
        })

        const motorcycleName = computed(() => {
            const { motorcycle_brand, motorcycle_model } = props.repairOrder

            return [motorcycle_brand, motorcycle_model]
                .filter(Boolean)
                .join(' ') || 'Motocicleta não informada'
        })

        const clientInitials = computed(() => {
            const name = props.repairOrder.client_name || 'Cliente'

            return name
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map(part => part.charAt(0).toUpperCase())
                .join('')
        })

        const formattedPhone = computed(() => {
            const phone = props.repairOrder.client_phone

            if (!phone) return 'Telefone não informado'

            const digits = String(phone).replace(/\D/g, '')

            // Formato brasileiro com código do país: 55 + DDD + número
            if (digits.length === 13 && digits.startsWith('55')) {
                return `+${digits.slice(0, 2)} (${digits.slice(2, 4)}) ${digits.slice(4, 9)}-${digits.slice(9)}`
            }

            if (digits.length === 12 && digits.startsWith('55')) {
                return `+${digits.slice(0, 2)} (${digits.slice(2, 4)}) ${digits.slice(4, 8)}-${digits.slice(8)}`
            }

            return phone
        })

        function formatDate(value) {
            if (!value) return '—'

            const date = new Date(value.replace(' ', 'T'))

            if (Number.isNaN(date.getTime())) return '—'

            return new Intl.DateTimeFormat('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            }).format(date)
        }

        return {
            currentStatus,
            currentUrgency,
            motorcycleName,
            clientInitials,
            formattedPhone,
            formatDate,
        }
    },
}
</script>
