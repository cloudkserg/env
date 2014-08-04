<?php
/**
 * Environment
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
class Environment implements EnvironmentInterface
{

    /**
     * params
     *
     * @var array
     */
    private $_params;

    /**
     * debug
     *
     * @var boolean
     */
    private $_debug = false;

    /**
     * __construct
     *
     * @param array $params
     * @return void
     */
    public function __construct($params)
    {
        $this->_params = $params;
        if (isset($this->_params['params']['debug'])) {
            $this->_debug = $this->_params['params']['debug'];
        }
    }


    /**
     * loadYii
     *
     * @return void
     */
    public function loadYii()
    {
        //load Yii
        $frameworkPath = ROOT_PATH . '/vendor/yii/yii';
        if ($this->_debug) {
            
            // remove the following lines when in production mode
            defined('YII_DEBUG') or define('YII_DEBUG', true);
            // specify how many levels of call stack 
            // should be shown in each log message
            defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
            
            $yii = $frameworkPath . '/yii.php';
        } else {
            $yii = $frameworkPath . '/yiilite.php';
        }
        require_once($yii);

    }

    /**
     * getDebug
     *
     * @return void
     */
    public function getDebug()
    {
        return $this->_debug;
    }

    /**
     * getParams
     *
     * @return void
     */
    public function getParams()
    {
        return $this->_params;
    }
}
