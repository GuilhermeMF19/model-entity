<?php

namespace app\database\relations;

use app\database\entity\Entity;
use app\database\interfaces\RelationshipInterface;
use app\library\Helpers;
use Exception;

/**
 * Classe RelationshipBelongsTo
 * Implementa a interface RelationshipInterface para criar relacionamentos "pertence a" entre entidades.
 */
class RelationshipBelongsTo implements RelationshipInterface
{
    /**
     * Cria uma relação "pertence a" entre entidades.
     *
     * @param string $class Classe principal da relação
     * @param string $foreignClass Classe estrangeira da relação
     * @param string $withProperty Nome da propriedade relacionada
     * @param array|Entity $results Resultados da consulta principal
     * @return object Objeto contendo os itens relacionados e o nome da propriedade de relacionamento
     * @throws Exception Quando a classe estrangeira não existe
     */
    public function createWith(string $class, string $foreignClass, string $withProperty, array|Entity $results): object
    {
        // Verifica se a classe estrangeira existe
        if (!class_exists($foreignClass)) {
            throw new Exception("Model {$foreignClass} does not exist");
        }

        // Obtém o nome curto da classe estrangeira e o nome da chave estrangeira
        $classShortName = Helpers::getClassShortName($foreignClass);
        $foreignKey = strtolower($classShortName) . '_id';

        // Obtém os IDs relacionados
        if (is_array($results)) {
            $ids = array_map(function ($data) use ($foreignKey) {
                return $data->$foreignKey;
            }, $results);
        }

        if ($results instanceof Entity) {
            $ids = $results->$foreignKey;
        }

        // Obtém os resultados da classe estrangeira
        $relatedWith = new $foreignClass;
        $resultsFromRelated = $relatedWith->relatedWith(is_array($ids) ? array_unique($ids) : $ids);

        // Associa os resultados relacionados aos resultados principais
        if ($results instanceof Entity) {
            $results->$withProperty = $resultsFromRelated[0];
        } else {
            foreach ($results as $data) {
                foreach ($resultsFromRelated as $dateFromRelated) {
                    if ($data->$foreignKey === $dateFromRelated->id) {
                        $data->$withProperty = $dateFromRelated;
                    }
                }
            }
        }

        // Retorna um objeto contendo os itens relacionados e o nome da propriedade de relacionamento
        return (object) [
            'items' => $results,
            'withName' => $withProperty,
        ];  
    }
}
