<?php

/**
 * <b>Create [ INSERT ]</b>
 * Classe responsável por cadastros genéricos no banco de dados!
 *
 * @copyright (c) 2017, Robson V. Leite UPINSIDE TECNOLOGIA
 */

namespace CRUD;

USE \PDOException;
USE \PDO;

class Create
{

    private $table;
    private $data;
    private $result;

    /** @var PDOStatement */

    private $create;

    /** @var PDO */

    private $conn;

    /* Obtém conexão do banco de dados Singleton */

    public function __construct()
    {
        $this->conn = Conn::getConn();
    }

    /**
     * <b>create:</b> Executa um cadastro simplificado no banco de dados utilizando prepared statements.
     * Basta informar o nome da tabela e um array atribuitivo com nome da coluna e valor!
     *
     * @param STRING $table = Informe o nome da tabela no banco
     * @param ARRAY $data = Informe um array atribuitivo. ( Nome Da Coluna => Valor )
     */

    public function create($table, array $data)
    {
        $this->table = (string) $table;
        $this->data = $data;

        $this->getSyntax();
        $this->execute();
    }

    /**
     * <b>createMultiple:</b> Executa um cadastro múltiplo no banco de dados utilizando prepared statements.
     * Basta informar o nome da tabela e um array multidimensional com nome da coluna e valores!
     *
     * @param STRING $table = Informe o nome da tabela no banco
     * @param ARRAY $data = Informe um array multidimensional. ( [] = Key => Value )
     */

    public function createMultiple($table, array $data)
    {
        $this->table = (string) $table;
        $this->data = $data;

        $fields = implode(', ', array_keys($this->data[0]));
        $places = null;
        $inserts = null;
        $links = count(array_keys($this->data[0]));

        foreach ($data as $valueMult) {
            $places .= '(';
            $places .= str_repeat('?,', $links);
            $places .= '),';

            foreach ($valueMult as $valueSingle) {
                $inserts[] = $valueSingle;
            }
        }

        $places = str_replace(',)', ')', $places);
        $places = substr($places, 0, -1);
        $this->data = $inserts;

        $this->create = "INSERT INTO {$this->table} ({$fields}) VALUES {$places}";
        $this->execute();
    }

    /**
     * <b>getResult:</b> Retorna o ID do registro inserido ou FALSE caso nenhum registro seja inserido!
     *
     * @return INT
     */

    public function getResult()
    {
        return $this->result;
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */

    /**
     * Obtém o PDO e Prepara a query
     */

    private function connect()
    {
        $this->create = $this->conn->prepare($this->create);
    }

    /**
     * Cria a sintaxe da query para Prepared Statements
     */

    private function getSyntax()
    {
        $fields = implode(', ', array_keys($this->data));
        $places = ':' . implode(', :', array_keys($this->data));
        $this->create = "INSERT INTO {$this->table} ({$fields}) VALUES ({$places})";
    }

    /**
     * Obtém a Conexão e a Syntax, executa a query!
     */

    private function execute()
    {
        $this->connect();

        try {
            $this->create->execute($this->data);
            $this->result = $this->conn->lastInsertId();
        } catch (PDOException $e) {
            $this->result = null;
            echo "<b>Erro ao Cadastrar:</b> {$e->getMessage()} {$e->getCode()}";
        }
    }

}
