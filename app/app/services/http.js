import axios from 'axios'

export class HttpClient {
    constructor(baseURL) {
        this.client = axios.create({
            baseURL,
            headers: {
                Accept: 'application/json',
            },
        })
    }

    async get(url, config = {}) {
        const response = await this.client.get(url, config)

        return response.data
    }

    async post(url, data = {}, config = {}) {
        const response = await this.client.post(url, data, config)

        return response.data
    }

    async patch(url, data = {}, config = {}) {
        const response = await this.client.patch(url, data, config)

        return response.data
    }

    async delete(url, config = {}) {
        const response = await this.client.delete(url, config)

        return response.data
    }
}
