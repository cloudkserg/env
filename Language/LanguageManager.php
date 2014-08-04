<?php
/**
 * LanguageManager
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
class LanguageManager
{
    /**
     * _language
     *
     * @var string
     */
    private $_language;


    /**
     * _languages
     *
     * @var array
     */
    private $_languages;

    /**
     * _defaultLanguage
     *
     * @var string
     */
    private $_defaultLanguage;
    
    /**
     * _languageLocator
     *
     * @var LanguageLocator
     */
    private $_languageLocator;

    /**
     * __construct
     *
     * @param array $options
     * @return void
     */
    public function __construct($languages, $defaultLanguage, LanguageLocator $languageLocator)
    {
        $this->_languages = $languages;
        $this->_defaultLanguage = $defaultLanguage;

        $this->_languageLocator = $languageLocator;

    }

    /**
     * getConfig
     *
     * @return array
     */
    public function getConfig($basePath)
    {
        $config = array();
        $config['components'] = $this->getComponents();

        $configFile = $basePath . '/config/' . $this->_language . '.php';
        if (file_exists($configFile)) {
            $config = CMap::mergeArray(require($configFile), $config);
        }

        return $config;
    }

    /**
     * getComponents
     *
     * @return  array
     */
    private function getComponents()
    {
        $components = array();

        $components['urlManager'] = array(
            'class' => 'xtlan.cms.plugins.language.LanguageUrlManager',
            'baseUrl' => $this->_languageLocator->getBaseUrl(),
            'languages' => $this->_languages 
        );

        return $components;
    }

    /**
     * getLangauge
     *
     * @return string
     */
    public function getLanguage()
    {
        if (!isset($this->_language)) {
            $this->_language = $this->findLanguage();
            $this->setSessionLanguage($this->_language);
        }
        return $this->_language;
    }

    /**
     * findLanguage
     *
     * @return string
     */
    private function findLanguage()
    {
        //Обнаруживаем язык по данным пользователя (url)
        $language = $this->_languageLocator->locateLanguage();
        if (isset($language) and $this->validateLanguage($language)) {
            return $language;
        }

        //Обнаруживаем язык в сессии пользователя
        $language = $this->getSessionLanguage();
        if (isset($language) and $this->validateLanguage($language)) {
            return $language;
        }

        //Обнаруживаем язык по языку из браузера
        $language = $this->_languageLocator->getClientLanguage($language);
        if (isset($language) and $this->validateLanguage($language)) {
            return $language;
        }

        return $this->_defaultLanguage;
    }

    /**
     * validateLanguage
     *
     * @param string $language
     * @return void
     */
    private function validateLanguage($language)
    {
        if (in_array($language, $this->_languages)) {
            return $language;
        }
        return null;
    }


    /**
     * setSessionLanguage
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    private function setSessionLanguage($value)
    {
        $this->_session['language'] = $value;
    }


    /**
     * getSessionLanguage
     *
     * @param string $name
     * @return mixed (null|value)
     */
    private function getSessionLanguage()
    {
        if (isset($this->_session['language'])) {
            return $session['language'];
        }
        return null;
    }


}

