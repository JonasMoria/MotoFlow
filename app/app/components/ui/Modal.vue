<template>
    <Teleport to="body">
        <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="modelValue"
                class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/50 p-3 sm:items-center sm:p-4"
                @click.self="handleClose">
                <section ref="modalContent" role="dialog" aria-modal="true" :aria-labelledby="titleId"
                    class="flex max-h-[95dvh] w-full flex-col overflow-hidden rounded-t-2xl bg-white shadow-xl sm:rounded-2xl"
                    :class="sizeClass" @keydown.esc.stop="handleClose">
                    <!-- Cabeçalho -->
                    <header
                        class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                        <div class="min-w-0">
                            <h2 :id="titleId" class="text-lg font-semibold text-foreground">
                                {{ title }}
                            </h2>

                            <p v-if="description" class="mt-1 text-sm text-muted">
                                {{ description }}
                            </p>
                        </div>

                        <button type="button" aria-label="Fechar modal" :disabled="preventClose"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand disabled:cursor-not-allowed disabled:opacity-50"
                            @click="handleClose">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m18 6-12 12M6 6l12 12" />
                            </svg>
                        </button>
                    </header>

                    <!-- Conteúdo -->
                    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">
                        <slot />
                    </div>

                    <!-- Rodapé opcional -->
                    <footer v-if="$slots.footer" class="shrink-0 border-t border-slate-200 px-5 py-4 sm:px-6">
                        <slot name="footer" />
                    </footer>
                </section>
            </div>
        </Transition>
    </Teleport>
</template>

<script>
export default {
    name: 'Modal',

    props: {
        modelValue: {
            type: Boolean,
            default: false,
        },

        title: {
            type: String,
            required: true,
        },

        description: {
            type: String,
            default: '',
        },

        size: {
            type: String,
            default: 'lg',
            validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value),
        },

        preventClose: {
            type: Boolean,
            default: false,
        },
    },

    emits: ['update:modelValue', 'close'],

    computed: {
        titleId() {
            return `${this.$.uid}-modal-title`
        },

        sizeClass() {
            const sizes = {
                sm: 'sm:max-w-md',
                md: 'sm:max-w-lg',
                lg: 'sm:max-w-2xl',
                xl: 'sm:max-w-4xl',
                full: 'sm:max-w-6xl',
            }

            return sizes[this.size]
        },
    },

    watch: {
        modelValue(isOpen) {
            if (isOpen) {
                this.$nextTick(() => {
                    this.$refs.modalContent
                        ?.querySelector('button')
                        ?.focus()
                })
            }
        },
    },

    methods: {
        handleClose() {
            if (this.preventClose) {
                return
            }

            this.$emit('update:modelValue', false)
            this.$emit('close')
        },
    },
}
</script>
