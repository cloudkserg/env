<?php
/**
 * DummyConfigStore
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
class DummyConfigStore implements ConfigStoreInterface
{
    /**
     * _configs
     *
     * @var array
     */
    private $_configs = array();

    /**
     * getConfig
     *
     * @return array
     */
    public function getConfig($nameApp)
    {
        if (isset($this->_configs[$nameApp])) {
            return $this->_configs[$nameApp];
        }
        return null;
    
    }

    /**
     * setConfig
     *
     * @param string $nameApp
     * @param array $config
     * @return void
     */
    public function setConfig($nameApp, array $config)
    {
        $this->_configs[$nameApp] = $config;
    }

}
