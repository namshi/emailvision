<?php

namespace Namshi\Emailvision\Exception;

use Exception;

/**
 * Exception responsible to report a fault in the configuration of the
 * Emailvision client.
 */
class Configuration extends Exception
{
    const MESSAGE = "You must provide the attribute '%s' for the template '%s' in the Client configuration";
    
    /**
     * Constructor
     * 
     * @param string $attribute
     */
    public function __construct($attribute, $template)
    {
        $this->message = sprintf(self::MESSAGE, $attribute, $template);
    }
}
