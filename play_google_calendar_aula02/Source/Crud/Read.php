<?php

/**
 * <b>Read [SELECT]</b>
 * Classe responsável por leituras genéricas no banco de dados!
 *
 * @copyright (c) 2017, Robson V. Leite UPINSIDE TECNOLOGIA
 */

namespace CRUD;

USE \PDOException;
USE \PDO;

class Read
{

    private $select;
    private $places;
    private $result;

    /** @var PDOStatement */

    private $consult;

    /** @var PDO */

    private $conn;

    /* Obtém conexão do banco de dados Singleton */

    public function __construct()
    {
        $this->conn = Conn::getConn();
    }

    /**
     * <b>read:</b> Executa uma leitura simplificada com Prepared Statments.
     * Basta informar o nome da tabela, os termos da seleção e uma analize em cadeia (ParseString) para executar.
     *
     * @param STRING $table = Nome da tabela
     * @param STRING $terms = WHERE | ORDER | LIMIT :limit | OFFSET :offset
     * @param STRING $parseString = link={$link}&link2={$link2}
     */

    public function read($table, $terms = null, $parseString = null)
    {

        if (!empty($parseString)) {
            parse_str($parseString, $this->places);
        }

        $this->select = "SELECT * FROM {$table} {$terms}";
        $this->execute();
    }

    /**
     * <b>readFull:</b> Executa leitura de dados via query que deve ser montada manualmente para possibilitar
     * seleção de multiplas tabelas em uma única query!
     *
     * @param STRING $query = Query Select Syntax
     * @param STRING $parseString = link={$link}&link2={$link2}
     */

    public function readFull($query, $parseString = null)
    {

        $this->select = (string) $query;

        if (!empty($parseString)) {
            parse_str($parseString, $this->places);
        }

        $this->execute();
    }

    /**
     * <b>linkResult:</b> Obtém resultados relacionados de outra tabela por meio de coluna e valor associado!
     *
     * @param STRING $table = Nome da tabela
     * @param STRING $column = Nome da coluna relacionada a sua leitura atual!
     * @param INT $value = Valor relacionado, geralmente o ID que se associa a outra tabela!
     * @param STRING $fields = Campos Nome das colunas que deseja ler separadas por vírgula.
     * @return ARRAY
     */

    public function linkResult($table, $column, $value, $fields = null)
    {

        if ($fields) {
            $this->readFull("SELECT {$fields} FROM {$table} WHERE {$column} = :value", "value={$value}");
        } else {
            $this->read($table, "WHERE {$column} = :value", "value={$value}");
        }

        if ($this->getResult()) {
            return $this->getResult()[0];
        } else {
            return false;
        }
    }

    /**
     * <b>getResult:</b> Retorna um array com todos os resultados obtidos. Envelope primário númérico.
     * Para obter um resultado chame o índice getResult()[0]!
     *
     * @return ARRAY
     */

    public function getResult()
    {
        return $this->result;
    }

    /**
     * <b>getRowCount:</b> Retorna o número de registros encontrados pelo select!
     *
     * @return INT
     */

    public function getRowCount()
    {
        return $this->consult->rowCount();
    }

    public function setPlaces($parseString)
    {
        parse_str($parseString, $this->places);
        $this->execute();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */

    //Obtém o PDO e Prepara a query
    private function connect()
    {
        $this->consult = $this->conn->prepare($this->select);
        $this->consult->setFetchMode(PDO::FETCH_ASSOC);
    }

    /**
     * Cria a sintaxe da query para Prepared Statements
     */
    private function getSyntax()
    {
        if ($this->places) {
            foreach ($this->places as $param => $value) {
                if ($param == 'limit' || $param == 'offset') {
                    $value = (int) $value;
                }
                $this->consult->bindValue(":{$param}", $value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
            }
        }
    }

    /**
     * Obtém a Conexão e a Syntax, executa a query!
     */

    private function execute()
    {
        $this->connect();
        try {
            $this->getSyntax();
            $this->consult->execute();
            $this->result = $this->consult->fetchAll();
        } catch (PDOException $e) {
            $this->result = null;
            echo "<b>Erro ao Ler:</b> {$e->getMessage()} {$e->getCode()}";
        }
    }

}
