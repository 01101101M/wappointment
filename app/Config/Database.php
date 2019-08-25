<?php

namespace Wappointment\Config;

class Database
{
    public static $prefix_self = 'wappo';
    private static $capsule = null;

    public static function capsule()
    {
        if (is_null(self::$capsule)) {
            self::$capsule = new \Illuminate\Database\Capsule\Manager();

            self::$capsule->addConnection(self::config());

            self::$capsule->setAsGlobal();
            self::$capsule->bootEloquent();
        }

        return self::$capsule;
    }

    private static function config()
    {
        $db = new \Wappointment\WP\Database();

        return [
            'driver' => 'mysql',
            'host' => $db->getHost(),
            'port' => $db->getPort(),
            'database' => $db->getDbName(),
            'username' => $db->getDbUser(),
            'password' => $db->getDbPass(),
            'charset' => 'utf8',
            'collation' => $db->getDbCollate(),
            'prefix' => $db->getPrefix(),
            'strict' => true,
            'engine' => null,
        ];
    }
}
