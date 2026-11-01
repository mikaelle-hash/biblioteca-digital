# Biblioteca Digital

Sistema de gerenciamento de biblioteca desenvolvido como projeto acadêmico do curso de Engenharia de Software.

## Sobre o projeto

A Biblioteca Digital foi desenvolvida para facilitar o gerenciamento de livros, usuários e empréstimos, permitindo organizar as atividades de uma biblioteca por meio de uma aplicação web.

## Funcionalidades

- Cadastro e consulta de livros.
- Cadastro e gerenciamento de usuários.
- Controle de empréstimos.
- Registro de devoluções.
- Consulta de empréstimos.
- Controle de acesso conforme o perfil do usuário.

## Tecnologias utilizadas

- PHP
- HTML
- CSS
- JavaScript
- MariaDB / MySQL
- phpMyAdmin
- XAMPP
- Git e GitHub

## Estrutura do projeto

- `config/` – arquivos de configuração e conexão com o banco de dados.
- `css/` – arquivos de estilização.
- `js/` – scripts JavaScript.
- `pages/` – páginas de gerenciamento do sistema.
- `biblioteca_digital.sql` – script de criação e configuração do banco de dados.

## Requisitos para execução

- Windows.
- XAMPP instalado.
- Visual Studio Code ou editor de código equivalente.
- Navegador de internet.

## Instalação e execução

1. Baixe ou clone este repositório.
2. Coloque a pasta `biblioteca_digital` dentro do diretório `htdocs` do XAMPP.
3. Inicie os módulos Apache e MySQL no XAMPP.
4. Acesse o phpMyAdmin pelo endereço `http://localhost/phpmyadmin`.
5. Crie o banco de dados `biblioteca_digital`.
6. Importe o arquivo `biblioteca_digital.sql`.
7. Configure a conexão com o banco de dados no arquivo `config/conexao.php`, utilizando as credenciais do ambiente local.
8. Acesse o sistema pelo endereço `http://localhost/biblioteca_digital/`.

## Acesso ao sistema

O sistema possui controle de acesso por perfil de usuário, incluindo:

- Administrador.
- Bibliotecário.
- Leitor.

As credenciais de acesso devem ser fornecidas separadamente para cada usuário autorizado.

## Projeto acadêmico

Desenvolvido para fins acadêmicos como parte das atividades do curso de Engenharia de Software.

**Repositório:** https://github.com/mikaelle-hash/biblioteca-digital
