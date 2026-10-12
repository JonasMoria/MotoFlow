
export class Client {
    constructor({
        id = null,
        name = '',
        phone = '',
        email = '',
        document = '',
        address = '',
        avatar = null,
        avatar_path = null,
        created_at = null,
        updated_at = null,
    } = {}) {
        this.id = id
        this.name = name
        this.phone = phone
        this.email = email
        this.document = document
        this.address = address
        this.avatar = avatar
        this.avatar_path = avatar_path
        this.created_at = created_at
        this.updated_at = updated_at
    }

    static fromObject(data = {}) {
        return new Client({
            id: data.id ?? null,
            name: data.name ?? '',
            phone: data.phone ?? '',
            email: data.email ?? '',
            document: data.document ?? '',
            address: data.address ?? '',
            avatar: data.avatar ?? null,
            avatar_path: data.avatar_path ?? null,
            created_at: data.created_at ?? null,
            updated_at: data.updated_at ?? null,
        })
    }

    toFormData() {
        const formData = new FormData()

        formData.append('name', this.name)
        formData.append('phone', this.phone)

        if (this.email) {
            formData.append('email', this.email)
        }

        if (this.document) {
            formData.append('document', this.document)
        }

        if (this.address) {
            formData.append('address', this.address)
        }

        if (this.avatar instanceof File) {
            formData.append('avatar', this.avatar)
        }

        return formData
    }

    toJSON() {
        return {
            id: this.id,
            name: this.name,
            phone: this.phone,
            email: this.email,
            document: this.document,
            address: this.address,
            avatar_path: this.avatar_path,
            created_at: this.created_at,
            updated_at: this.updated_at,
        }
    }
}
