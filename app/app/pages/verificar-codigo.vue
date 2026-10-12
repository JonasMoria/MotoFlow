
<template>
    <main class="flex min-h-screen items-center justify-center bg-background px-4 py-8 sm:px-6">
        <section class="w-full max-w-md rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-8">
            <div class="mb-8 flex flex-col items-center text-center">
                <div class="mb-5 grid size-16 place-items-center rounded-2xl bg-blue-50 text-brand">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        class="size-8"
                        aria-hidden="true"
                    >
                        <rect x="3" y="5" width="18" height="14" rx="3" />
                        <path d="m4 7 8 6 8-6" />
                        <path d="m15 16 2 2 4-4" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-text sm:text-3xl">
                    Verifique seu e-mail
                </h1>

                <p class="mt-3 text-sm leading-6 text-text-muted">
                    Enviamos um código de segurança para o endereço informado.
                    Digite o código de seis dígitos para continuar.
                </p>

                <p v-if="email" class="mt-2 break-all text-sm font-semibold text-brand">
                    {{ email }}
                </p>
            </div>

            <Alert
                v-if="errorMessage"
                :variant="AlertVariant.ERROR"
                title="Não foi possível verificar o código"
            >
                {{ errorMessage }}
            </Alert>

            <form class="mt-6 flex flex-col gap-5" @submit.prevent="handleSubmit">
                <div class="flex flex-col gap-2">
                    <label for="code" class="text-sm font-semibold text-text">
                        Código de verificação
                    </label>

                    <input
                        id="code"
                        v-model="code"
                        type="text"
                        name="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        pattern="[0-9]{6}"
                        minlength="6"
                        maxlength="6"
                        placeholder="000000"
                        required
                        :disabled="loading"
                        aria-describedby="code-help"
                        class="min-h-14 w-full rounded-lg border border-border bg-surface px-4 text-center text-2xl font-bold tracking-[0.5em] text-text outline-none transition-colors placeholder:text-slate-300 focus:border-brand focus:ring-4 focus:ring-brand/10 disabled:opacity-60"
                        @input="handleCodeInput"
                    />

                    <p id="code-help" class="text-xs leading-5 text-text-muted">
                        Informe os seis dígitos recebidos por e-mail.
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="loading || code.length !== 6 || !email"
                    class="flex min-h-13 items-center justify-center gap-3 rounded-lg bg-brand px-5 text-sm font-semibold text-white transition-colors hover:bg-brand-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span
                        v-if="loading"
                        class="size-5 animate-spin rounded-full border-2 border-white/35 border-t-white motion-reduce:animate-none"
                        aria-hidden="true"
                    />

                    {{ loading ? 'Verificando...' : 'Verificar código' }}

                    <svg
                        v-if="!loading"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="size-5"
                        aria-hidden="true"
                    >
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </button>
            </form>

            <div class="mt-6 border-t border-border pt-5 text-center">
                <button
                    type="button"
                    class="text-sm font-semibold text-brand transition-colors hover:text-brand-hover focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand"
                    @click="backToLogin"
                >
                    Voltar para o login
                </button>
            </div>

            <p class="mt-6 text-center text-xs leading-5 text-text-muted">
                <span class="inline-flex items-center justify-center gap-2">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="size-4"
                        aria-hidden="true"
                    >
                        <rect x="4" y="10" width="16" height="11" rx="2" />
                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    </svg>
                    Sua segurança é prioridade.
                </span>
            </p>
        </section>
    </main>
</template>

<script>
import Alert from '~/components/ui/Alert.vue'
import { AlertVariant } from '~/features/ui/types/AlertVariant'
import { HttpStatus } from '~/features/shared/types/HttpStatus'
import { getApiMessage } from '~/features/shared/messages/ApiMessages'

export default {
    name: 'TwoFactorPage',

    components: {
        Alert,
    },

    data() {
        return {
            AlertVariant,
            email: '',
            code: '',
            loading: false,
            errorMessage: '',
        }
    },

    head() {
        return {
            title: 'Verificação de segurança | MotoFlow',
        }
    },

    mounted() {
        this.email = sessionStorage.getItem('motoflow_login_email') || ''

        if (!this.email) {
            navigateTo('/login', { replace: true })
        }
    },

    methods: {
        handleCodeInput(event) {
            this.code = event.target.value.replace(/\D/g, '').slice(0, 6)
            this.errorMessage = ''
        },

        async handleSubmit() {
            this.errorMessage = ''

            if (!this.email) {
                this.errorMessage = 'Informe seu e-mail novamente.'
                return
            }

            if (!/^\d{6}$/.test(this.code)) {
                this.errorMessage = getApiMessage('FORM.LOGIN.CODE.SIZE')
                return
            }

            this.loading = true

            try {
                const { $api } = useNuxtApp()

                const response = await $api.post('/login/verify', {
                    email: this.email,
                    code: this.code,
                })

                const token = response.data?.data?.token

                if (!token) {
                    this.errorMessage =
                        'A autenticação foi iniciada, mas o servidor não retornou o token de acesso.'
                    return
                }

                sessionStorage.setItem('motoflow_access_token', token)
                sessionStorage.removeItem('motoflow_login_email')

                await navigateTo('/painel/servicos', { replace: true })
            } catch (error) {
                const flag = error.response?.data?.message
                const status = error.response?.status

                if (flag) {
                    this.errorMessage = getApiMessage(flag)
                } else if (status === HttpStatus.UNAUTHORIZED) {
                    this.errorMessage =
                        'O código é inválido ou expirou. Verifique o e-mail e tente novamente.'
                } else if (status === HttpStatus.UNPROCESSABLE_ENTITY) {
                    this.errorMessage =
                        'O código informado não é válido. Confira os seis dígitos.'
                } else if (!error.response) {
                    this.errorMessage =
                        'Não foi possível conectar ao servidor. Tente novamente.'
                } else {
                    this.errorMessage =
                        'Não foi possível verificar o código. Tente novamente.'
                }
            } finally {
                this.loading = false
            }
        },

        async backToLogin() {
            sessionStorage.removeItem('motoflow_login_email')
            await navigateTo('/login')
        },
    },
}
</script>
