<?php
/**
 * SiteManager
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
class SiteManager extends ApplicationManager implements ApplicationManagerInterface
{

    /**
     * _info
     *
     * @var string
     */
    private $_info;

    /**
     * _baseUrl
     *
     * @var string
     */
    private $_baseUrl = '';

    /**
     * _languageManager
     *
     * @var LanguageManager
     */
    private $_languageManager;
    
    protected $_basePath;

    /**
     * __construct
     *
     * @param array $sites
     * @param LanaguageManager $manager
     * @param EnvironmentInterface $environment
     * @return void
     */
    public function __construct(array $sites, EnvironmentInterface $environment) 
    {
        $this->_environment = $environment;
        $this->_debug = $this->_environment->getDebug();

        //Если все удачно загружаем информацию
        $this->_name = $this->defineNameSiteByRequest($sites, $_SERVER['REQUEST_URI']);
        $this->_info = $sites[$this->_name];

        if (isset($this->_info['baseUrl'])) {
            $this->_baseUrl = $this->formatUrl($this->_info['baseUrl']);
        }
        $this->_basePath = Yii::getPathOfAlias($this->_info['basePath']);


    }

    /**
     * setLanaguageManager
     *
     * @param languageManager $languageManager
     * @return void
     */
    public function setLanaguageManager(languageManager $languageManager)
    {
        $this->_languageManager = $languageManager;
        $this->_name = $this->_name . $this->_languageManager->getLanguage();
    }

    /**
     * getLanguageManager
     *
     * @return void
     */
    public function getLanguageManager()
    {
        if (!isset($this->_languageManager)) {
            $this->_languageManager = new LanguageManager(
                $this->_info['internalization']['languages'],
                $this->_info['internalization']['defaultLanguage'],
                new LanguageLocator($this->_baseUrl)
            );

            $this->_name = $this->_name . $this->_languageManager->getLanguage();
        }
        return $this->_languageManager;
    }

    /**
     * buildConfig
     *
     * @return array
     */
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
            'params' => array(
                'baseUrl' => $this->_baseUrl
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
        $config = $this->getConfigFromFile('main.php', 'main.local.php');

        //Если сайт мультиязычный - 
        //коннектим главный конфиг с 
        //мультиязычной конфигурацией
        if ($this->isInternalizationSite()) {
            $config = CMap::mergeArray(
                $this->getLanguageManager()->getConfig($this->_basePath), 
                $config
            );
        }

        //Загружаем пакеты
        if (isset($config['packages'])) {
            $packageConfig = $this->getPackageManager()->getConfigFromPackages($config['packages']);
            $config = CMap::mergeArray($packageConfig, $config);
        }        


        return $config;
    }


    /**
     * isInternalizationSite
     *
     * @return boolean
     */
    private function isInternalizationSite()
    {
        return isset($this->_info['internalization']);
    }

    /**
     * defineNameSiteByRequest
     *
     * @param array $sites
     * @param mixed $request
     * @return string
     */
    private function defineNameSiteByRequest(array $sites, $request)
    {
        $currentName = null;
        $requestFormatted = $this->formatUrl($request);

        //Прохожим по очереди все сайты
        foreach ($sites as $name => $info) {
            //Форматируем baseUrl 
            if (!isset($info['baseUrl'])) {
                $currentName = $name;
                break;
            }

            $baseUrl = $this->formatUrl($info['baseUrl']);

            //Определяем подходит ли данный запрос под этот сайт
            $mask = '#^' . $baseUrl . '#Di';

            //Если подходит выходим
            if (preg_match($mask, $requestFormatted)) {
                //фиксируем текущее имя и baseURl
                $currentName = $name;
                break;
            }

        }    

        //Если нет таких сайтов - ошибка
        if (!isset($currentName)) {
            throw new Exception('Not find site with baseURl = currentRequest');
        }

        //Если все удачно загружаем информацию
        return $currentName;
    }

    /**
     * formatUrl
     *
     * @param string url
     * @return string
     */
    private function formatUrl($url)
    {
        return trim($url, '/');
    }



}
