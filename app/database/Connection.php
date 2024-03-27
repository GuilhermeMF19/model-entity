<?php

namespace app\database;

use PDO;

/**
 * Classe de conexão com o banco de dados.
 * 
 * Esta classe fornece uma conexão com o banco de dados usando PDO.
 */
class Connection
{
    // Declara uma propriedade privada e estática para armazenar a conexão PDO
    private static ?PDO $connect = null;

    // Define um método estático para obter a conexão com o banco de dados
    public static function getConnection()
    {
        // Verifica se a conexão ainda não foi estabelecida
        if (!self::$connect) {
            // Se ainda não foi estabelecida, cria uma nova instância PDO e atribui à propriedade estática
            self::$connect = new PDO('mysql:host=localhost; dbname=entity', 'root', '', [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]);
        }

        return self::$connect;
    }
}
