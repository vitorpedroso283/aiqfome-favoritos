# 🍔 AIQFome - Favoritos API

Esta é a **AIQFome Favoritos API**, uma REST API para **gerenciamento de clientes e seus favoritos de produtos**, integrada à [FakeStore API](https://fakestoreapi.com/docs), criada como parte do teste técnico para a vaga de Desenvolvedor Sênior.
O objetivo foi demonstrar boas práticas de arquitetura, planejamento e qualidade de código, entregando uma solução clara e escalável para o escopo proposto.

## 📑 Sumário

-   [🛠️ Contexto e Decisão de Arquitetura](#contexto)
-   [⚡️ Cacheamento](#cacheamento)
-   [🛠️ Tecnologias Utilizadas](#tecnologias)
-   [🏗️ Arquitetura e Estrutura do Projeto](#arquitetura)
-   [👤 Usuário Padrão para Testes](#usuario-teste)
-   [🚀 Como rodar o projeto](#como-rodar)
-   [✅ Testes e Documentação](#testes)
-   [📋 Exemplo de Requisições e Respostas](#exemplo-requisicoes)
-   [⚡️ Decisões de Simplicidade e Arquitetura](#decisoes)
-   [🙌 Considerações Finais](#consideracoes)

## 🛠️ Contexto e Decisão de Arquitetura <a id="contexto"></a>

Esta solução foi implementada considerando que a API poderia servir como camada interna do Aiqfome para administrar favoritos e dados de clientes, e não como uma camada diretamente exposta ao cliente final. Dessa forma:

-   Os endpoints recebem `customer_id` explicitamente, para permitir que uma camada interna (ex.: Backend do Aiqfome) faça operações para múltiplos clientes.
-   A autenticação (`user`) não representa o cliente final, mas sim o sistema ou operador responsável por administrar registros e favoritos, que nesse caso seria o 'AiQFome'.
-   Se futuramente necessária, poderia ser adaptada para derivar o `customer_id` direto do token, atendendo ao cenário de cliente final.

## ⚡️ Cacheamento <a id="cacheamento"></a>

Para garantir performance e reduzir chamadas à API externa:

-   **Os dados dos produtos são cacheados individualmente por ID por 24h.**
-   **As listas de favoritos são cacheadas por cliente e por página, ordenação e paginação, com duração de 1h.**
-   Sempre que um favorito é adicionado ou removido, os caches relacionados ao cliente são invalidados para evitar dados inconsistentes.
-   A escolha de cachear tanto produtos quanto as listas evita múltiplas requisições desnecessárias ao FakeStore, melhorando a escalabilidade da solução.
-   A cada adição ou remoção de favoritos, todas as chaves de cache associadas ao cliente são invalidadas para evitar dados desatualizados.
-   Se algum produto não existir mais na FakeStore, o registro correspondente é automaticamente excluído da tabela de favoritos para garantir consistência e limpeza dos dados.
-   Dessa forma, evitamos múltiplas requisições desnecessárias à FakeStore e garantimos uma experiência de resposta rápida e atualizada.

## 🛠️ Tecnologias Utilizadas <a id="tecnologias"></a>

- 🐘 **PHP 8.4** — Linguagem principal do projeto  
- ⚡️ **Laravel 12** — Framework para desenvolvimento REST  
- 🐳 **Docker / Docker Compose** — Containerização e ambiente isolado  
- 🗄️ **PostgreSQL** — Banco de dados relacional utilizado pela aplicação  
- ✅ **Pest** — Framework de testes para garantir qualidade e cobertura de casos críticos  
- 📄 **Swagger / OpenAPI** — Documentação clara e estruturada para a API  
- 🔐 **Sanctum** — Autenticação e controle de acesso para endpoints protegidos  
- 💾 **Cache Laravel** — Melhor performance e escalabilidade para integração com FakeStore  
- 🐞 **Log e Exceptions Laravel** — Controle de erros e registro para manutenção e debugging  
- 🔥 **Environment Variables (.env)** — Configuração flexível e segura para diferentes contextos de deployment  

## 🏗️ Arquitetura e Estrutura do Projeto <a id="arquitetura"></a>

A aplicação foi estruturada para refletir uma arquitetura clara e organizada, com foco em **separação de responsabilidades**, facilidade de manutenção e alinhada às boas práticas do Laravel.

#### 📁 Estrutura de Pastas e Camadas

-   **`app/Http/Controllers`** — Controladores responsáveis por receber as requisições e retornar as respostas HTTP.
    -   Ex.: `AuthController.php`, `CustomerController.php`, `CustomerFavoriteController.php`
-   **`app/Http/Requests`** — Validação centralizada para entradas de dados nas operações de Store, Update e List.
-   **`app/Http/Resources`** — Transformações e formatações dos dados antes de serem retornados para o cliente (JSON estruturado).
-   **`app/Services`** — Lógica de negócio desacoplada dos controladores:
    -   `Auth\AuthService.php`
    -   `Customer\CustomerService.php`
    -   `CustomerFavorite\CustomerFavoriteService.php`
    -   `Product\ProductDataProvider.php`
-   **`app/Dto`** — Objetos de Transferência de Dados para garantir consistência e tipagem clara nas operações de Create e Update.
-   **`app/Models`** — Modelos Eloquent para representar as entidades e comunicar com o banco de dados.
-   **`app/Rules`** — Validações específicas para casos de negócio (Ex.: verificar existência de produto externo).
-   **`routes/api.php`** — Definições dos endpoints e mapeamento para seus respectivos controladores.

#### ⚡️ Fluxo de Operações

1. O Controller recebe e valida a requisição.
2. O Service executa a lógica de negócio e realiza operações com Model e Provider.
3. Os dados são cacheados quando possível (usando Laravel Cache) para melhorar performance e evitar múltiplas chamadas à FakeStore.
4. Os Responses são transformados e padronizados por `Resources`.
5. Os Testes (`Pest`) garantem qualidade e cobertura para todas as operações.

#### 🌐 Integrações Externas

-   O `ProductDataProvider` centraliza todas as chamadas para a FakeStore API, implementa cache e lógica para verificar e retornar dados atualizados dos produtos.

#### ✅ Benefícios desta Arquitetura

-   ✅ Maior facilidade para manutenção e entendimento do código
-   ✅ Isolamento claro de responsabilidades (Controller, Service, Model, Resource, DTO)
-   ✅ Melhor controle de cache e integração com serviços externos
-   ✅ Testabilidade elevada (usando Pest) graças à clara separação de responsabilidades
-   ✅ Melhor controle de erros e consistência dos dados

#### 🔥 Suporte ao Ambiente e Dados

-   ✅ **Migrations** para versionamento e controle de schema do banco de dados.
-   ✅ **Seeders** para popular usuário padrão teste da api.
-   ✅ **Factories** para gerar dados consistentes e facilitar testes e desenvolvimento.

## 🚀 Como rodar o projeto <a id="como-rodar"></a>

Rodar esta API Laravel + PostgreSQL é simples e direto graças ao Docker! 🙌

### 📥 1️⃣ Clone o repositório

```bash
git clone https://github.com/vitorpedroso283/aiqfome-favoritos.git
cd aiqfome-favoritos
```

### ⚙️ 2️⃣ Suba todos os serviços

```bash
docker compose build
docker compose up
```

### ⚡️ O que isso vai subir

-   **app** — Laravel 12 rodando em [http://localhost:8000](http://localhost:8000)
-   **db** — PostgreSQL 17 (usando Alpine)

---

### 🐳 `docker-compose.yml` (já incluso)

```yaml
services:
    app:
        build:
            context: .
            dockerfile: Dockerfile
        container_name: laravel-app
        ports:
            - "8000:8000"
        depends_on:
            - db
        environment:
            DB_CONNECTION: pgsql
            DB_HOST: db
            DB_PORT: 5432
            DB_DATABASE: laravel
            DB_USERNAME: laravel
            DB_PASSWORD: laravel

    db:
        image: postgres:17.5-alpine
        container_name: laravel-db
        environment:
            POSTGRES_DB: aiqfome-favorite.db
            POSTGRES_USER: laravel
            POSTGRES_PASSWORD: laravel
        volumes:
            - db_data:/var/lib/postgresql/data
        ports:
            - "5432:5432"
volumes:
    db_data:
```

---

### ⚡️ Variáveis de Ambiente

> ✅ Já são configuradas automaticamente com o `.env` copiado para a imagem (`.env.example` ➔ `.env`)

Exemplo do `.env` utilizado:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

FAKESTORE_URL=https://fakestoreapi.com
```

---

### 🔑 Finalização

Depois de subir o container, gere a `APP_KEY` e rode as migrações para finalizar:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Feito! 🎉 Sua aplicação estará rodando em **http://localhost:8000** — todas as variáveis e dependências estão corretamente configuradas direto no build.

## 👤 Usuário Padrão para Testes <a id="usuario-teste"></a>

Esta aplicação cria automaticamente um usuário padrão para facilitar testes e integração:

-   **Email**: `esfomeado@user.com`
-   **Senha**: `tocomfome`

👉 O usuário é criado automaticamente através do **Seeder** (`database/seeders/UserSeeder.php`), não sendo necessária uma rota de registro para testes básicos.
Se quiser alterar as credenciais, atualize o arquivo `UserSeeder.php`.

## ✅ Testes e Documentação <a id="testes"></a>

Esta aplicação foi construída para facilitar testes e entendimento:

### 🐳 Testes Automatizados

-   Os testes foram escritos com **Pest**, garantindo qualidade e abrangência nas operações críticas:
    -   Autenticação (`login`)
    -   Operações de CRUD para clientes
    -   Adição e remoção de favoritos
    -   Validação de produtos através da FakeStore
-   Para rodar todos os testes:
    ```bash
    docker compose exec app php artisan test
    ```

---

### 📄 Documentação com Swagger

Todos os endpoints estão documentados e anotados para **OpenAPI/Swagger**:

-   Os controllers (`AuthController`, `CustomerController` e `CustomerFavoriteController`) têm anotações `@OA` para:
    -   Descrição clara dos endpoints
    -   Parâmetros (`path`, `query`, `body`) e exemplos
    -   Estrutura de retorno (`200`, `422`, `404`, `401`)

#### 💻 Como acessar

Se estiver utilizando Laravel Swagger (ou L5‑Swagger), depois de subir o ambiente:

-   Acesse a documentação em:
    ```
    http://localhost:8000/api/documentation
    ```
-   Você poderá navegar pelos endpoints e testá‑los direto do navegador.

---

### ✅ Resultado

-   **Swagger** para fácil entendimento e integração
-   **Pest** para garantir que todas as regras de negócio e casos críticos sejam atendidos e não sofram regressões

## 📋 Exemplo de Requisições e Respostas <a id="exemplo-requisicoes"></a>

### 🔐 Autenticação

**POST /api/auth/login**  
Exemplo de Request:

```bash
curl -X POST http://localhost:8000/api/auth/login -H "Content-Type: application/json" -d '{
  "email": "esfomeado@user.com",
  "password": "tocomfome"
}'
```

Exemplo de Response (200 — Sucesso):

```json
{
    "data": {
        "token": "6|abxZ9NxCBKDj5SGxa8oaB4OsJBbIxbJpUMh1ejmC824109e0"
    }
}
```

### 👥 Criar Cliente

**POST /api/customers**  
Exemplo de Request:

```bash
curl -X POST http://localhost:8000/api/customers -H "Authorization: Bearer SEU_TOKEN" -H "Content-Type: application/json" -d '{
  "name": "Maria Oliveira",
  "email": "maria.oliveira@exemplo.com"
}'
```

Exemplo de Response (201 — Sucesso):

```json
{
    "data": {
        "id": 42,
        "name": "Maria Oliveira",
        "email": "maria.oliveira@exemplo.com",
        "created_at": "2025-06-25 10:45:12",
        "updated_at": "2025-06-25 10:45:12"
    }
}
```

### ❤️ Adição de Favorito

**POST /api/customers/{customer_id}/favorites**  
Exemplo de Request:

```bash
curl -X POST http://localhost:8000/api/customers/42/favorites -H "Authorization: Bearer SEU_TOKEN" -H "Content-Type: application/json" -d '{
  "product_id": 1
}'
```

Exemplo de Response (201 — Sucesso):

```json
{
    "data": {
        "product_id": 1,
        "title": "Fjallraven - Foldsack No. 1 Backpack, Fits 15 Laptops",
        "price": 109.95,
        "image": "https://fakestoreapi.com/img/81fPKd-2YLpL._AC_SL1500_.jpg",
        "review": {
            "rate": 3.9,
            "count": 120
        }
    }
}
```

### 📋 Listagem de Favoritos

**GET /api/customers/{customer_id}/favorites**  
Exemplo de Request:

```bash
curl -X GET http://localhost:8000/api/customers/42/favorites -H "Authorization: Bearer SEU_TOKEN"
```

Exemplo de Response (200 — Sucesso):

```json
{
    "data": [
        {
            "product_id": 1,
            "title": "Fjallraven - Foldsack No. 1 Backpack, Fits 15 Laptops",
            "price": 109.95,
            "image": "https://fakestoreapi.com/img/81fPKd-2YLpL._AC_SL1500_.jpg",
            "review": {
                "rate": 3.9,
                "count": 120
            }
        }
    ],
    "links": {
        "first": "http://localhost/api/customers/42/favorites?page=1",
        "last": "http://localhost/api/customers/42/favorites?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/customers/42/favorites",
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

## ⚡️ Decisões de Simplicidade e Arquitetura <a id="decisoes"></a>

### 🗄️ Por que não usar Tags no Cache (Redis/Memcached)?

-   Não foram usados tags no cache por não estarmos com Redis ou Memcached configurados.
-   O cache padrão do Laravel foi suficiente para este escopo, garantindo eficiência e simples manutenção sem complexidade adicional.

### 🏗️ Por que uma Arquitetura Simples?

-   O escopo atual não exige DDD ou estrutura de pacotes complexos no Laravel.
-   A estrutura atual foi pensada para facilitar entendimento e manutenção para quem for evoluir o projeto no futuro.
-   Em um contexto maior, poderia evoluir para DDD e pacotes Laravel para melhorar escalabilidade e isolamento de domínios.

### ⚡️ Por que Laravel para este Teste?

-   Laravel foi escolhido para garantir velocidade e qualidade de entrega, sem perder tempo com detalhes de infraestrutura complexos.
-   O foco foi produzir uma solução clara, organizada e testada para o escopo requerido, entregando valor direto e evitando sobrecarga desnecessária no desenvolvimento.

### 🐞 Por que usar TDD (Test-Driven Development)?

-   O desenvolvimento guiado por testes foi utilizado para garantir **qualidade e confiança** desde o primeiro momento.
-   Escrever testes antes ou junto ao desenvolvimento ajuda a **entender e estruturar o problema**, reduzindo retrabalho e promovendo uma arquitetura mais clara e coerente.
-   Dessa maneira, cada feature foi implementada com uma **comprovação prática** de que atende ao escopo requerido, facilitando manutenção e evoluções futuras.
-   Resultado: menos bugs, mais previsibilidade e uma base de código sólida para escalar e adaptar.

### ⚡️ Limite de Requisições (Rate Limiting)

Esta API implementa **rate limiting** para aumentar a estabilidade e prevenir abusos. Por padrão:

- Limite de **120 requisições por minuto** por IP ou por usuário autenticado.
- Aplicado automaticamente nas rotas através do middleware `throttle:120,1` do Laravel.

Se precisar alterar, ajuste as configurações em:

- `routes/api.php`, para definir o middleware diretamente nas rotas.
- `App\Providers\RouteServiceProvider.php`, para personalização por IP, usuário ou tipo de rota.

---

## 🙌 Considerações Finais <a id="consideracoes"></a>

Este desafio foi uma ótima oportunidade para refletir e praticar boas decisões de arquitetura, planejamento e execução antes de simplesmente começar a codar.

Foi uma experiência divertida e enriquecedora (aproveitei para tomar bons cafés enquanto pensava nas soluções e estruturava cada camada para tornar o entendimento e manutenção do código mais simples e claro.)

Focar no entendimento do problema, alinhar qualidade e agilidade e construir uma solução clara e escalável são pilares para construir software que não só funciona, mas faz sentido para quem vai mantê‑lo no futuro.

**Obrigado pela oportunidade!** 🙏☕️
