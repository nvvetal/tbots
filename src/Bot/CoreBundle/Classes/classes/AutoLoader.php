<?php
class AutoLoader
{
    protected static $_path;
    protected static $_class = 'AutoLoader';
    protected static $_method = 'autoLoad';

    public static function addPath($path)
    {
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $path)) {
            return false;
        }

        $path = realpath($path);

        if (!is_dir($path)) {
            return false;
        }

        set_include_path(get_include_path() . PATH_SEPARATOR . $path);
        self::$_path[] = $path;
    }

    public static function register()
    {
        spl_autoload_register(array(self::$_class, self::$_method));
    }

    public static function unRegister()
    {
        spl_autoload_unregister(array(self::$_class, self::$_method));
    }

    public static function autoLoad($class)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return true;
        }
        if (!isset(self::$_path)) {
            self::_initPath();
        }

        foreach (self::$_path as $path) {
            if (self::_loadPath($path, $class)) {
                return true;
            }
        }

        return false;
    }

    public static function init()
    {
        self::_initPath();
    }

    protected static function _loadPath($path, $class)
    {
        $class = str_replace('\\', '/', $class);
        $classPath = explode('_', $class);
        $classFile = array_pop($classPath);
        $ourPath = $path;
        foreach ($classPath as $p) {
            if (is_dir($ourPath . '/' . $p)) {
                $ourPath = $ourPath . '/' . $p ;
            } elseif (is_dir($ourPath . '/' . strtolower($p))) {
                $ourPath = $ourPath . '/' . strtolower($p);
            } else {
                return false;
            }
        }
        if (is_readable($ourPath . '/' . $classFile . '.php')) {
            $file = $ourPath . '/' . $classFile . '.php';
        } else {
            return false;
        }
        /** @noinspection PhpIncludeInspection */
        require_once $file;

        return true;
    }

    public static function _initPath()
    {
        if (!empty(self::$_path)) {
            return true;
        }
        $myDir = realpath(DOC_ROOT.'/classes');
        self::$_path = array($myDir);

        return true;
    }
}

