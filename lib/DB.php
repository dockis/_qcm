<?php

class DB
{
    private static $connection;

    private static $settings = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );

    /** Vytváří připojení na databázi.
     * @param $host // host - localhost apd.
     * @param $db // název databáze
     * @param $user // uživatelské jméno
     * @param $password // uživatelské heslo
     * @return PDO // instance PDO
     */
    public static function connect($host, $db, $user, $password)
    {
        if(!isset(self::$connection))
        {
            self::$connection = @new PDO("mysql:host=".$host.";dbname=".$db, $user, $password, self::$settings);
        }
        return self::$connection;
    }

    /** Předává dotaz a parametry instanci PDO
     * @param $sql // MySql dotaz - string
     * @param array $param // předávané parametry - array
     * @return mixed // vrací výsledek dotazu
     */
    public static function query($sql, $param = array())
    {
        $q = self::$connection->prepare($sql);
        $q->execute($param);
        return $q;
    }
}