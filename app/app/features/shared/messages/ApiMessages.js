
const ApiMessages = Object.freeze({
    'FORM.LOGIN.EMAIL.REQUIRED': 'Informe seu e-mail.',
    'FORM.LOGIN.EMAIL.INVALID': 'Informe um endereço de e-mail válido.',
    'FORM.LOGIN.PASSWORD.REQUIRED': 'Informe sua senha.',
    'FORM.LOGIN.PASSWORD.INVALID': 'A senha informada é inválida.',
    'USER.LOGIN.INVALID_CREDENTIALS': 'E-mail ou senha incorretos.',
})

export function getApiMessage(flag) {
    return ApiMessages[flag] ?? 'Não foi possível concluir a operação. Tente novamente.'
}
