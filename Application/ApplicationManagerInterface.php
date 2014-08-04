<?php
/**
 * ApplicationManagerInterface
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
interface ApplicationManagerInterface
{

    /**
     * getYiiConfig
     *
     * @return array
     */
    public function getYiiConfig();

    /**
     * onBootstrap
     *
     * @return void
     */
    public function onBootstrap();

}
