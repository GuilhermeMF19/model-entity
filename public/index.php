<?php
use app\library\Query;
use app\database\model\Post;
use app\database\model\User;
use app\database\model\Comment;
use app\database\relations\RelationshipHasMany;
use app\database\relations\RelationshipBelongsTo;

require '../vendor/autoload.php';

$query = new Query;

// Seleciona apenas os campos 'id' e 'name'
$query->select('id, name')
    // Limita o resultado a 10 registros por página
    ->limit(10)
    // Filtra os registros para aqueles com 'id' maior que 1
    ->where('id', '>', 1)
    // Pagina o resultado usando a classe User como modelo
    ->paginate(User::class);

// Executa a consulta, encontra os registros e estabelece as relações com os posts
$users = $query->modelInstance->execute($query)->find()->makeRelationsWith(
    [Post::class, RelationshipHasMany::class, 'posts'],
);

// Conta o total de registros encontrados
$count = $query->modelInstance->count($query)->total;

// Exibe o total de registros
var_dump($count);

// Exibe uma linha de separação
var_dump('<br> __________________________________________________________________________________________________<br>');

// Exibe os links de paginação
var_dump(
    $query->paginate->createLinks(1)
);

// Exibe uma linha de separação
var_dump('<br> __________________________________________________________________________________________________<br>');

// Exibe os usuários encontrados
var_dump($users);
