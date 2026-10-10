<template>
    <form class="flex w-full flex-col gap-6" @submit.prevent="handleSubmit">
        <span>
            <img src="@/assets/images/motoflow-logo.png" alt="Logo da MotoFlow" class="w-42">
        </span>
        <div>
            <h2 class="text-[1.65rem] font-bold tracking-tight text-text">
                Acesse sua conta
            </h2>

            <p class="mt-2 text-sm text-text-muted">
                Entre para gerenciar sua oficina.
            </p>
        </div>

        <Alert v-if="errorMessage" :variant="AlertVariant.ERROR" title="Não foi possível entrar" role="alert">
            {{ errorMessage }}
        </Alert>

        <div class="flex flex-col gap-2">
            <label for="email" class="text-sm font-semibold text-text">
                E-mail
            </label>

            <div
                class="flex min-h-13 items-center gap-3 rounded-lg border border-border bg-surface px-4 transition-colors focus-within:border-brand focus-within:ring-4 focus-within:ring-brand/10">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    class="size-5 shrink-0 text-text-muted" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="3" />
                    <path d="m4 7 8 6 8-6" />
                </svg>

                <input id="email" v-model="email" type="email" name="email" placeholder="voce@empresa.com.br"
                    autocomplete="email" required :disabled="loading"
                    class="min-w-0 flex-1 bg-transparent text-sm text-text outline-none placeholder:text-slate-400 disabled:opacity-60" />
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <label for="password" class="text-sm font-semibold text-text">
                Senha
            </label>

            <div
                class="flex min-h-13 items-center gap-3 rounded-lg border border-border bg-surface px-4 transition-colors focus-within:border-brand focus-within:ring-4 focus-within:ring-brand/10">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    class="size-5 shrink-0 text-text-muted" aria-hidden="true">
                    <rect x="4" y="10" width="16" height="11" rx="2" />
                    <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                </svg>

                <input id="password" v-model="password" :type="showPassword ? 'text' : 'password'" name="password"
                    placeholder="Digite sua senha" autocomplete="current-password" required :disabled="loading"
                    class="min-w-0 flex-1 bg-transparent text-sm text-text outline-none placeholder:text-slate-400 disabled:opacity-60" />

                <button type="button" :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
                    :aria-pressed="showPassword"
                    class="grid size-8 shrink-0 place-items-center rounded-md text-text-muted transition-colors hover:bg-background hover:text-text focus-visible:outline-2 focus-visible:outline-brand"
                    @click="showPassword = !showPassword">
                    <svg v-if="!showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        class="size-5" aria-hidden="true">
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>

                    <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5"
                        aria-hidden="true">
                        <path d="m3 3 18 18" />
                        <path
                            d="M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A10.7 10.7 0 0 1 12 5c6.5 0 10 7 10 7s-3.5 7-10 7a10 10 0 0 1-4-.8M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7" />
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit" :disabled="loading"
            class="flex min-h-13 cursor-pointer items-center justify-center gap-3 rounded-lg bg-brand px-5 text-sm font-semibold text-white transition-colors hover:bg-brand-hover focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:cursor-wait disabled:opacity-70">
            <span v-if="loading"
                class="size-5 animate-spin rounded-full border-2 border-white/35 border-t-white motion-reduce:animate-none"
                aria-hidden="true" />

            {{ loading ? 'Entrando...' : 'Entrar na plataforma' }}

            <svg v-if="!loading" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5"
                aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6" />
            </svg>
        </button>
    </form>
</template>

<script setup>

import { ref } from 'vue'
import { AlertVariant } from '~/features/ui/types/AlertVariant'
import { HttpStatus } from '~/features/shared/types/HttpStatus'
import { getApiMessage } from '~/features/shared/messages/ApiMessages'
import Alert from '~/components/ui/Alert.vue'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const loading = ref(false)
const errorMessage = ref('')

async function handleSubmit() {
    errorMessage.value = ''

    if (!email.value.trim() || !password.value) {
        errorMessage.value = !email.value.trim()
            ? getApiMessage('FORM.LOGIN.EMAIL.REQUIRED')
            : getApiMessage('FORM.LOGIN.PASSWORD.REQUIRED')

        return
    }

    loading.value = true

    try {
        const { $api } = useNuxtApp()

        await $api.post('/login', {
            email: email.value.trim(),
            password: password.value,
        })

        await navigateTo('/two-factor')
    } catch (error) {
        const status = error.response?.status
        const flag = error.response?.data?.message

        if (flag) {
            errorMessage.value = getApiMessage(flag)
        } else if (status === HttpStatus.UNAUTHORIZED) {
            errorMessage.value = getApiMessage(
                'USER.LOGIN.INVALID_CREDENTIALS',
            )
        } else if (status === HttpStatus.UNPROCESSABLE_ENTITY) {
            errorMessage.value = 'Verifique os dados informados.'
        } else if (!error.response) {
            errorMessage.value =
                'Não foi possível conectar ao servidor. Tente novamente.'
        } else {
            errorMessage.value =
                'Não foi possível realizar o login. Tente novamente.'
        }
    } finally {
        loading.value = false
    }
}
</script>
