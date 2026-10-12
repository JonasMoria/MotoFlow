const ApiMessages = Object.freeze({
    // Login
    'FORM.LOGIN.EMAIL.REQUIRED': 'Informe seu e-mail.',
    'FORM.LOGIN.EMAIL.INVALID': 'Informe um endereço de e-mail válido.',
    'FORM.LOGIN.PASSWORD.REQUIRED': 'Informe sua senha.',
    'FORM.LOGIN.PASSWORD.INVALID': 'A senha informada é inválida.',
    'USER.LOGIN.INVALID_CREDENTIALS': 'E-mail ou senha incorretos.',

    // Verificação em duas etapas
    'FORM.LOGIN.CODE.REQUIRED': 'Informe o código de verificação.',
    'FORM.LOGIN.CODE.INVALID': 'O código de verificação deve conter apenas texto válido.',
    'FORM.LOGIN.CODE.SIZE': 'O código de verificação deve conter exatamente seis dígitos.',
    'USER.LOGIN.CODE.INVALID': 'Código de verificação inválido.',

    // Clientes
    'FORM.CLIENT.NAME.REQUIRED': 'Informe o nome do cliente.',
    'FORM.CLIENT.NAME.STRING': 'O nome do cliente deve ser um texto válido.',
    'FORM.CLIENT.NAME.MAX': 'O nome do cliente deve ter no máximo 255 caracteres.',
    'FORM.CLIENT.PHONE.REQUIRED': 'Informe o telefone do cliente.',
    'FORM.CLIENT.PHONE.STRING': 'O telefone do cliente deve ser um texto válido.',
    'FORM.CLIENT.EMAIL.INVALID': 'Informe um endereço de e-mail válido.',
    'FORM.CLIENT.EMAIL.MAX': 'O e-mail deve ter no máximo 255 caracteres.',
    'FORM.CLIENT.DOCUMENT.STRING': 'O documento do cliente deve ser um texto válido.',
    'FORM.CLIENT.DOCUMENT.MAX': 'O documento deve ter no máximo 20 caracteres.',
    'FORM.CLIENT.ADDRESS.STRING': 'O endereço do cliente deve ser um texto válido.',
    'FORM.CLIENT.AVATAR.FILE': 'O avatar deve ser um arquivo válido.',
    'FORM.CLIENT.AVATAR.IMAGE': 'O avatar deve ser uma imagem.',
    'FORM.CLIENT.AVATAR.MIMES': 'O avatar deve estar no formato JPG, JPEG, PNG ou WEBP.',
    'FORM.CLIENT.AVATAR.MAX': 'O avatar deve ter no máximo 5 MB.',

    // Autenticação e erros de negócio
    'USER.UNAUTHENTICATED': 'Sua sessão expirou. Faça login novamente.',
    'CLIENT.NOT_FOUND': 'Cliente não encontrado.',
})

export function getApiMessage(flag) {
    return ApiMessages[flag] ?? 'Não foi possível concluir a operação. Tente novamente.'
}
