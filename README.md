markdown

# Instalação do Projeto

Este guia fornece as instruções necessárias para instalar e configurar o projeto.

## Utils

- Instalador Laravel Sail
- PHP ^8.2
- Composer
- Laravel 11

## Instalação
```bash
git clone https://github.com/LuizEscobarC/Bank-Account.git

cd AccountBankManager

./vendor/bin/sail up -d

composer install 

# Copie o arquivo .env.example para criar um arquivo .env e configure as variáveis de ambiente:
Bash

cp .env.example .env

# Certifique-se de definir a APP_KEY gerando uma nova chave:

./vendor/bin/sail artisan key:generate



```
## SHORTCUTS DE TESTES

 
# Testando o job e fila para agendamento
```bash
./vendor/bin/sail artisan migrate

./vendor/bin/sail artisan seed

# na raiz do projeto rode para trocar o agendamento da regra de negócio pra algo mais testavel...
sed -i.bak "s/dailyAt('05:00')/everyMinute()/g" ./routes/console.php
#ou altere no arquivo de rotas de console

# após isso abra dois terminais, um será pra rodar a fila e outro pra testar o que quiser
# antes de rodar a fila abra o banco e repare nos valores de balance, amount, de cada usuario
# _ e suas transferências agendadas

# inicie a fila e espera 1 minuto, se quiser pode abrir multiplor workers
./vendor/bin/sail artisan queue:work

# após rodar o jobs de agendamento e o de processamento de transações agendadas de uma_
# olhada no banco novamente e verá que todas as regras de negócio estão corretas.
# com isso é só ir modificando o banco ou gerando dados para ver as mutiplas regras de negócio acontecendo
```

# endpoint de criação de accountBank
```bash
#rode na raiz
php artisan scribe:generate

# Após isso visite:
open http://localhost:80/docs
```

## REsumindo endpoints

- POST /api/account-banks/store 
- - cria um usuário podendo personalizar name e balance
- - escolhi o uuid pois vi no documento ali da regra de negócio que poderia ser incremental ou não
* request e response
```json
{"name":"John Doe","balance":"1000"}
{
    "data": {
        "id": "9cb429e2-ff09-42c8-9f30-a436ba8ce446",
        "name": "John Doe",
        "balance": 1000,
        "created_at": "2024-08-06 20:28:27",
        "updated_at": "2024-08-06 20:28:27"
    }
}
```

- POST /api/account-banks/transfer
- - gera uma transação que posse ser imediata ou não, isso vai depender de 3 fatores
- - - se o campo sheduled_at for preenchido a regra de negócio de transação de amount não é feita
- - - todo agendamento de transação começa como pending
- - - se o campo scheduled não for preenchido a regra de negócio é feita contato que seja autorizada pelo serviço externo
- - - se o campo scheduled foi agendado para o dia seguinte, as 5 am a file iria realizar a transação
- - - - se o autorizador liberar a transação, o servico de transação faz a transção e marca como transação completed e a data de processamento é armazenado
- - - - se o autorizador não liberar a transação, ela é marcada como not-authorized 
- - - - caso o autorizador esteja fora do ar e retorne um 500, o padrão é não autorizar a transação, decidi isso pois se fosse real não faria sentido liberar o envio de dinheiro sem autorização.
- - - - a autorização é aplicada no endpoint manual sem agendamento

# COMMAND CLI
```bash
# esse comando só permite preencher o nome, o valor é zerado por padrão
#input
./vendor/bin/sail artisan bank-account:create luiz

# output
echo "Conta bancária 'luiz' criada com sucesso."
```

# TESTES AUTOMATIZADOS

```bash
./vendor/bin/sail test --parallel

# criei testes unitários para factories, models, um service e observer
# porém, testes de integração/rota para o resto da api
# utilizei a mais atual Biblioteca de testes Pest


# utiliei o conceito de git hook para fazer automatizações de segurança entes do commits e push_
# com shell e a biblioteca husky para versionar os hooks
# com isso implementei o teste de integração pré push e no repositório antes de cada merge é feito um CI
# para poder testar formatação, padronização de código e testes no geral com o github Actions antes de entrar na develop e na main_
# seguindo o conceito de gitflow

# se for criar uma branch o git hook que criei obriga o padrão inicial ABM-1-nome_da_branch 

# fiz todo o projeto de plenejamento de produto e analise de requisitos antes de desenvolver 
# separei tudo em tasks (issues) e atribui a mim mesmo:  https://github.com/users/LuizEscobarC/projects/1/views/2
# arquivo de CI no git Actions: https://github.com/LuizEscobarC/Bank-Account/blob/develop/.github/workflows/laravel.yml
# workflow runs: https://github.com/LuizEscobarC/Bank-Account/actions

# muito obrigado pela atenção espero não ter decepcionado, se gostou dos meus pre commits e push personalizados,
# eu coloquei em um repositório: https://github.com/LuizEscobarC/troll-face-git-hook-husky
```
