# BUGFIX — #BRD-2026-012

**Data da correção:** 23/05/2026  
**Corrigido por:** Bernardo Marques Mariné

---

## O que era o bug

O bug ocorria no arquivo `ContatoController.php`.
A query SQL em `ContatoController::index` apenas selecionava os contatos
e os ordenava em ordem crescente. Além de não possuir uma cláusula de
filtragem, ignorava completamente o `{id}` da transportadora que a URL recebia.

---

## Resposta para a Camila

Oi Camila! O problema já foi identificado e resolvido. A causa era
exatamente o que você tinha reportado. A listagem de contatos não estava
filtrando. Apenas retornando todos juntos, independente de qual
transportadora você queria acessar.

Agora a filtragem vai funcionar perfeitamente. Já pode testar.

Tenha um ótimo dia!

---

## Como reproduzir (antes da correção)

1. Fazer `GET /transportadoras/1/contatos`
2. Observar que a resposta retornava contatos de outras transportadoras
   junto com os da transportadora 1

---

## Como verificar que está corrigido

1. Fazer `GET /transportadoras/1/contatos` — retorna apenas os contatos
   vinculados à transportadora de id 1
2. Fazer `GET /transportadoras/2/contatos` — retorna apenas os contatos
   vinculados à transportadora de id 2
3. As listas são diferentes entre si