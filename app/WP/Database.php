<?php

namespace Wappointment\WP;

class Database
{
    public $host = '';
    public $charset = '';
    public $collate = '';
    private $port = '3306';
    private $alt_port = '';
    private $prefix = '';
    private $mainprefix = '';

    public function __construct()
    {
        global $wpdb;

        $this->host = DB_HOST;
        $this->prefix = $wpdb->prefix;
        if (is_multisite() && defined('BLOG_ID_CURRENT_SITE')) {
            $this->mainprefix = $wpdb->get_blog_prefix(BLOG_ID_CURRENT_SITE);
        }

        if (strpos($this->host, ':') !== false) {
            $host_port = explode(':', $this->host);
            $this->host = $host_port[0];
            $this->port = $host_port[1];
        } else {
            if (!empty(ini_get('mysqli.default_port'))) {
                $this->alt_port = ini_get('mysqli.default_port'); // make sure this cannot break working connection
            }
        }
        $this->charset =  $wpdb->charset;
        $this->collate =  $wpdb->collate;
        //dd(ini_get('mysqli.default_port'));
    }

    public function getDbName()
    {
        return DB_NAME;
    }

    public function getDbUser()
    {
        return DB_USER;
    }

    public function getDbPass()
    {
        return DB_PASSWORD;
    }

    public function getDbCollate()
    {
        return $this->collate;
        /* $defaultCollate = $this->getDbCharset() != 'utf8mb4' ? 'utf8_general_ci' : 'utf8mb4_unicode_ci';
        return !defined('DB_COLLATE') || empty(DB_COLLATE) ? 'utf8_general_ci' : DB_COLLATE; */
    }

    public function getDbCharset()
    {
        return $this->charset;
        /* return !defined('DB_CHARSET') || empty(DB_CHARSET) ? 'utf8' : DB_CHARSET; */
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }
    public function getMainPrefix()
    {
        return $this->mainprefix;
    }
}
