<?php

class MyPDO extends PDO
{
    public function __construct($file = 'dbsetting.ini')
    {
        if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');

        $dns = 'mysql:host=' . $settings['database']['host'] .
        ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
        ';dbname=' . $settings['database']['dbname'];

        parent::__construct($dns, $settings['database']['username'], $settings['database']['password']);
    }
}
