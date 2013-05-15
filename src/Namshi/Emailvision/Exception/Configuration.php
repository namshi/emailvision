<?php

namespace Namshi\Emailvision\Exception;

use Exception;

/**
 * Exception responsible to report a fault in the configuration of the
 * Emailvision client.
 */
class Configuration extends Exception
{
    /**
     * Constructor
     * 
     * @param string $attribute
     */
    public function __construct($attribute)
    {
        $this->message = sprintf("You must provide the attribute '%s' in the Client configuration", $attribute);
    }
}
