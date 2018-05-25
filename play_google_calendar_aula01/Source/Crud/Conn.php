<?php

/**
 * <b>Conn [ CONNECTION ]</b>
 * Classe de conexão no padrão SingleTon com banco de dados mysql/mariadb.
 *
 * @copyright (c) 2017, Robson V. Leite UPINSIDE TECNOLOGIA
 */

namespace CRUD;

USE \PDOException;
USE \PDO;

class Conn
{
    /** Atributos da classe */

    private static $host = DATABASE['HOST'];
    private static $user = DATABASE['USER'];
    private static $pass = DATABASE['PASS'];
    private static $name = DATABASE['NAME'];

    /** @var PDO */

    private static $connect = null;

    /**
     * <b>getConn:</b> Retorna um objeto PDO Singleton Pattern.
     * @return PDO
     */

    public static function getConn()
    {
        return self::connect();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */

    /**
     * <b>connect:</b> Conecta com o banco de dados com o pattern singleton.
     * @return PDO
     */

    private static function connect()
    {
        try {

            if (self::$connect == null) {
                $dsn = 'mysql:host=' . self::$host . ';dbname=' . self::$name;
                $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
                self::$connect = new PDO($dsn, self::$user, self::$pass, $options);
                self::$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }

        return self::$connect;
    }

    /**
     * <b>__construct:</b> Construtor protegido previne que uma nova instância da
     * classe seja criada através do operador `new` de fora dessa classe.
     * @return void
     */

    private function __construct()
    {
    }

    /**
     * <b>__clone:</b> Método clone privado previne a clonagem dessa instância
     * da classe
     * @return void
     */

    private function __clone()
    {
    }

    /**
     * <b>__wakeup:</b> Método unserialize privado para previnir que desserialização
     * da instância dessa classe.
     * @return void
     */

    private function __wakeup()
    {
    }

}
