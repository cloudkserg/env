<?php
/**
 * EnvironmentInterface
 *
 * @version 1.0.0
 * @copyright Copyright 2011 by Kirya <cloudkserg11@gmail.com>
 * @author Kirya <cloudkserg11@gmail.com>
 */
interface EnvironmentInterface
{

    /**
     * loadYii
     *
     * @return void
     */
    public function loadYii();

    /**
     * getParams
     *
     * @return array
     */
    public function getParams();


}
