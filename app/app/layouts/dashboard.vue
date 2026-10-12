<template>
    <div class="min-h-screen bg-background text-foreground">
        <div class="min-h-screen md:flex">
            <!-- Menu lateral desktop -->
            <aside class="hidden w-64 shrink-0 flex-col bg-sidebar text-white md:fixed md:inset-y-0 md:left-0 md:flex">
                <nav class="flex-1 space-y-2 px-2 py-6">
                    <NuxtLink v-for="item in menuItems" :key="item.label" :to="item.to"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors"
                        :class="isActive(item.to)
                            ? 'bg-brand text-white'
                            : 'text-gray-300 hover:bg-white/10 hover:text-white'">
                        <img src="@/assets/icons/services.svg" v-if="item.icon === 'services'"
                            class="h-6 w-6 shrink-0" />
                        <img src="@/assets/icons/clients.svg" v-else-if="item.icon === 'clients'"
                            class="h-6 w-6 shrink-0" />
                        <img src="@/assets/icons/motorcycle.svg" v-else class="h-6 w-6 shrink-0" />

                        <span>{{ item.label }}</span>
                    </NuxtLink>
                </nav>
            </aside>

            <!-- Área principal -->
            <div class="flex min-h-screen min-w-0 flex-1 flex-col pb-20 md:ml-64 md:pb-0">
                <!-- Cabeçalho -->
                <header
                    class="sticky top-0 z-30 flex h-16 shrink-0 items-center justify-between border-b border-border bg-surface px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3 md:hidden">
                        <img src="@/assets/images/motoflow-logo.png" alt="MotoFlow Logo" width="100">
                    </div>

                    <div class="hidden md:block">
                        <img src="@/assets/images/motoflow-logo.png" alt="MotoFlow Logo" width="100">
                    </div>

                    <!-- Menu do perfil -->
                    <div ref="profileMenuRef" class="relative ml-auto">
                        <button type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-border bg-background transition-colors hover:bg-gray-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring"
                            aria-label="Abrir menu da conta" aria-haspopup="menu" :aria-expanded="profileMenuOpen"
                            @click="profileMenuOpen = !profileMenuOpen">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M5 21v-2a7 7 0 0 1 14 0v2" />
                            </svg>
                        </button>

                        <div v-if="profileMenuOpen"
                            class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-border bg-surface py-2 shadow-lg"
                            role="menu">
                            <NuxtLink to="/painel/meu-perfil" role="menuitem"
                                class="block px-4 py-3 text-sm transition-colors hover:bg-background"
                                @click="profileMenuOpen = false">
                                Meu Perfil
                            </NuxtLink>

                            <div class="my-1 border-t border-border" />

                            <button type="button" role="menuitem"
                                class="block w-full px-4 py-3 text-left text-sm text-danger transition-colors hover:bg-red-50"
                                @click="logout">
                                Sair
                            </button>
                        </div>
                    </div>
                </header>

                <!-- Conteúdo da página -->
                <main class="min-w-0 flex-1 p-4 sm:p-6 lg:p-8">
                    <slot />
                </main>
            </div>
        </div>

        <!-- Menu inferior mobile -->
        <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-white/10 bg-sidebar px-2 pb-[env(safe-area-inset-bottom)] pt-2 md:hidden"
            aria-label="Navegação principal">
            <div class="mx-auto flex max-w-lg items-stretch justify-around">
                <NuxtLink v-for="item in menuItems" :key="item.label" :to="item.to"
                    class="flex min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-lg px-1 py-2 text-xs transition-colors"
                    :class="isActive(item.to)
                        ? 'text-white'
                        : 'text-gray-400 hover:text-white'">
                    <img src="@/assets/icons/services.svg" v-if="item.icon === 'services'" class="h-6 w-6 shrink-0" />
                    <img src="@/assets/icons/clients.svg" v-else-if="item.icon === 'clients'"
                        class="h-6 w-6 shrink-0" />
                    <img src="@/assets/icons/motorcycle.svg" v-else class="h-6 w-6 shrink-0" />

                    <span class="max-w-full truncate">{{ item.label }}</span>
                </NuxtLink>
            </div>
        </nav>
    </div>
</template>


<script>
export default {
    name: 'DashboardLayout',

    data() {
        return {
            profileMenuOpen: false,
            profileMenuRef: null,

            menuItems: [
                {
                    label: 'Serviços',
                    to: '/painel/servicos',
                    icon: 'services',
                },
                {
                    label: 'Clientes',
                    to: '/painel/clientes',
                    icon: 'clients',
                },
                {
                    label: 'Motos',
                    to: '/painel/motos',
                    icon: 'motorcycles',
                },
            ],
        }
    },

    mounted() {
        document.addEventListener('click', this.handleClickOutside)
    },

    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside)
    },

    methods: {
        isActive(path) {
            return this.$route.path === path
                || this.$route.path.startsWith(`${path}/`)
        },

        handleClickOutside(event) {
            const profileMenu = this.$refs.profileMenuRef

            if (
                profileMenu
                && !profileMenu.contains(event.target)
            ) {
                this.profileMenuOpen = false
            }
        },

        async logout() {
            this.profileMenuOpen = false

            try {
                const { $api } = useNuxtApp();

                await $api.post('/logout-all');
            } catch (error) {
                console.error('Erro ao encerrar as sessões:', error)
            } finally {
                if (import.meta.client) {
                    sessionStorage.removeItem('email');
                    sessionStorage.removeItem('motoflow_access_token');
                }

                await navigateTo('/login')
            }
        },
    },
}
</script>
