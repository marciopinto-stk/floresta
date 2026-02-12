# Contribuindo com o Floresta
Obrigado por contribuir com o projeto **Floresta**.
Este documento define as regras oficiais de desenvolvimento, organização e governança técnica do projeto.

# Princípios do Projeto
O Floresta segue:
- Arquitetura Modular
- Separação de responsabilidades (Interface → Application → Domain → Infrastructure)
- Rastreabilidade (RF → US → UC → Código)
- Padronização de branches, PRs e Issues
- Código limpo e testável

# Estratégia de Branches
## Branch principal
- `master` → sempre estável
- Toda alteração deve ocorrer via Pull Request

## Convenção de Nome de Branch

Formato obrigatório:
```
<tipo>/<modulo>-<issue-id>-<slug>
```

### Tipos permitidos
- `feature/` → nova funcionalidade
- `fix/` → correção de bug
- `chore/` → manutenção/refactor/config
- `docs/` → documentação
- `test/` → testes

### Exemplos
```
feature/medicos-123-importar-produtividade
fix/core-210-token-expirado
chore/infra-77-update-docker
docs/medicos-90-rf05
```

## Regras
- Toda branch deve estar vinculada a uma Issue.
- Nunca fazer commit direto na `master`.
- Branch deve ser deletada após merge.

# Pull Requests
## Regra obrigatória
- Todo PR deve usar o template oficial.
- Todo PR deve referenciar a Issue correspondente.

Exemplo no PR:
```
Closes #123
```

## Revisão
- Mínimo 1 aprovação.
- Todos os checks devem estar verdes.
- Conversas devem estar resolvidas.

# Arquitetura e Camadas
## Fluxo permitido de dependências
```
Interface → Application → Domain
Infrastructure → Domain
Application → Infrastructure (via contrato)
```

## Fluxo proibido
```
Domain → Infrastructure ❌
Domain → Application ❌
Application → Interface ❌
Módulo A → Infrastructure do Módulo B ❌
```

# Organização Modular
Cada módulo deve conter:
```
Domain/
Application/
Infrastructure/
Interface/
Routes/
Providers/
```

Controllers devem ser finos.
Regras de negócio devem estar na camada Application ou Domain.

# Testes
Sempre que aplicável:
- Criar testes unitários para UseCases
- Criar testes de integração para endpoints críticos
- Garantir que todos os testes passam antes do merge

# Convenção de Commits (Recomendado)

Formato:
```
<tipo>(<modulo>): <descrição>
```

### Exemplos
```
feat(medicos): implementar importacao de produtividade
fix(core): corrigir validacao de token
chore(infra): atualizar imagem docker
docs(wiki): atualizar modelo c4
test(medicos): adicionar testes do usecase
```

# Labels e Projects
Todos os trabalhos devem:
- Estar vinculados a uma Issue
- Estar associados a um EPIC (quando aplicável)
- Ter campos preenchidos no Project (Type, Module, Layer)

# Documentação
Antes de implementar:
- Verificar RF
- Verificar User Story
- Verificar Caso de Uso

Após implementar:
- Atualizar rastreabilidade se necessário
- Atualizar documentação afetada

# Boas Práticas Obrigatórias
- Controllers finos
- Sem regra de negócio em Request
- Sem acesso direto a DB fora de Infrastructure
- Sem acoplamento entre módulos
- Logs para falhas críticas

# O que NÃO fazer
- Commit direto na main
- Criar branch sem Issue vinculada
- Misturar múltiplas features no mesmo PR
- Ignorar padrão de arquitetura

# Evolução
Para grandes mudanças arquiteturais, criar:
- ADR (Architecture Decision Record)
- Documento de RFC (quando necessário)

# Fluxo Resumido
1. Criar Issue
2. Criar branch seguindo padrão
3. Implementar
4. Criar PR
5. Revisão
6. Merge via Squash
7. Deletar branch

# Histórico

| Versão | Data       | Autor        | Descrição |
|--------|------------|-------------|-----------|
| 1.0    | 11/02/2026 | Marcio Mota | Documento inicial criado |
