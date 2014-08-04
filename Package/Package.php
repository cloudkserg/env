<?php
/**
 * Package
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
class Package implements IPackage, IConsolePackage
{

    /**
     * getConfig
     *
     * @return array
     */
    public function getConfig()
    {
        return require($this->getPath() . '/config/main.php');
    }

    /**
     * getConsoleConfig
     *
     * @return array
     */
    public function getConsoleConfig()
    {
        return require($this->getPath() . '/config/console.php');
    }

    /**
     * getPath
     *
     * @return string
     */
    private function getPath()
    {
        $class = new ReflectionClass($this);
        return  dirname($class->getFileName());
    }

    /**
     * onBootstrap
     *
     * @return void
     */
    public function onBootstrap()
    {
            return true;
    }

    /**
     * registerClassMap
     * @param array $userClassMap
     * @return void
     */
    public function registerClassMap(array $userClassMap)
    {
        Yii::$classMap = array_merge(
            Yii::$classMap,
            $userClassMap
        );
    }
}
