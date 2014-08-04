<?php
/**
 * ApplicationManager
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
abstract class ApplicationManager implements ApplicationManagerInterface
{
    const CONFIG_KEY = 'application_manager';

    /**
     * _packageManager
     *
     * @var PackageManagerInterface
     */
    protected $_packageManager;

    /**
     * _configStore
     *
     * @var ConfigStoreInterface
     */
    protected $_configStore;

    /**
     * _debug
     *
     * @var boolean
     */
    protected $_debug;

    /**
     * _name
     *
     * @var string
     */
    protected $_name;

    /**
     * _basePath
     *
     * @var string
     */
    protected $_basePath;

    /**
     * _config
     *
     * @var array
     * 
     * */
    protected $_config;

    /**
     * setConfigStore
     *
     * @param ConfigStoreInterface $configStore
     * @return void
     */
    public function setConfigStore(ConfigStoreInterface $configStore)
    {
        $this->_configStore = $configStore;
    }

    /**
     * getConfigStore
     *
     * @return ConfigStoreInterface
     */
    public function getConfigStore()
    {
        if (!isset($this->_configStore)) {
            if ($this->_debug) {
                $this->_configStore = new DummyConfigStore();
            } else {
                $this->_configStore = new ConfigStore($this->_basePath . '/runtime');
            }
        }

        return $this->_configStore;
    }
 

    /**
     * setPackageManager
     *
     * @param PackageManager $manager
     * @return void
     */
    public function setPackageManager(PackageManager $manager)
    {
        $this->_packageManager = $manager;
    }

    /**
     * getPackageManager
     *
     * @return PackageManager
     */
    public function getPackageManager()
    {
        if (!isset($this->_packageManager)) {
            $this->_packageManager = new PackageManager();
        }
        return $this->_packageManager;
    }

    /**
     * getConfig
     *
     * @return array
     */
    public function getYiiConfig()
    {
        //В конце парсим конфиг
        return   $this->parseConfig($this->getConfig());
    }

    /**
     * getConfig
     *
     * @return array
     */
    private function getConfig()
    {
        if (!isset($this->_config)) {
            //Получаем хранилище конфига
            $configStore = $this->getConfigStore();

            //Проверяем есть ли уже готовый конфиг
            $config = $configStore->getConfig($this->getNameConfig());
            if (!isset($config)) {

                //Если нет собираем его с нуля
                $config = $this->buildConfig();
                
                //Дополняем пакетной информацией
                $config[self::CONFIG_KEY]['bootstrapPackages'] = 
                    $this->getPackageManager()->getBootstrapPackages();


                //Кешируем
                $configStore->setConfig($this->getNameConfig(), $config);
            }
            
            $this->_config = $config;
        }

        return $this->_config;
    }

    /**
     * getNameConfig
     *
     * @return string
     */
    private function getNameConfig()
    {
        return $this->_name . '_' . get_class($this);
    }


    /**
     * onBootstrap
     *
     * @return void
     */
    public function onBootstrap()
    {
        $config = $this->getConfig();
        $this->getPackageManager()->onBootstrap(
            $config[self::CONFIG_KEY]['bootstrapPackages']
        );
    }



    /**
     * buildConfig
     * Функция которая строит конфиг
     *
     * @return array
     */
    abstract protected function buildConfig();

    /**
     * parseConfig
     * Парсим конфиг
     * Приготовляем его к обработке Yii::application
     *
     * @return array
     */
    protected function parseConfig(array $config)
    {
        //Задаем debug mode
        $config = $this->setDebugMode($config);

        //delete own config
        unset($config[self::CONFIG_KEY]);
        unset($config['packages']);

        return $config;
    }

    /**
     * setDebugMode
     *
     * @param array $config
     * @return array
     */
    private function setDebugMode(array $config)
    {
        $config['components']['db']['enableProfiling'] = $this->_debug;
        $config['components']['db']['enableParamLogging'] = $this->_debug;
        return $config;
    }



     /**
     * getConfigFromFile
     * Вспомогательная функция для получения 
     * конфига из двух файлов
     * второй не обязательный
     *
     * @param string $mainFile
     * @param string $localFile
     * @return array
     */
    protected function getConfigFromFile($mainFile, $localFile)
    {
        $mainPath = $this->_basePath . '/config/' . $mainFile;
        if (!file_exists($mainPath)) {
            throw new Exception(
                'Конфиг для сайта не задан.'
            );
        }
        $config = require($mainPath);

        $localPath = $this->_basePath . '/config/' . $localFile;
        if (file_exists($localPath)) {
            $config = CMap::mergeArray($config, require($localPath)); 
        }

        return $config;
    }




}
