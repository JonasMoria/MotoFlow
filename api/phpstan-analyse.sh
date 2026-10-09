#!/usr/bin/env bash

set -uo pipefail

ROOT_DIR="$(git rev-parse --show-toplevel)"
cd "$ROOT_DIR"

RED='\033[0;31m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\033[0;33m'
RESET='\033[0m'

mapfile -t FILES < <(
    git diff --cached --name-only --diff-filter=ACMR -- '*.php' |
    while IFS= read -r FILE; do
        [[ -f "$FILE" ]] && printf '%s\n' "$FILE"
    done
)

if [ "${#FILES[@]}" -eq 0 ]; then
    echo -e "${YELLOW}Nenhum arquivo PHP em stage para analisar.${RESET}"
    exit 0
fi

CONTAINER_FILES=()

for FILE in "${FILES[@]}"; do
    if [[ "$FILE" == api/* ]]; then
        CONTAINER_FILES+=("/var/www/${FILE#api/}")
    fi
done

if [ "${#CONTAINER_FILES[@]}" -eq 0 ]; then
    echo -e "${YELLOW}Nenhum arquivo PHP da API em stage.${RESET}"
    exit 0
fi

echo -e "\n${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
echo -e "${CYAN} PHPStan | Análise de arquivos em stage${RESET}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}\n"

printf '  %s\n' "${FILES[@]}"

if docker compose exec -T api-cli ./vendor/bin/phpstan analyse --no-progress --error-format=table "${CONTAINER_FILES[@]}"; then
    echo -e "\n${GREEN}✓ Todos os arquivos passaram na análise.${RESET}"
    exit 0
else
    echo -e "\n${RED}✗ Foram encontrados erros pelo PHPStan.${RESET}"
    exit 1
fi