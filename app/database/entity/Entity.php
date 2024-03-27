<?php

namespace app\database\entity;

/**
 * Classe abstrata Entity
 * Serve como uma estrutura base para todas as entidades do banco de dados.
 */
abstract class Entity
{
    /** @var array Armazena os atributos da entidade */
    protected array $attributes = [];

    /**
     * Define um valor para um atributo da entidade.
     *
     * @param string $property Nome do atributo
     * @param mixed $value Valor a ser atribuído ao atributo
     */
    public function __set(string $property, mixed $value)
    {
        $this->attributes[$property] = $value;
    }

    /**
     * Obtém o valor de um atributo da entidade.
     *
     * @param string $property Nome do atributo
     * @return mixed Valor do atributo
     */
    public function __get(string $property)
    {
        return $this->attributes[$property];
    }

    /**
     * Obtém todos os atributos da entidade.
     *
     * @return array Lista de atributos da entidade
     */
    public function getAttributes()
    {
        return $this->attributes;    
    }
}
