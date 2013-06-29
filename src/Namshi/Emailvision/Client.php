<?php

namespace Namshi\Emailvision;

use Guzzle\Service\Client as BaseClient;
use Namshi\Emailvision\Exception\Configuration as ConfigurationException;
use InvalidArgumentException;
use DateTime;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Namshi\Emailvision\Exception;

/**
 * HTTP client tied to the emailvision "REST" interface.
 */
class Client extends BaseClient
{
    const BASE_URL      = "http://api.notificationmessaging.com/";
    const ERROR_SERVER  = "Unable to send email: the Emailvision server replied with a status code %d and provided these informations:\n%s"; 
    
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
        'uidkey',
        'stype',
    );
    
    /**
     * Parameters that are optional in emailvision's configyration.
     *
     * @var array
     */
    protected $optionalParameters  = array(
        'senddate',
    );
    
    /**
     * Constructor
     * 
     * @param array $config Emailvision's configuration variables
     * @param type $httpConfig Configuration for the underlying Guzzle HTTP client
     */
    public function __construct(array $config, $httpConfig = null) 
    {
        $this->emailvisionConfig = $this->validateConfiguration($config);
        
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
    public function createRequest($method = "GET", $uri = null, $headers = null, $body = null, array $dyn = array())
    {
        $uri = $this->getBaseUrl() . 'NMSREST?' . http_build_query($this->getQueryStringParameters($dyn));

        return parent::createRequest($method, $uri, $headers, $body);
    }
    
    /**
     * Returns all the query string parameters needed by the Emailvision REST
     * api to work, assembling the dynamic variables as per their specification.
     * 
     * Example:
     * - var1 = 1
     * - var2 = 2
     * 
     * results in:
     * ...&dyn=var1:1|var2:2
     * 
     * @param array $dyn
     * @return array
     */
    protected function getQueryStringParameters(array $dyn = array())
    {        
        if (count($dyn)) {
            foreach ($dyn as $key => $parameter) {
                $dyn[$key] = sprintf("%s:%s", $key, $parameter);
            }
            
            $this->emailvisionConfig['dyn'] = implode('|', $dyn);
        }
        
        return $this->emailvisionConfig;
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
        try {
            $this->emailvisionConfig['email']       = $recipient;

            return $this->send($this->createRequest('GET', null, null, null, $dyn));  
        } catch (ServerErrorResponseException $e) {
            throw new Exception(sprintf(self::ERROR_SERVER, $e->getResponse()->getStatusCode(), $e->getResponse()->getBody(true)));
        }
    }
    
    /**
     * Validates the configuration parameters required by Emailvision.
     * 
     * @param array $config
     * @return array
     * @throws ConfigurationException
     */
    protected function validateConfiguration(array $config)
    {
        foreach ($this->mandatoryAttributes as $attribute) {
            if (!array_key_exists($attribute, $config)) {
                throw new ConfigurationException($attribute);
            }
        }
        
        if (isset($config['senddate'])) {
            if (!$config['senddate'] instanceOf DateTime) {
                throw new InvalidArgumentException("The 'senddate' parameter needs to be a \DateTime object");
            }
        } else {
            $config['senddate'] = new DateTime('2013-01-01 00:00:00');
        }

        $config['senddate'] = $config['senddate']->format('Y-m-d H:i:s');
        
        return $config;
    }
}