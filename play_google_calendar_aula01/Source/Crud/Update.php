<?php

/**
 * <b>Update [ UPDATE ]</b>
 * Classe responsável por atualizações genéricas no banco de dados!
 * 
 * @copyright (c) 2017, Robson V. Leite UPINSIDE TECNOLOGIA
 */

namespace CRUD;

USE \PDOException;
USE \PDO;

class Update {

    private $table;
    private $data;
    private $terms;
    private $places;
    private $result;

    /** @var PDOStatement */

    private $update;

    /** @var PDO */

    private $conn;
    
    /* Obtém conexão do banco de dados Singleton */

    public function __construct() {
        $this->conn = Conn::getConn();
    }

    /**
     * <b>update:</b> Executa uma atualização simplificada com Prepared Statments.
     *
     * @param STRING $table = Informe o nome da tabela no banco
     * @param ARRAY $data = Informe um array atribuitivo. ( Nome Da Coluna => Valor )
     * @param STRING $terms = WHERE coluna = :link AND.. OR..
     * @param STRING $parseString = link={$link}&link2={$link2}
     */

    public function update($table, array $data, $terms, $parseString) {
        $this->table = (string) $table;
        $this->data = $data;
        $this->terms = (string) $terms;

        parse_str($parseString, $this->places);
        $this->getSyntax();
        $this->execute();
    }

    /**
     * <b>getResult:</b> Retorna TRUE se não ocorrer erros, ou FALSE. Mesmo não alterando os dados se uma query
     * for executada com sucesso o retorno será TRUE. Para verificar alterações execute o getRowCount();
     *
     * @return BOOL
     */

    public function getResult() {
        return $this->result;
    }

    /**
     * <b>getRowCount:</b> Retorna o número de linhas alteradas no banco!
     *
     * @return INT $var = Quantidade de linhas alteradas
     */

    public function getRowCount() {
        return $this->update->rowCount();
    }

    /**
     * <b>setPlaces:</b> Método pode ser usado para atualizar com Stored Procedures. Modificando apenas os valores
     * da condição. Use este método para editar múltiplas linhas!
     *
     * @param STRING $ParseString = id={$id}&..
     */

    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->places);
        $this->getSyntax();
        $this->execute();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */

    //Obtém o PDO e Prepara a query
    private function connect() {
        $this->update = $this->conn->prepare($this->update);
    }

    /**
     * Cria a sintaxe da query para Prepared Statements
     */

    private function getSyntax() {
        foreach ($this->data as $Key => $Value):
            $places[] = $Key . ' = :' . $Key;
        endforeach;

        $places = implode(', ', $places);
        $this->update = "UPDATE {$this->table} SET {$places} {$this->terms}";
    }

    /**
     * Obtém a Conexão e a Syntax, executa a query!
     */

    private function execute() {
        $this->connect();
        $this->setNull();
        try {
            $this->update->execute(array_merge($this->data, $this->places));
            $this->result = true;
        } catch (PDOException $e) {
            $this->result = null;
            echo "<b>Erro ao Atualizar:</b> {$e->getMessage()} {$e->getCode()}";
        }
    }

    /**
     * Set empty data to NULL (Colaboration @Guilherme Augusto Teixeira De Matos)
     */

    private function setNull() {
        foreach ($this->data as $Key => $Value):
            $this->data[$Key] = ($Value == "" ? null : $Value);
        endforeach;
    }

}
