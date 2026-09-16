# Baralho de Estudos — Laravel

Cartões acumulativos gerados durante as sessões de tutoria.
Formato compatível com importação no Anki (pergunta no título, resposta no corpo).

---

## Por que o Laravel exige `vendor/autoload.php` logo no início de `public/index.php`?

Porque é o autoloader PSR-4 gerado pelo Composer. Sem ele o PHP não sabe onde
encontrar nenhuma classe do framework nem do diretório `app/`, então a aplicação
não consegue nem iniciar o bootstrap.

Tags: #laravel #php #laravel::bootstrap #php::composer
Fonte: https://laravel.com/docs/10.x/structure
<!-- anki-id: laravel-vendor-autoload-bootstrap -->

---

## O que significa um erro `Failed opening required` no PHP e como ele difere de um erro de sintaxe?

Significa que o `require`/`require_once` não encontrou o arquivo no caminho
informado nem no `include_path`. É um erro de arquivo ausente ou caminho errado,
não de código inválido. A correção é criar/restaurar o arquivo ou ajustar o caminho.

Tags: #php #php::erros
Fonte: https://www.php.net/manual/en/function.require.php
<!-- anki-id: php-failed-opening-required -->

---

## Por que `vendor/` não vem no repositório Git de um projeto Laravel?

Porque está no `.gitignore`: as dependências são reconstruídas a partir de
`composer.json` e `composer.lock`, que já fixam as versões exatas. Isso mantém o
repositório pequeno e garante instalação reproduzível com `composer install`.

Tags: #php #php::composer #laravel::setup
Fonte: https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies
<!-- anki-id: composer-vendor-gitignored -->

---

## Quando usar `composer install` e quando usar `composer update`?

`composer install` respeita o `composer.lock` e instala as versões exatas já
travadas — é o comando para preparar um projeto existente. `composer update`
resolve novamente as restrições do `composer.json` e reescreve o lock, mudando
versões. Em ambiente de trabalho normal, use sempre `install`.

Tags: #php #php::composer
Fonte: https://getcomposer.org/doc/03-cli.md#install-i
<!-- anki-id: composer-install-vs-update -->

---

## Num projeto Laravel dockerizado, por que `composer install` deve rodar dentro do container?

Porque o container define a versão do PHP e as extensões instaladas. Rodando no
host, o Composer resolve as dependências contra outro ambiente e pode gravar em
`vendor/` pacotes incompatíveis com o PHP do container.

Tags: #laravel #docker #php::composer #laravel::docker
Fonte: https://getcomposer.org/doc/03-cli.md#install-i
<!-- anki-id: composer-install-dentro-do-container -->

---

## O que significa "Installation failed, reverting ./composer.json and ./composer.lock" no fim de um erro do Composer?

Que o comando **falhou** (saída diferente de 0) e nada foi instalado: o Composer
desfez as alterações que tinha feito nos dois arquivos. Não é aviso — é erro
fatal de resolução. O lado bom é que o projeto continua exatamente como estava.

Tags: #php #php::composer
Fonte: https://getcomposer.org/doc/articles/troubleshooting.md#your-requirements-could-not-be-resolved-to-an-installable-set-of-packages
<!-- anki-id: composer-installation-failed-reverting -->

---

## Num erro "Your requirements could not be resolved", quais linhas realmente indicam a causa raiz?

Três tipos: `your php version (X) does not satisfy` (restrição de plataforma),
`is locked to version ... an update was not requested` (atualização parcial
travada pelo lock) e `conflict with` (teto declarado por um pacote). O bloco
gigante de `Conclusion: don't install...` é só a lista do que foi descartado.

Tags: #php #php::composer #php::erros
Fonte: https://getcomposer.org/doc/articles/troubleshooting.md#your-requirements-could-not-be-resolved-to-an-installable-set-of-packages
<!-- anki-id: composer-ler-erro-resolucao -->

---

## Por que atualizar o Pest 2 arrasta obrigatoriamente o PHPUnit junto?

Porque cada release do Pest declara `require: phpunit/phpunit ^10.5.X` e ao mesmo
tempo `conflict: phpunit/phpunit > 10.5.X`, fixando um patch exato. Mudar o
PHPUnit exige mudar o Pest, e vice-versa — não dá para atualizar um isoladamente.

Tags: #php #php::composer #laravel::testes #pest
Fonte: https://pestphp.com/docs/installation
<!-- anki-id: pest-pin-phpunit-conflict -->

---

## Qual comando do Composer explica, em poucas linhas, por que uma versão específica não pode ser instalada?

`composer why-not <pacote> <versao>` (ex.: `composer why-not pestphp/pest 2.36`).
Ele mostra só as restrições que bloqueiam aquela versão, em vez do grafo inteiro
impresso por um `require` que falhou. O inverso é `composer why <pacote>`, que
lista quem depende dele.

Tags: #php #php::composer
Fonte: https://getcomposer.org/doc/03-cli.md#why-not-prohibits
<!-- anki-id: composer-why-not -->

---

## Num projeto Laravel padrão, os testes usam o mesmo banco da aplicação?

Não. O `phpunit.xml` sobrescreve o ambiente com `DB_CONNECTION=sqlite` e
`DB_DATABASE=:memory:`, criando um banco novo em memória a cada execução. Por
isso a suíte roda mesmo com o container do MySQL fora do ar — e por isso é
rápida e isolada. Exige a extensão `pdo_sqlite` no PHP.

Tags: #laravel #laravel::testes #phpunit
Fonte: https://laravel.com/docs/10.x/testing#environment
<!-- anki-id: laravel-testes-sqlite-memoria -->

---

## De onde vem um arquivo `phpunit.xml.bak` num projeto?

Do comando `phpunit --migrate-configuration`, que atualiza o `phpunit.xml` para o
schema da versão nova e guarda o original como `.bak`. Na migração para o PHPUnit
10, por exemplo, o bloco `<coverage><include>` passou a ser `<source><include>`.
O `.bak` é só resíduo: pode ser apagado depois de conferir o resultado.

Tags: #php #phpunit #laravel::testes
Fonte: https://docs.phpunit.de/en/10.5/configuration.html
<!-- anki-id: phpunit-migrate-configuration-bak -->

---

## Em testes HTTP do Laravel, o que acontece ao passar `postJson('auth.login', ...)` em vez de `postJson(route('auth.login'), ...)`?

O primeiro argumento é uma **URI**, não um nome de rota. `'auth.login'` vira o
caminho `/auth.login`, que não casa com nenhuma rota: a resposta é 404 e o
controller nunca roda. Assertions de validação falham com "Response does not
have JSON validation errors". Use sempre `route('nome')` para resolver o nome.

Tags: #laravel #laravel::testes #laravel::routing
Fonte: https://laravel.com/docs/10.x/http-tests#making-requests
<!-- anki-id: laravel-testes-postjson-uri-vs-route -->

---

## O que `trans()` / `__()` retornam quando a chave de tradução não existe?

A própria chave, como string. `trans('validation.requiride')` devolve
`"validation.requiride"` em vez de lançar erro — por isso um typo em teste vira
uma comparação silenciosamente errada. A chave correta é `validation.required`,
cuja mensagem é `The :attribute field is required.`

Tags: #laravel #laravel::localization #laravel::validation
Fonte: https://laravel.com/docs/10.x/localization#retrieving-translation-strings
<!-- anki-id: laravel-trans-chave-inexistente -->
