<?php

namespace app\database\relations;

use app\database\entity\Entity;
use app\database\interfaces\RelationshipInterface;
use app\database\model\Model;
use app\library\Helpers;
use Exception;

class RelationshipHasMany implements RelationshipInterface
{
    /**
     * Cria uma relação "hasMany" entre duas entidades.
     * 
     * @param string $class Classe principal da relação
     * @param string $foreignClass Classe estrangeira da relação
     * @param string $withProperty Nome da propriedade relacionada
     * @param array|Entity $results Resultados da consulta principal
     * @return object Objeto contendo os itens relacionados e o nome da propriedade de relacionamento
     * @throws Exception Se a classe estrangeira não existir
     */
    public function createWith(string $class, string $foreignClass, string $withProperty, array|Entity $results): object
    {
        // Verifica se a classe estrangeira existe
        if (!class_exists($foreignClass)) {
            throw new Exception("Model {$foreignClass} does not exist");
        }

        // Obtém o nome curto da classe principal
        $classShortName = Helpers::getClassShortName($class);

        // Sufixo do nome da classe principal com "id"
        $classNameWithIdSuffix = strtolower($classShortName) . '_id';

        // Inicializa a variável para armazenar os IDs relacionados
        $ids = null;

        // Verifica se os resultados são uma instância de Entity
        if ($results instanceof Entity) {
            $ids = $results->id;
        }

        // Verifica se os resultados são um array
        if (is_array($results)) {
            // Extrai os IDs do array de resultados
            $ids = array_map(function ($data) {
                return $data->id;
            }, $results);
        }

        // Cria uma nova instância da classe estrangeira
        $relatedWith = new $foreignClass;

        // Obtém os resultados relacionados com base nos IDs fornecidos
        $resultsFromRelated = $relatedWith->relatedWith(is_array($ids) ? array_unique($ids) : $ids, $classNameWithIdSuffix);

        // Atualiza os resultados da consulta principal com os resultados relacionados
        if ($results instanceof Entity) {
            $results->$withProperty = $resultsFromRelated;
        } else {
            foreach ($results as $data) {
                $arrayOfData = [];
                foreach ($resultsFromRelated as $dataFromRelated) {
                    // Verifica se há correspondência entre os IDs
                    if ($data->id === $dataFromRelated->$classNameWithIdSuffix) {
                        $arrayOfData[] = $dataFromRelated;
                    }
                }
                // Define a propriedade relacionada com os resultados correspondentes
                $data->$withProperty = $arrayOfData;
            }
        }

        // Retorna um objeto contendo os itens relacionados e o nome da propriedade de relacionamento
        return (object)[
            'items' => $results,
            'withName' => $withProperty,
        ];
    }
}
