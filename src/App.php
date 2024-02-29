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

    public static function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    public static function post($key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    public static function view($view, $data = []) {
        $viewFile = self::$_templateDir . '/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("View file '$view' not found");
        }

        extract($data);
        ob_start();
        include_once $viewFile;
        $content = ob_get_clean();

        return require self::$_templateDir . '/layout.php';
    }

    public static function url($path) {
        return self::$_config['url'] . $path;
    }

    public static function asset($string) {
        return self::$_config['url'] . 'assets/' . $string;
    }
}
