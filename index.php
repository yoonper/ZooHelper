<?php

class ZooHelper
{
    public function __construct($uri)
    {
        $this->_init($uri);
        $this->_autoload();
        $this->_bootstrap();
    }

    /**
     * 初始化
     * @param $uri
     */
    private function _init($uri)
    {
        error_reporting(0);
        define('PATH_ROOT', str_replace('\\', '/', dirname(__FILE__)));
        define('VERSION', '1.0');
        list($module, $method) = explode('/', trim($uri, '/'));
        define('MODULE', $module ?: 'index');
        define('METHOD', $method ?: 'index');
        date_default_timezone_set('prc');
    }

    /**
     * 自动加载
     */
    private function _autoload()
    {
        spl_autoload_register(function ($classname) {
            $config = ['ctl' => 'control', 'mdl' => 'model', 'lib' => 'library'];
            $dir = $config[substr($classname, 0, 3)];
            $file = sprintf('%s/%s/%s.php', PATH_ROOT, $dir, $classname);
            include_once $file;
        });
    }

    /**
     * 运行框架
     */
    private function _bootstrap()
    {
        $class = sprintf('ctl%s', ucfirst(MODULE));
        call_user_func([new $class(), METHOD]);
    }
}

new ZooHelper($_SERVER['SCRIPT_URL']);