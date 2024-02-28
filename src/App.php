<?php

final class App
{
    private static $_templateDir;

    private static $_config = [];

    private static $_database = [];

    public static function setTemplateDir($dir) {
        self::$_templateDir = $dir;
    }

    public static function setConfig($config) {
        self::$_config = $config;
    }

    public static function config($key) {
        return self::$_config[$key];
    }

    public static function db() {
        if (empty(self::$_database)) {
            self::$_database = new Database(self::$_config['database']['host'], self::$_config['database']['username'], self::$_config['database']['password'], self::$_config['database']['database'], self::$_config['database']['port']);
        }

        return self::$_database;
    }

    public static function view($view, $data = []) {
        $viewFile = self::$_templateDir . '/' . $view;
        if (!file_exists($viewFile)) {
            throw new \Exception('View file not found');
        }

        extract($data);
        require $viewFile;
    }
}
