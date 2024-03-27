# Projeto Entity PHP

Este é um projeto PHP que utiliza o padrão de arquitetura Model-Entity para interagir com um banco de dados relacional. Ele fornece uma estrutura básica para lidar com operações CRUD (Create, Read, Update, Delete) em entidades do banco de dados.

## Aviso
Este projeto foi baseado em um curso do Clube Full-stack.

## Funcionalidades

- **Métodos CRUD Básicos:** A classe `Model` fornece métodos para realizar operações básicas de criação, leitura e contagem de registros.
- **Definição de Consulta Flexível:** A classe `Query` permite definir consultas personalizadas com métodos encadeados, como `select`, `where`, `limit`, `order`, etc.
- **Relacionamentos entre Modelos:** O sistema oferece suporte para estabelecer relacionamentos entre diferentes modelos de banco de dados, como relacionamentos "hasMany" e "belongsTo".
- **Paginação de Resultados:** Há suporte para paginação de resultados para facilitar a navegação por grandes conjuntos de dados.

## Estrutura do Projeto

- **app/database:** Contém classes relacionadas ao acesso e manipulação de dados no banco de dados.
  - **model:** Classes que representam modelos de banco de dados.
  - **entity:** Classes que representam entidades de banco de dados.
  - **relations:** Classes para definir e gerenciar relacionamentos entre modelos.
- **app/library:** Contém classes e utilitários auxiliares.
- **public:** Diretório público acessível pelo navegador. Aqui estão os arquivos de entrada do aplicativo.

## Requisitos

- PHP >= 7.0
- Composer (para instalação de dependências)

## Instalação

1. Clone o repositório para o seu ambiente local.
2. Configure as credenciais do banco de dados no arquivo `Connection.php`.
3. Execute `composer install` para instalar as dependências do projeto.


## Uso

1. Defina suas classes de modelo (Model) e entidades (Entity) no diretório app/database/model e app/database/entity, respectivamente.

2. Defina os relacionamentos entre modelos usando as classes de relacionamento (RelationshipHasMany, RelationshipBelongsTo) no diretório app/database/relations.

3. Use a classe Query para construir consultas SQL de forma programática.

4. Execute os métodos find(), get(), count() para recuperar resultados do banco de dados.

5. Use o método makeRelationsWith() para carregar relacionamentos definidos entre modelos.

6. Utilize a paginação através do método paginate() e createLinks() para criar links de paginação.

## Contribuições são bem-vindas! Se você quiser melhorar este projeto, sinta-se à vontade para enviar pull requests ou abrir issues.

## Licença
1. Este projeto está licenciado sob a MIT License.