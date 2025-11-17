# API Multi-Gateway Payment System

Sistema gerenciador de pagamentos multi-gateway desenvolvido em Laravel 10. Este projeto foi implementado seguindo as melhores práticas de desenvolvimento, Clean Code, TDD (Test-Driven Development) e arquitetura escalável.

## 📋 Sobre o Projeto

Este é um sistema completo de gerenciamento de pagamentos que permite processar transações através de múltiplos gateways de pagamento. O sistema tenta processar o pagamento em cada gateway seguindo uma ordem de prioridade configurável. Se um gateway falhar, o sistema automaticamente tenta o próximo gateway disponível.

### Funcionalidades Implementadas (Nível 3 - Completo)

✅ **Valor da compra calculado via back-end**
- Múltiplos produtos e quantidades
- Cálculo automático do valor total

✅ **Gateways com autenticação**
- Gateway 1: Autenticação via Bearer Token
- Gateway 2: Autenticação via Headers personalizados

✅ **Sistema de Roles e Permissões**
- **ADMIN**: Acesso total ao sistema
- **MANAGER**: Pode gerenciar produtos e usuários
- **FINANCE**: Pode gerenciar produtos e realizar reembolsos
- **USER**: Acesso padrão (visualizar clientes e transações)

✅ **TDD (Test-Driven Development)**
- Testes unitários
- Testes de integração/feature
- Cobertura de funcionalidades críticas

✅ **Docker Compose**
- MySQL 8.0
- Aplicação Laravel
- Mocks dos gateways

## 🛠 Tecnologias Utilizadas

- **PHP 8.2**
- **Laravel 10**
- **MySQL 8.0**
- **Laravel Sanctum** (Autenticação)
- **Guzzle HTTP** (Comunicação com gateways)
- **PHPUnit** (Testes)
- **Docker & Docker Compose**

## 📦 Requisitos

- Docker e Docker Compose
- Git

## 🚀 Instalação e Configuração

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd api-multi-gateway
```

### 2. Configure o ambiente

```bash
cp .env.example .env
```

### 3. Inicie os containers

```bash
docker-compose up -d
```

### 4. Instale as dependências

```bash
docker-compose exec app composer install
```

### 5. Gere a chave da aplicação

```bash
docker-compose exec app php artisan key:generate
```

### 6. Execute as migrations

```bash
docker-compose exec app php artisan migrate
```

### 7. Execute os seeders

```bash
docker-compose exec app php artisan db:seed
```

## 🧪 Executando os Testes

```bash
docker-compose exec app php artisan test
```

Ou para executar testes específicos:

```bash
docker-compose exec app php artisan test --filter PaymentTest
docker-compose exec app php artisan test --filter AuthTest
```

## 📚 Estrutura do Banco de Dados

### Tabelas

- **users**: Usuários do sistema com roles
- **gateways**: Gateways de pagamento configurados
- **clients**: Clientes que realizam compras
- **products**: Produtos disponíveis para compra
- **transactions**: Transações de pagamento
- **transaction_products**: Relação entre transações e produtos

## 🛣 Rotas da API

### Rotas Públicas

#### Autenticação
- `POST /api/login` - Realizar login

**Body:**
```json
{
  "email": "admin@betalent.tech",
  "password": "password"
}
```

#### Pagamentos
- `POST /api/payments` - Realizar uma compra

**Body:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "card_number": "5569000000006063",
  "cvv": "010",
  "products": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 2,
      "quantity": 1
    }
  ]
}
```

### Rotas Privadas (Requerem autenticação)

Todas as rotas privadas requerem o header:
```
Authorization: Bearer {token}
```

#### Autenticação
- `POST /api/logout` - Fazer logout
- `GET /api/me` - Obter informações do usuário autenticado

#### Gateways (ADMIN)
- `GET /api/gateways` - Listar todos os gateways
- `GET /api/gateways/{id}` - Detalhes de um gateway
- `PATCH /api/gateways/{id}/status` - Ativar/desativar gateway
- `PATCH /api/gateways/{id}/priority` - Alterar prioridade do gateway

#### Usuários (ADMIN, MANAGER)
- `GET /api/users` - Listar todos os usuários
- `POST /api/users` - Criar usuário
- `GET /api/users/{id}` - Detalhes de um usuário
- `PUT /api/users/{id}` - Atualizar usuário
- `DELETE /api/users/{id}` - Deletar usuário

#### Produtos (ADMIN, MANAGER, FINANCE)
- `GET /api/products` - Listar todos os produtos
- `POST /api/products` - Criar produto
- `GET /api/products/{id}` - Detalhes de um produto
- `PUT /api/products/{id}` - Atualizar produto
- `DELETE /api/products/{id}` - Deletar produto

#### Clientes (Todos autenticados)
- `GET /api/clients` - Listar todos os clientes
- `GET /api/clients/{id}` - Detalhes do cliente e suas compras

#### Transações (Todos autenticados)
- `GET /api/transactions` - Listar todas as transações
- `GET /api/transactions/{id}` - Detalhes de uma transação

#### Reembolsos (ADMIN, FINANCE)
- `POST /api/transactions/{id}/refund` - Realizar reembolso

