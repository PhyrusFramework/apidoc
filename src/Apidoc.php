<?php

class Apidoc {

    private static $tree;

    private static function initTree() {
        self::$tree = [
            '__no_section__' => []    
        ];
    }

    private static function findFiles(string $path) {

        $list = [];

        $file = "$path/apidoc.json";
        if (file_exists($file)) {
            $list[] = $file;
        }

        $sub = subfolders($path);
        foreach($sub as $s) {
            $l = self::findFiles($s);
            foreach($l as $item) {
                $list[] = $item;
            }
        }

        return $list;

    }

    private static function parseFile(string $path) {

        $json = JSON::fromFile($path)->toArray();

        if (!isset($json['methods'])) {
            return;
        }

        $section = $json['section'] ?? '__no_section__';

        $auth = $json['authentication'] ?? null;

        // --------------

        if (!isset(self::$tree[$section])) {
            self::$tree[$section] = [];
        }

        $rel = str_replace(Path::pages(), '', $path);
        $rel = str_replace('/pages', '', $rel);
        $rel = str_replace('/apidoc.json', '', $rel);
        $route = $rel;

        $methods = $json['methods'];
        foreach($methods as $method => $data) {
            if (!isset($data['path'])) {
                $data['path'] = $route;
            }
            else {
                $newPath = $data['path'];
                $newPath = str_replace('~', $route, $newPath);
                $data['path'] = $newPath;
            }
            $methods[$method] = $data;
        }

        self::$tree[$section][] = [
            'auth' => $auth,
            'methods' => $methods
        ];

    }

    private static function getTree() {

        self::initTree();

        $files = self::findFiles(Path::pages());

        foreach($files as $file) {
            self::parseFile($file);
        }

    }

    public static function display($options = []) {

        self::getTree();

        $ops = Arr::instance($options)->force([
            'title' => Config::get('title'),
            'version' => Config::get('version'),
            'base' => URL::host()
        ])->getArray();

        $ops['tree'] = self::$tree;

        $css = file_get_contents(__DIR__ . '/assets/apidoc.css');
        echo "<style>$css</style>";

        view(__DIR__ . '/view.php', $ops);

    }

}