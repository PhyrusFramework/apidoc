<?php

class ApidocController extends Controller {

    private $tree;
    private $counter = 0;

    private function initTree() {
        $this->tree = [
            '__no_section__' => []    
        ];
    }

    private function findFiles(string $path) {

        $list = [];

        $file = "$path/apidoc.json";
        if (file_exists($file)) {
            $list[] = $file;
        }

        $sub = subfolders($path);
        foreach($sub as $s) {
            $l = $this->findFiles($s);
            foreach($l as $item) {
                $list[] = $item;
            }
        }

        return $list;

    }

    private function parseFile(string $path) {

        $json = JSON::fromFile($path)->toArray();

        if (!isset($json['methods'])) {
            return;
        }

        $section = $json['section'] ?? '__no_section__';
        $auth = $json['authentication'] ?? null;

        // --------------

        if (!isset($this->tree[$section])) {
            $this->tree[$section] = [];
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
            $data['id'] = "e$this->counter";

            $methods[$method] = $data;
        }

        $this->tree[$section][] = [
            'auth' => $auth,
            'methods' => $methods
        ];

    }

    private function getTree() {

        $this->initTree();

        $files = $this->findFiles(Path::pages());

        foreach($files as $file) {
            $this->parseFile($file);
        }

    }

    public function init() {
        GoogleFonts::use('Roboto');
        Assets::include_css(Path::toRelative(__DIR__) . '/assets/apidoc.min.css');
        Assets::include_js(Path::toRelative(__DIR__) . '/assets/apidoc.js', true);
        $this->ajax = ['getApidocData'];
    }

    public function display() {
        view(__DIR__ . '/view.php');
    }

    public function getApidocData($req) {
        $this->getTree();

        $ops = Arr::instance([])->force([
            'title' => Config::get('title'),
            'version' => Config::get('version'),
            'base' => URL::host()
        ])->getArray();

        $ops['sections'] = $this->tree;

        return $ops;
    }

}