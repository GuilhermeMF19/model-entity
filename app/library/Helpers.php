<?php

namespace app\library;

use ReflectionClass;

/**
 * Classe Helpers
 * Fornece métodos úteis para realizar várias operações no código.
 */
class Helpers
{
    /**
     * Obtém o nome curto de uma classe.
     *
     * @param object|string $class Nome da classe ou objeto da classe
     * @return string Nome curto da classe
     */
    public static function getClassShortName(object|string $class): string
    {
        $reflect = new ReflectionClass($class);
        return $reflect->getShortName();
    }
}
