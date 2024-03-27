<?php

namespace app\library;

use app\database\model\Model;

/**
 * Classe Query
 * Responsável por construir consultas SQL dinâmicas.
 */
class Query
{
    protected array $data; // Array para armazenar os dados da consulta
    public ?Model $modelInstance = null; // Instância do modelo associado à consulta
    public Paginate $paginate; // Objeto de paginação para consultas paginadas

    /**
     * Define as colunas a serem selecionadas na consulta.
     *
     * @param array|string $select Colunas a serem selecionadas
     * @return $this
     */
    public function select(array|string $select)
    {
        $this-> data['select'] = $select;

        return $this;
    }
    
    /**
     * Adiciona uma cláusula WHERE à consulta.
     *
     * @param string $field Campo da cláusula WHERE
     * @param string $operator Operador de comparação
     * @param string|int $value Valor a ser comparado
     * @param string|null $logic Lógica adicional para a cláusula WHERE
     * @return $this
     */
    public function where(string $field, string $operator, string|int $value, ?string $logic = null)
    {
        $this->data['where'][] = "{$field} {$operator} :{$field} {$logic}";
        $this->data['binds'][$field] = $value;

        return $this;
    }
    
    /**
     * Define o limite de resultados da consulta.
     *
     * @param int $limit Limite de resultados
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->data['limit'] = $limit;

        return $this;
    }
    
    /**
     * Define a ordem de classificação dos resultados da consulta.
     *
     * @param string $order Ordem de classificação
     * @return $this
     */
    public function order(string $order)
    {
        $this->data['order'] = $order;

        return $this;
    }
    
    /**
     * Define o deslocamento inicial dos resultados da consulta.
     *
     * @param int $offset Deslocamento inicial
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->data['offset'] = $offset;

        return $this;
    }

    /**
     * Define a classe do modelo associado à consulta.
     *
     * @param string $model Classe do modelo
     */
    public function model(string $model)
    {  
        if(class_exists($model) && !$this->modelInstance){
            $this->modelInstance = new $model;
        }
    }

    /**
     * Define a classe do modelo e habilita a paginação.
     *
     * @param string $model Classe do modelo
     */
    public function paginate(string $model)
    {
        if(class_exists($model) && !$this->modelInstance){
            $this->modelInstance = new $model;
            $this->paginate = new Paginate($this->modelInstance, $this);
        }
    }

    /**
     * Cria uma consulta SQL com base nos dados fornecidos.
     *
     * @param array $transformsSelected Transformações a serem aplicadas à consulta
     * @return array Consulta SQL e parâmetros correspondentes
     */
    public function createQuery(array $transformsSelected)
    {
        $transformed = [];
        foreach ($transformsSelected as $transform) {
            $transformed[$transform] = $this->transform($transform);
        }

        return array_values($transformed);
    }

    /**
     * Reseta os dados da consulta.
     */
    private function reset()
    {
        $this->data = [];
    }

    /**
     * Transforma os dados da consulta em uma string SQL.
     *
     * @param string $field Campo a ser transformado
     * @return string|null String SQL correspondente ao campo
     */
    private function transform($field)
    {
        $data = [];
        switch ($field) {
            case 'select':
                if(isset($this->data[$field]) && is_array($this->data[$field])){
                    $data[$field] = rtrim(implode(',', $this->data[$field]));
                }
                break;
            case 'where':
                if(isset($this->data[$field]) && is_array($this->data[$field])){
                    $data[$field] = ' where '.implode(' ', $this->data[$field]);
                }
                break;
            case 'limit':
                if(isset($this->data[$field])){
                    $data[$field] = ' limit '.$this->data[$field];
                }
                break;
            case 'offset':
                if(isset($this->data[$field])){
                    $data[$field] = ' offset '.$this->data[$field];
                }
                break;
            case 'order':
                if(isset($this->data[$field])){
                    $data[$field] = ' order by '.$this->data[$field];
                }
                break;

            default: 
                break;
        }

        return $data[$field] ?? null;
    }

    /**
     * Retorna o valor de um campo específico dos dados da consulta.
     *
     * @param string $field Campo a ser retornado
     * @return mixed Valor do campo
     */
    public function get(string $field)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Retorna todos os dados da consulta.
     *
     * @return array Todos os dados da consulta
     */
    public function getData()
    {
        $this->transform('select');
        $this->transform('where');
        $this->transform('limit');
        $this->transform('order');

        return $this->data;
    }
}
