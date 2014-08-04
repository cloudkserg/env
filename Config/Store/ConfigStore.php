<?php
/**
 * Description of ConfigStore
 *
 * @author art3mk4 <Art3mk4@gmail.com>
 */
class ConfigStore implements ConfigStoreInterface
{
    /**
     * _filePath
     *
     * @var string
     */
    private $_filePath;
    
    /**
     * _maxDiffTime
     *
     * @var int
     */
    private $_maxDiffTime = 3000;


    /**
     * construct
     * 
     * @param type $filepath
     */
    public function __construct($filepath)
    {
        $this->_filePath = $filepath;
    }
    
    /**
     * getConfig
     *
     * @param string $nameApp
     * @return mixed
     */
    public function getConfig($nameApp)
    {
        $file = $this->getFile($nameApp);

        if ($this->isValidate($file)) {
            return require($file);
        }

        return null;
    }

    /**
     * isValidate
     *
     * @param string $file
     * @return boolean
     */
    private function isValidate($file)
    {
        
        if (!file_exists($file)) {
            return false;
        }

        $diffTime = filemtime($file) - time();
        return $diffTime < $this->_maxDiffTime;
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
        $content = $this->buildContent($config);
        file_put_contents($this->getFile($nameApp), $content, LOCK_EX);
    }

    /**
     * buildContent
     *
     * @param array $config
     * @return string
     */
    private function buildContent(array $config)
    {
        $content = "<?php\n";
        $content .= "return ";
        $content .= var_export($config, true);
        $content .= ';';

        return $content;
    }

    /**
     * getFile
     *
     * @param string $nameApp
     * @return string
     */
    private function getFile($nameApp)
    {
        return $this->_filePath . '/' . $nameApp . '.conf.php';
    }
}
