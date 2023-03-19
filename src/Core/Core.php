<?php

class Core
{
    private static array $DB = [];

    /**
     * @throws \Exception
     */
    private static function initDB(): PDO
    {
        $db_config  = [
            'dsn'      => getenv('DB_DSN'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'charset'  => getenv('DB_CHARSET'),
        ];
        $db_options = [
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $db_config['charset']
        ];
        try {
            $db = new PDO($db_config['dsn'], $db_config['username'], $db_config['password'], $db_options);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        unset($db_config, $db_options);

        return $db;
    }

    /**
     * @throws Exception
     */
    public static function DB(string $connect = 'default'): PDO
    {
        if (!isset(self::$DB[$connect])) {
            return self::$DB[$connect] = self::initDB();
        }
        return self::$DB[$connect];
    }

    public  static function CloseDB(string $connect = 'default'): void
    {
        self::$DB[$connect] = null;
    }
}