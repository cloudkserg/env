<?php
/**
 * ConsoleManager
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
class ConsoleManager extends ApplicationManager implements ApplicationManagerInterface
{

    /**
     * _environment
     *
     * @var EnvironmentInterface
     */
    private $_environment;

   /**
     * __construct
     *
     * @param string $name
     * @param string $basePath
     * @param EnvironmentInterface $environment
     * @return void
     */
    public function __construct($name, $basePath, EnvironmentInterface $environment)
    {
        $this->_name = $name;
        $this->_basePath = $basePath;
        
        $this->_environment = $environment;
        $this->_debug = $environment->getDebug();
    }


    protected function buildConfig()
    {
        return CMap::mergeArray(
            $this->getDefaults(),
            $this->_environment->getParams(),
            $this->getAppConfig()
        );
    }

    /**
     * getDefaults
     *
     * @return array
     */
    private function getDefaults()
    {
        return array(
            'basePath' => $this->_basePath,
            'commandMap' => array(
                'migrate' => array(
                    'migrationPath' => $this->_basePath . '/migrations'
                )
            )
        );
    }

    /**
     * getAppConfig
     *
     * @return array     
     * */
    private function getAppConfig()
    {
        $config = $this->getConfigFromFile('console.php', 'console.local.php');
        
        //Загружаем пакеты
        if (isset($config['packages'])) {
            $packageConfig = $this->getPackageManager()->getConfigFromPackages(
                $config['packages'], 
                true
            );
            $config = CMap::mergeArray($packageConfig, $config);
        }        

        return $config;
    }

    /**
     * onBootstrap
     *
     * @return array
     */
    public function onBootstrap()
    {
        parent::onBootstrap();

        $moduleName = $this->defineModuleName();
        if (isset($moduleName)) {
            if (Yii::app()->hasModule($moduleName)) {
                $module = Yii::app()->getModule($moduleName);
            }
            unset($_SERVER['argv'][1]);
        }

    }

    /**
     * defineModuleName
     *
     * @return null|string
     */
    private function defineModuleName()
    {
        $arguments = getopt('m:', array('module:'));
        if (isset($arguments['m'])) {
            return $arguments['m'];
        } elseif (isset($arguments['module'])) {
            return $arguments['module'];
        }


        return null;
    }




}
