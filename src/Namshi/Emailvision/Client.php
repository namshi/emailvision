<?php

namespace Namshi\Emailvision;

use Guzzle\Service\Client as BaseClient;
use Namshi\Emailvision\Exception\Configuration as ConfigurationException;

/**
 * HTTP client tied to the emailvision "REST" interface.
 */
class Client extends BaseClient
{
    const BASE_URL = "http://api.notificationmessaging.com/";
    
    /**
     * Emailvision's configuration parameters.
     *
     * @var array
     */
    protected $emailvisionConfig    = array();
    
    /**
     * Parameters that are required in emailvision's configyration.
     *
     * @var array
     */
    protected $mandatoryAttributes  = array(
        'random',
        'encrypt',
        'senddate',
        'uidkey',
        'stype',
    );
    
    /**
     * Constructor
     * 
     * @param array $config Emailvision's configuration variables
     * @param type $httpConfig Configuration for the underlying Guzzle HTTP client
     */
    public function __construct(array $config, $httpConfig = null) 
    {
        $this->validateConfiguration($config);
        $this->emailvisionConfig = $config;
        
        parent::__construct(self::BASE_URL, $httpConfig);
    }
    
    /**
     * Create a request tied to emailvision's "REST" interface.
     * 
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param string $body
     * @return Guzzle\Http\Message\MessageInterface
     */
    public function createRequest($method = "GET", $uri = null, $headers = null, $body = null)
    {
        $uri = $this->getBaseUrl() . 'NMSREST?' . http_build_query($this->emailvisionConfig);
        
        return parent::createRequest($method, $uri, $headers, $body);
    }
    
    /**
     * Sends a request with the specified $dyn variables to emailvision's system,
     * that will send a generated email to the $recipient.
     * 
     * @param string $recipient
     * @param array $dyn
     * @return Response
     */
    public function sendEmail($recipient, array $dyn = array())
    {
        $this->emailvisionConfig['email'] = $recipient;
        
        return $this->send($this->createRequest());
    }
    
    /**
     * Validates the configuration parameters required by Emailvision.
     * 
     * @param array $config
     * @throws ConfigurationException
     */
    protected function validateConfiguration(array $config)
    {
        foreach ($this->mandatoryAttributes as $attribute) {
            if (!array_key_exists($attribute, $config)) {
                throw new ConfigurationException($attribute);
            }
        }
    }
}