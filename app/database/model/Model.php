<?php

namespace app\database\model;

use app\library\Query;
use PDO;
use Exception;
use app\database\Connection;
use app\database\entity\Entity;
use app\database\relations\RelationshipBelongsTo;
use app\database\interfaces\RelationshipInterface;
use app\library\Helpers;

/**
 * Classe abstrata Model
 * Define métodos básicos para interagir com o banco de dados.
 */
abstract class Model
{
    protected string $table; // Nome da tabela do modelo
    protected ?Query $query = null; // Instância de consulta
    protected array|Entity $results; // Resultados da consulta

    /**
     * Obtém o nome da classe da entidade associada ao modelo.
     *
     * @return string Nome da classe da entidade
     * @throws Exception Se a classe da entidade não existir
     */
    private function getEntity()
    {
        $class = Helpers::getClassShortName(static::class);
        $entity = "app\\database\\entity\\{$class}Entity";

        if (!class_exists($entity)) {
            throw new Exception("Entity {$entity} does not exist");
        }

        return $entity;
    }

    /**
     * Retorna todos os registros da tabela.
     */
    public function all()
    {
        try {
            $connection = Connection::getConnection();
            [$select, $where,$order,$limit,$offset] = $this->query->createQuery([
                'select', 'where', 'order', 'limit', 'offset',
            ]);
            $select = $select ?? '*';
            $query = "select {$select} from {$this->table}{$where}{$order}{$limit}{$offset}";
            $prepare = $connection->prepare($query);
            $prepare->execute($this->query->get('binds'));

            $this->results = $prepare->fetchAll(PDO::FETCH_CLASS, $this->getEntity());

            return $this;
        } catch (\PDOException $th) {
            var_dump($th->getMessage());
        }
    }

    /**
     * Retorna um único registro com base nas condições fornecidas.
     */
    public function find()
    {
        try {
            $connection = Connection::getConnection();
            [$select, $where] = $this->query->createQuery([
                'select', 'where',
            ]);
            $select = $select ?? '*';
            $query = "select {$select} from {$this->table}{$where}";
            $prepare = $connection->prepare($query);
            $prepare->execute($this->query->get('binds'));

            $this->results = $prepare->fetchObject($this->getEntity());

            return $this;
        } catch (\PDOException $th) {
            var_dump($th->getMessage());
        }
    }

    /**
     * Retorna o total de registros para a consulta atual.
     *
     * @param Query|null $query Consulta opcional
     */
    public function count(?Query $query = null)
    {
        try {
            $this->query = ($this->query) ?: $query;
            $connection = Connection::getConnection();
            [$where] = $this->query->createQuery(['where']);
            $sql = "select count(*) as total from {$this->table}{$where}";
            $prepare = $connection->prepare($sql);
            $prepare->execute($this->query->get('binds'));

            return $prepare->fetchObject($this->getEntity());
        } catch (\PDOException $th) {
            var_dump($th->getMessage());
        }
    }

    /**
     * Define a consulta a ser executada.
     *
     * @param Query $query Instância de consulta
     * @return $this
     */
    public function execute(Query $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Cria um novo registro no banco de dados.
     *
     * @param Entity $entity Entidade a ser criada
     */
    public function create(Entity $entity)
    {
        try {
            $connection = Connection::getConnection();
            $query = "insert into {$this->table}(";
            $query .= implode(',', array_keys($entity->getAttributes())) . ') values(';
            $query .= ':' . implode(',:', array_keys($entity->getAttributes())) . ')';

            $prepare = $connection->prepare($query);

            return $prepare->execute($entity->getAttributes());
        } catch (\PDOException $th) {
            var_dump($th->getMessage());
        }
    }

    /**
     * Cria uma relação entre dois modelos.
     *
     * @param string $class Classe do modelo relacionado
     * @param string $relation Classe da relação a ser criada
     * @param string $property Propriedade da relação
     * @param array|Entity $results Resultados da consulta
     * @return object Objeto contendo os resultados e o nome da propriedade
     */
    private function relation(string $class, string $relation, string $property, array|Entity $results)
    {
        if (!class_exists($class)) {
            throw new Exception("Model {$class} does not exist");
        }

        if (!class_exists($relation)) {
            throw new Exception("Relation {$relation} does not exist");
        }

        $classRelation = new $relation;
        if (!$classRelation instanceof RelationshipInterface) {
            throw new Exception("Class {$relation} is not type of RelationshipInterface");
        }

        return $classRelation->createWith(
            static::class,
            $class,
            $property,
            $results
        );
    }

    /**
     * Cria relações entre o modelo atual e outros modelos.
     *
     * @param mixed ...$relations Arrays contendo informações sobre as relações
     * @return array Resultados das relações
     */
    public function makeRelationsWith(...$relations)
    {
        $relationsCreated = [];
        foreach ($relations as $relationArray) {
            if (count($relationArray) !== 3) {
                throw new Exception('To make relations, yout need to give exactly 3 parameters to relations methods');
            }
            [$class,$relation,$property] = $relationArray;

            $relationsCreated[] = $this->relation($class, $relation, $property, $this->results);
        }

        if (count($relationsCreated) == 1) {
            return $relationsCreated[0]->items;
        }

        return $this->makeManyRelationsWith(...$relationsCreated);
    }

    /**
     * Cria várias relações entre modelos.
     *
     * @param mixed ...$relations Arrays contendo informações sobre as relações
     * @return array Resultados das relações
     */
    private function makeManyRelationsWith(...$relations)
    {
        $relation1 = $relations[0];
        unset($relations[0]);

        foreach ($relations as $value) {
            $withName = $value->withName;
            foreach ($value->items as $key => $object) {
                if (!property_exists($relation1->items[$key], $withName)) {
                    $relation1->items[$key]->$withName = $object->$withName;
                }
            }
        }

        return $relation1->items;
    }

    /**
     * Retorna os resultados da consulta.
     *
     * @return mixed Resultados da consulta
     */
    public function get()
    {
        if ($this->results) {
            return $this->results;
        }
    }

    /**
     * Retorna registros relacionados com base em IDs fornecidos.
     *
     * @param array|int $ids IDs dos registros relacionados
     * @param string $field Campo para correspondência de IDs
     * @return array Registros relacionados
     */
    public function relatedWith(array|int $ids, string $field = 'id')
    {
        $connection = Connection::getConnection();

        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }

        $query = "select * from {$this->table} where {$field} in (" . $ids . ')';
        $stmt = $connection->query($query);

        var_dump('related with executed');

        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->getEntity());
    }
}
