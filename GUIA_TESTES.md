# 🧪 Guia: Como Executar os Testes

## 📋 Comandos Básicos

### Opção 1: Usando PHPUnit Diretamente (Recomendado)
```bash
docker-compose exec app php vendor/bin/phpunit
```

### Opção 2: Forçando Variáveis de Ambiente
Se der erro de banco de dados, force as variáveis:
```bash
docker-compose exec app bash -c "APP_ENV=testing DB_CONNECTION=sqlite DB_DATABASE=:memory: php vendor/bin/phpunit"
```

### Opção 3: Usando Artisan Test
```bash
docker-compose exec app php artisan test
```

**Nota:** O `phpunit.xml` já está configurado corretamente. Se houver conflito com `.env`, use a Opção 2.

### Executar Testes Específicos

#### Por arquivo:
```bash
docker-compose exec app php artisan test tests/Feature/PaymentTest.php
```

#### Por filtro (nome do teste):
```bash
docker-compose exec app php artisan test --filter PaymentTest
docker-compose exec app php artisan test --filter "can_process_payment"
```

#### Por suite (Unit ou Feature):
```bash
docker-compose exec app php artisan test --testsuite=Unit
docker-compose exec app php artisan test --testsuite=Feature
```

---

## 🎯 Testes Disponíveis

### Testes Unitários (`tests/Unit/`)
- **GatewayFactoryTest**: Testa a criação de serviços de gateway

### Testes de Feature (`tests/Feature/`)
- **AuthTest**: Testes de autenticação (login, logout, me)
- **PaymentTest**: Testes de processamento de pagamentos
- **UserTest**: Testes de gerenciamento de usuários
- **ProductTest**: Testes de gerenciamento de produtos
- **GatewayTest**: Testes de gerenciamento de gateways
- **RefundTest**: Testes de reembolsos

---

## 📊 Opções Úteis

### Ver Cobertura de Código
```bash
docker-compose exec app php artisan test --coverage
```

### Executar em Paralelo (mais rápido)
```bash
docker-compose exec app php artisan test --parallel
```

### Ver os 10 Testes Mais Lentos
```bash
docker-compose exec app php artisan test --profile
```

### Modo Compacto
```bash
docker-compose exec app php artisan test --compact
```

---

## 🔧 Configuração dos Testes

Os testes estão configurados para usar:
- **Banco de dados**: SQLite em memória (`:memory:`)
- **Ambiente**: `testing`
- **Cache**: `array` (em memória)
- **Sessão**: `array` (em memória)

Isso garante que os testes sejam:
- ✅ Rápidos (banco em memória)
- ✅ Isolados (cada teste tem seu próprio banco)
- ✅ Limpos (banco é resetado entre testes)

---

## 📝 Exemplos de Uso

### 1. Executar apenas testes de pagamento:
```bash
docker-compose exec app php artisan test --filter PaymentTest
```

### 2. Executar apenas testes de autenticação:
```bash
docker-compose exec app php artisan test --filter AuthTest
```

### 3. Executar um teste específico:
```bash
docker-compose exec app php artisan test --filter "can_process_payment_with_multiple_products"
```

### 4. Executar todos os testes e ver cobertura:
```bash
docker-compose exec app php artisan test --coverage
```

---

## 🐛 Troubleshooting

### Erro: "Database file does not exist"
**Solução:** Os testes usam SQLite em memória, não precisa criar arquivo. Se persistir:
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan test
```

### Erro: "Class not found"
**Solução:** Limpe o cache e reinstale:
```bash
docker-compose exec app composer dump-autoload
docker-compose exec app php artisan test
```

### Testes muito lentos
**Solução:** Use execução paralela:
```bash
docker-compose exec app php artisan test --parallel
```

---

## 📈 Estrutura dos Testes

```
tests/
├── Feature/          # Testes de integração/feature
│   ├── AuthTest.php
│   ├── PaymentTest.php
│   ├── UserTest.php
│   ├── ProductTest.php
│   ├── GatewayTest.php
│   └── RefundTest.php
├── Unit/             # Testes unitários
│   └── GatewayFactoryTest.php
└── TestCase.php     # Classe base para testes
```

---

## ✅ Checklist Antes de Executar Testes

- [ ] Containers estão rodando: `docker-compose ps`
- [ ] Dependências instaladas: `docker-compose exec app composer install`
- [ ] Cache limpo: `docker-compose exec app php artisan config:clear`


## 📚 Mais Informações

- [Documentação Laravel Testing](https://laravel.com/docs/10.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)

