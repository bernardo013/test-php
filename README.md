# Teste Técnico — Estágio em Desenvolvimento PHP

## Contexto

Você está iniciando seu estágio em uma empresa de **TMS (Transportation Management System)** — um sistema que gerencia o transporte de cargas.

No seu primeiro dia, o time te passou duas tarefas: um bug reportado pelo time de operações e um novo endpoint para implementar.

## Como rodar


### Pré-requisitos

- PHP 8.1+
- MySQL 8+
- Composer

### Passo a passo

```bash
# 1. Clone o repositório
git clone https://github.com/bernardo013/test-php.git
cd test-php

# 2. Instale as dependências
composer install

# 3. Configure o ambiente
cp .env.example .env
# Edite o .env com as credenciais do seu banco MySQL

# 4. Crie as tabelas
vendor\bin\phinx migrate

# 5. Popule os dados iniciais
vendor\bin\phinx seed:run

# 6. Suba o servidor
php -S localhost:8000 public/index.php
```

## Exemplo das requisições
 Todas as requisições estão documentadas no link a baixo 

<img width="332" height="438" alt="image" src="https://github.com/user-attachments/assets/1ad016b7-d10d-4d89-b67f-ac214f4b1f68" />


Documentação da API -> [![Postman](https://img.shields.io/badge/Postman-Collection-orange)](https://www.postman.com/bernardo013-9220741/test-php/collection/djp2meg/test-php-req)


## Decisões Técnicas 
### Tarefa 1 
  - Minha primeira opção depois de entender o código e achar o problema no contatoController::index foi colocar a cláusula de filtragem where e passar o id como parâmetro na query. 
  - Depois de analisar melhor o que cada função fazia eu percebi que utilizar a função query() da classe PDO não fazia sentido por questões de vulnerabiliade. Lendo mais a fundo na documentação sobre as funções do [PDO](https://www.php.net/manual/en/class.pdo.php) . 
  - Percebi que não era só a melhor opção por padrão de código (todas as funções do contatoController utilizavam o prepare() com execute()), mas também por segurança. 
  - O prepare() entrega primeiro a query, depois o banco retorna os dados( ou mais específicamente um objeto PDOStatement) e o execute() envia os dados separadamente. Isso impede um ataque tipo o SQLInjection. Diferentemente da função query() que entrega "tudo junto".

### Tarefa 2 
 - Quando criei o endpoint de POST decidi manter a nomenclatura da função( e de todas daqui pra frente) seguindo o padrão da REST API, como o projeto já utilizava.
 - Iniciei a função com a validação padrão para ver se a transportadora existia.
 - Até então não havia percebido que as funções body() e json() eram funções helper, entendendo elas fiz as validações de uma forma muito mais organizada do que utilizando if/elseif e else.
 - Todas as validações estão feita antes da query pra garantir que o que vier do body esteja correto.
 - Para validar se o email possuia "@" a forma que encontrei seria utilizando o [FILTER_VALIDATE_EMAIL](https://www.php.net/manual/en/filter.constants.php#constant.filter-validate-email). Que faz uma validação robusta pelo o que eu entendi, englobando tambem o que foi pedido.
 - Para retornar o contato criado usei mais uma função do PDO o [lastInsertId()](https://www.php.net/manual/pt_BR/pdo.lastinsertid.php) que faz basicamente o que o nome sugere, ele retorna o id gerado pelo INSERT específico dessa conexão. Com esse id fiz uma query pra buscar o contato recém inserido e usei json() junto com format() para retornar o status 201.

## Tarefa Bônus 
- Iniciei essa tarefa fazendo as validações pra ver se a transportadora e o contato eram válidos, ou seja, se existiam.
- Utilizei basicamente o mesmo código da função show(), mas trocando o verbo da instrução para DELETE e retornando o status da requisição com o http_response_code.
- Nessa última etapa de retornar o status da requisição tentei utilizar a função json() mas como ela tinha uma config padrão de sempre exibir um "echo" mesmo passando null como parâmetro, optei por usar o http_response_code mesmo. 
- Para a última parte da validação do email duplicado, também reutilizei o código da função show(), apenas trocando o cid pelo email na  query na função store() 

## Autor

**Bernardo Marques** -> [@bernardov013](https://github.com/bernardo013)
