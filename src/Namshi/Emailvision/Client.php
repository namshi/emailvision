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
    const BASE_URL                  = "http://api.notificationmessaging.com/";
    const ERROR_SERVER              = "Unable to send email: the Emailvision server replied with a status code %d and provided these informations:\n%s"; 
    const ERROR_UNKNOWN_TEMPLATE    = "The emailvision client doesn't have any configuration for the template '%s'";
    
    /**
     * Emailvision's configuration parameters.
     *
     * @var array
     */
    protected $emailvisionConfig    = array();
    
    /**
     * Parameters that are required in emailvision's configuration.
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
     * Parameters that are optional in emailvision's configuration.
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
     * Builds a URI for the emailvision's "REST" interface.
     * 
     * @param string $emailTemplate
     * @param array $dyn
     * @return Guzzle\Http\Message\MessageInterface
     */
    public function getEmailvisionUri($emailTemplate, array $dyn = array())
    {
        return $this->getBaseUrl() . 'NMSREST?' . http_build_query($this->getQueryStringParameters($emailTemplate, $dyn));
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
     * @param string $emailTemplate
     * @param array $dyn
     * @return array
     */
    protected function getQueryStringParameters($emailTemplate, array $dyn = array())
    {        
        if (count($dyn)) {
            foreach ($dyn as $key => $parameter) {
                $dyn[$key] = sprintf("%s:%s", $key, $parameter);
            }
            
            $this->emailvisionConfig[$emailTemplate]['dyn'] = implode('|', $dyn);
        }
        
        return $this->emailvisionConfig[$emailTemplate];
    }
    
    /**
     * Sends a request with the specified $dyn variables to emailvision's system,
     * that will send a generated email to the $recipient.
     *
     * @param string $template
     * @param string $recipient
     * @param array $dyn
     * @return Response
     * @throws Exception|InvalidArgumentException
     */
    public function sendEmail($template, $recipient, array $dyn = array())
    {
        try {
            if (!isset($this->emailvisionConfig[$template])) {
                throw new InvalidArgumentException(sprintf(self::ERROR_UNKNOWN_TEMPLATE, $template));
            }
            
            $this->emailvisionConfig[$template]['email'] = $recipient;

            return $this->send($this->createRequest('GET', $this->getEmailvisionUri($template, $dyn)));
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
        foreach ($config as $template => $templateConfig) {
            foreach ($this->mandatoryAttributes as $attribute) {
                if (!is_array($templateConfig) || !array_key_exists($attribute, $templateConfig)) {
                    throw new ConfigurationException($attribute, $template);
                }
            }

            if (isset($templateConfig['senddate'])) {
                if (!$templateConfig['senddate'] instanceOf DateTime) {
                    throw new InvalidArgumentException("The 'senddate' parameter needs to be a \DateTime object");
                }
                
                $templateConfig['senddate'] = $templateConfig['senddate']->format('Y-m-d H:i:s');
            } else {
                $date                       = new DateTime('2013-01-01 00:00:00');
                $templateConfig['senddate'] = $date->format('Y-m-d H:i:s');
            }

            $config[$template]  = $templateConfig;
        }
        
        return $config;
    }
}