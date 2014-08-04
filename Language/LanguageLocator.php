<?php
/**
 * LanguageLocator
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
class LanguageLocator
{
    /**
     * _session
     *
     * @var CHttpSession
     */
    private $_session;


    /**
     * _baseUrl
     * property
     *
     * @var mixed
     */
    private $_baseUrl = '';

    /**
     * __construct
     *
     * @param string $baseUrl
     * @return void
     */
    public function __construct($baseUrl = '')
    {
        $this->_session = new CHttpSession();
        $this->_session->open();

        $this->_baseUrl = $baseUrl;
    
    }


    /**
     * locateLanguage
     *
     * @return string | null
     */
    public function  locateLanguage()
    {
        $baseUrl = $this->_baseUrl;
        if (!empty($baseUrl)) {
            $baseUrl = '/' . $baseUrl;
        }
        $mask  = "#^{$baseUrl}/(?<language>\w+)#Di";
        $request =  $_SERVER['REQUEST_URI'];

        if (preg_match($mask, $request, $matches)) {
            return $matches['language'];
        } 

        return null;
    }

    /**
     * getClientLanguage
     *
     * @return string
     */
    public function getClientLanguage()
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    /**
     * getBaseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }




}
