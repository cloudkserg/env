<?php
/**
 * IPackage
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
interface IPackage
{

    /**
     * getConfig
     *
     * @return array
     */
    public function getConfig();

    /**
     * onBootstrap
     *
     * @return void
     */
    public function onBootstrap();


}
