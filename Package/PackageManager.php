<?php
/**
 * PackageManager
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
class PackageManager
{
    /**
     * _packages
     *
     * @var array
     */
    private $_packages = array();


    /**
     * _configured
     *
     * @var array
     */
    private $_configured = array();

    /**
     * _fileNames
     *
     * @var array
     */
    private $_fileNames = array();

    /**
     * getConfigFromPackages
     *
     * @param array $aliases
     * @param boolean isConsole
     * @return array
     */
    public function getConfigFromPackages($aliases, $isConsole = false)
    {
        $config = array();

        //Проходим по очереди aliases
        foreach ($aliases as $alias) {
            //Если alias уже сконфигурирован - пропускаем
            if (isset($this->_configured[$alias])) {
                continue;
            }

            //Получаем конфиг из пакета
            if (!$isConsole) {
                $packageConfig = $this->getPackage($alias)->getConfig();
            } else {
                $packageConfig = $this->getPackage($alias)->getConsoleConfig();
            }

            //Если у него есть зависимости - загружаем
            if (isset($packageConfig['packages'])) {
                $packageConfig = CMap::mergeArray(
                    $packageConfig,
                    $this->getConfigFromPackages($packageConfig['packages'], $isConsole)
                );
            }

            //Мержим с основным
            $config = CMap::mergeArray($config, $packageConfig);

            //Указываем что пакет сконфигурирован
            $this->_configured[$alias] = true;
        }

        return $config;
    }


    /**
     * getPackage
     *
     * @param string $alias
     * @return IPackage
     */
    private function getPackage($alias)
    {
        if (!isset($this->packages[$alias])) {
            //Получаем путь к файлу по алиусу пакета
            //это путь к папке с пакетом
            $path = Yii::getPathOfAlias($alias);

            //Далее определяем имя файла с классом
            $namePackage = basename($path);
            $nameClass = ucfirst($namePackage) . 'Package';

            //Подключаем этот файл
            require_once("{$path}/{$nameClass}.php");
            $this->_packages[$alias] = new $nameClass();
        }

        return $this->_packages[$alias];
    }

    /**
     * getBootstrapPackages
     *
     * @return array
     */
    public function getBootstrapPackages()
    {
        $bootstrapPackages = array();

        foreach ($this->_packages as $alias => $package) {
            $nameClass = get_class($package);
            $reflector = new ReflectionClass($nameClass);
            $pathClass = $reflector->getFileName();

            $bootstrapPackages[$alias] = array(
                'class' => $nameClass,
                'path' => $pathClass
            );
        }
        return $bootstrapPackages;
    }

    /**
     * onBootstrap
     *
     * @param array bootstrapPackaes
     * @return void
     */
    public function onBootstrap(array $bootstrapPackages)
    {
        foreach ($bootstrapPackages as $alias => $packageInfo) {
            if (!isset($this->_packages[$alias])) {
                require_once($packageInfo['path']);
                $this->_packages[$alias] = new $packageInfo['class'];
            }

            $this->_packages[$alias]->onBootstrap();
        }
    }

}
