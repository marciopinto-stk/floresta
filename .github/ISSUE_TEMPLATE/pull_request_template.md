# Pull Request — Floresta

## Objetivo
Descreva de forma clara o que este PR entrega e por quê.

## Referências
- EPIC: #
- RF: RFXX
- US: US-XXX-000
- UC: UC-XXX-000
- Issue(s): #, #, #

## O que foi feito
- 
- 
- 

## Impacto / Camadas
Marque o que foi afetado:
- [ ] Backend
- [ ] Frontend
- [ ] Banco legado (queries / repositórios)
- [ ] Infra / Docker
- [ ] Documentação

## Como validar

Passos para testar (manual e/ou automatizado):
1. 
2. 
3. 

### Evidências (prints / logs)
Inclua prints ou logs relevantes (quando aplicável).

## Checklist de Qualidade

### Arquitetura / Padrões
- [ ] Controller está fino (sem regra de negócio)
- [ ] Regra está em UseCase/Service (Application)
- [ ] Dependências seguem o fluxo (Interface → Application → Domain; Infrastructure implementa Domain)
- [ ] Não houve acoplamento entre módulos

### Segurança / Dados
- [ ] Validações feitas em FormRequest (quando aplicável)
- [ ] Erros retornam mensagens amigáveis (sem vazar detalhes internos)
- [ ] Logs de falha relevantes foram adicionados/ajustados

### Testes
- [ ] Testes unitários adicionados/ajustados (quando aplicável)
- [ ] Testes de integração/feature adicionados/ajustados (quando aplicável)
- [ ] Todos os testes passam localmente

### Documentação
- [ ] Wiki/Docs atualizados (se necessário)
- [ ] Rastreabilidade RF → US → UC → Código atualizada (se necessário)

## Riscos / Pontos de Atenção
Liste qualquer risco, trade-off, ou ponto que o revisor precisa observar.
- 
- 

## Plano de Rollback (se necessário)
Como reverter com segurança caso algo dê errado:
- 
- 

## Revisão
- **Revisor(es) sugeridos:** @
- **Observações para revisão:**
  - 
