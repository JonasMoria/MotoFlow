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

        return response;
    }

    async post(url, data = {}, config = {}) {
        const response = await this.client.post(url, data, config)

        return response;
    }

    async patch(url, data = {}, config = {}) {
        const response = await this.client.patch(url, data, config)

        return response;
    }

    async delete(url, config = {}) {
        const response = await this.client.delete(url, config)

        return response;
    }
}
