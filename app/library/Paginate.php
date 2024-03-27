<?php

namespace app\library;

use app\database\model\Model;
use Exception;

/**
 * Classe Paginate
 * Responsável por implementar a paginação dos resultados de uma consulta.
 */
class Paginate
{
    private int $actualPage; // Página atual
    private int $pages; // Total de páginas

    /**
     * Construtor da classe Paginate.
     *
     * @param Model $model Instância do modelo para paginar
     * @param Query $query Instância da consulta para paginar
     */
    public function __construct(private Model $model, private Query $query)
    {
        // Define a página atual com base na query string 'page' ou padrão para 1
        $this->actualPage = $_GET['page'] ?? 1;
        // Obtém o limite de registros por página
        $perPage = $this->getLimit();
        // Calcula o total de registros para determinar o total de páginas
        $totalRecords = $this->totalRecords();
        // Define o deslocamento na consulta para a página atual
        $this->query->offset(ceil($this->actualPage - 1) * $perPage);
        // Calcula o total de páginas com base no total de registros e limite por página
        $this->pages = ceil($totalRecords / $perPage);
    }

    /**
     * Obtém o limite de registros por página.
     *
     * @return int Limite de registros por página
     * @throws Exception Se o limite não for definido
     */
    private function getLimit()
    {
        $limit = $this->query->get('limit');

        if (!$limit) {
            throw new Exception("To paginate please use limit method.");
        }

        return $limit;
    }

    /**
     * Obtém o total de registros para a consulta.
     *
     * @return int Total de registros
     */
    private function totalRecords()
    {
        return $this->model->count($this->query)->total;
    }

    /**
     * Cria os links de paginação.
     *
     * @param int $linksPerPage Número de links por página
     */
    public function createLinks(int $linksPerPage = 5)
    {
        // Determina o início e o fim dos links com base na página atual e no número de links por página
        $startLink = max(1, $this->actualPage - floor($linksPerPage / 2));
        $endLink = min($startLink + $linksPerPage - 1, $this->pages);

        // Loop para gerar os links de página
        for ($i = $startLink; $i <= $endLink; $i++) {
            // Se for a página atual, exibe em negrito
            if ($i == $this->actualPage) {
                echo "<strong>$i</strong> ";
            } else {
                // Senão, exibe como link para a página correspondente
                echo "<a href='?page=$i'>$i</a> ";
            }
        }
    }
}
