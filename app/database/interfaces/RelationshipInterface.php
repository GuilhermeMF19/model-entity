<?php

namespace app\database\interfaces;

use app\database\entity\Entity;

/**
 * Interface RelationshipInterface
 * Define um contrato para implementações de relacionamentos entre entidades.
 */
interface RelationshipInterface
{
    /**
     * Cria uma relação entre entidades.
     *
     * @param string $class Classe principal da relação
     * @param string $foreignClass Classe estrangeira da relação
     * @param string $withProperty Nome da propriedade relacionada
     * @param array|Entity $results Resultados da consulta principal
     * @return object Objeto contendo os itens relacionados e o nome da propriedade de relacionamento
     */
    public function createWith(string $class, string $foreignClass, string $withProperty, array|Entity $results): object;
}
