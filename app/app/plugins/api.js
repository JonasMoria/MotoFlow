
import { HttpClient } from '~/services/http'

export default defineNuxtPlugin(() => {
    const config = useRuntimeConfig()

    const api = new HttpClient(config.public.apiBaseUrl)

    return {
        provide: {
            api,
        },
    }
})
