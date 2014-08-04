<?php
/**
 * ConfigStoreInterface
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
interface ConfigStoreInterface
{


    /**
     * getConfig
     *
     * @param string $nameApp
     * @return array | null
     */
    public function getConfig($nameApp);

    /**
     * setConfig
     *
     * @param string $nameApp
     * @param array $config
     * @return void
     */
    public function setConfig($nameApp, array $config);


}