## 👥 Usuários Padrão

Após executar os seeders, os seguintes usuários estarão disponíveis:

| Email | Senha | Role |
|-------|-------|------|
| admin@betalent.tech | password | ADMIN |
| manager@betalent.tech | password | MANAGER |
| finance@betalent.tech | password | FINANCE |
| user@betalent.tech | password | USER |

## 🔐 Sistema de Permissões

### ADMIN
- Acesso total ao sistema
- Pode gerenciar gateways
- Pode gerenciar usuários
- Pode gerenciar produtos
- Pode processar reembolsos

### MANAGER
- Pode gerenciar usuários
- Pode gerenciar produtos

### FINANCE
- Pode gerenciar produtos
- Pode processar reembolsos

### USER
- Pode visualizar clientes
- Pode visualizar transações

## 🏗 Arquitetura

### Services

O projeto utiliza o padrão Service para separar a lógica de negócio:

- **PaymentService**: Processa pagamentos e reembolsos
- **GatewayFactory**: Cria instâncias dos serviços de gateway
- **Gateway1Service**: Implementa comunicação com Gateway 1
- **Gateway2Service**: Implementa comunicação com Gateway 2

### Adicionar Novos Gateways

Para adicionar um novo gateway:

1. Criar um novo service implementando `GatewayServiceInterface`
2. Registrar o tipo no `GatewayFactory`
3. Adicionar o gateway no banco de dados

Exemplo:

```php
// app/Services/Gateway3Service.php
class Gateway3Service implements GatewayServiceInterface
{
    // Implementar métodos
}

// app/Services/GatewayFactory.php
return match ($gateway->type) {
    'gateway1' => new Gateway1Service($gateway),
    'gateway2' => new Gateway2Service($gateway),
    'gateway3' => new Gateway3Service($gateway), // Novo gateway
    default => throw new \InvalidArgumentException(...),
};
```

## 🐳 Docker

### Containers

- **app**: Aplicação Laravel (porta 8000)
- **db**: MySQL 8.0 (porta 3306)
- **gateway-mock**: Mocks dos gateways (portas 3001 e 3002)

### Comandos Úteis

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f app

# Acessar container
docker-compose exec app bash

# Executar comandos artisan
docker-compose exec app php artisan {comando}
```

## 📝 Validações

Todas as requisições são validadas através de Form Requests:

- Validação de email único
- Validação de cartão (16 dígitos)
- Validação de CVV (3 dígitos)
- Validação de produtos existentes e ativos
- Validação de roles e permissões

## 🧹 Clean Code

O projeto segue princípios de Clean Code:

- **SRP**: Cada classe tem uma responsabilidade única
- **DRY**: Reutilização de código através de services e traits
- **SOLID**: Princípios aplicados na arquitetura
- **Naming**: Nomes descritivos e significativos
- **Comentários**: Apenas onde necessário
- **Formatação**: Código consistente e legível

## 🧪 Testes

### Cobertura de Testes

- ✅ Testes de autenticação
- ✅ Testes de pagamentos
- ✅ Testes de permissões e roles
- ✅ Testes de reembolsos
- ✅ Testes de validações

### Executar Testes Específicos

```bash
# Todos os testes
docker-compose exec app php artisan test

# Apenas testes de feature
docker-compose exec app php artisan test tests/Feature

# Apenas testes unitários
docker-compose exec app php artisan test tests/Unit

# Teste específico
docker-compose exec app php artisan test --filter PaymentTest
```

## 🔌 Gateways Mock

Os gateways mock estão configurados e rodando nos containers. Eles simulam os comportamentos dos gateways reais:

### Gateway 1 (http://gateway-mock:3001)
- Autenticação via POST /login
- Token Bearer para requisições subsequentes
- CVV 100 ou 200 retorna erro

### Gateway 2 (http://gateway-mock:3002)
- Autenticação via headers
- CVV 200 ou 300 retorna erro

## 📊 Exemplos de Uso

### Realizar Pagamento

```bash
curl -X POST http://localhost:8000/api/payments \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "card_number": "5569000000006063",
    "cvv": "010",
    "products": [
      {"product_id": 1, "quantity": 2},
      {"product_id": 2, "quantity": 1}
    ]
  }'
```

### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@betalent.tech",
    "password": "password"
  }'
```

### Listar Transações (Autenticado)

```bash
curl -X GET http://localhost:8000/api/transactions \
  -H "Authorization: Bearer {token}"
```

## 🚧 Considerações Técnicas

- Todas as respostas são em JSON
- Valores monetários são armazenados em centavos (integer)
- Soft deletes implementado para as principais entidades
- Logs de erros e operações importantes
- Transações de banco para garantir consistência
- Tratamento de erros com mensagens claras

## 📄 Licença

Este projeto foi desenvolvido como teste prático para seleção de talentos.

## 👨‍💻 Autor

Desenvolvido seguindo as especificações do teste prático da BeTalent, por Luiz Fernando Viçoso.

---

**Nota**: Este projeto foi implementado no nível 3 (completo), incluindo todas as funcionalidades solicitadas e seguindo as melhores práticas de desenvolvimento.

