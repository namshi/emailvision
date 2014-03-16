<?php

namespace Namshi\Emailvision;

use Namshi\Emailvision\Exception\Configuration as ConfigurationException;
use InvalidArgumentException;
use DateTime;
use Namshi\Emailvision\Exception;

/**
 * HTTP client tied to the emailvision "REST" interface.
 */
class Client
{
    const WSDL_URL                    = 'http://api.notificationmessaging.com/NMSOAP/NotificationService?wsdl';
    const ERROR_SERVER                = 'Unable to send email: the Emailvision server replied with "%s"';
    const ERROR_UNKNOWN_TEMPLATE      = 'The emailvision client doesn\'t have any configuration for the template \'%s\'';
    const SEND_REQUEST_SUCCESS_STATUS = 'success';

    /**
     * Emailvision's configuration parameters.
     *
     * @var array
     */
    protected $emailvisionConfig = array();

    /**
     * Parameters that are required in emailvision's configuration.
     *
     * @var array
     */
    protected $mandatoryAttributes = array(
        'random',
        'encrypt',
        'uidkey',
        'stype'
    );

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->emailvisionConfig = $this->validateConfiguration($config);
    }

    /**
     * Sends a request with the specified $dyn variables to emailvision's system,
     * that will send a generated email to the $recipient.
     *
     * @param string   $template
     * @param string   $recipient
     * @param array    $dyn
     * @param array    $content
     * @param DateTime $date
     * @param int      $notificationId
     *
     * @throws Exception | \InvalidArgumentException
     */
    public function sendEmail($template, $recipient, array $dyn = array(), array $content = array(), \DateTime $date = null, $notificationId = 0)
    {
        try {
            if (!isset($this->emailvisionConfig[$template])) {
                throw new InvalidArgumentException(sprintf(self::ERROR_UNKNOWN_TEMPLATE, $template));
            }

            $date               = $date instanceof \DateTime ?:new \DateTime();
            $client             = new \SoapClient(self::WSDL_URL);
            $sendRequestObjects = $this->createSendRequestObject(
                $recipient,
                $this->emailvisionConfig[$template],
                $this->arrayToEntries($content),
                $this->arrayToEntries($dyn),
                $date,
                $notificationId
            );
            $result             = $client->sendObjectsWithFullStatus(array('arg0' => array($sendRequestObjects)));

            if ($result->return->element->responseStatus != self::SEND_REQUEST_SUCCESS_STATUS) {
                throw new Exception($result->return->element->result);
            }
        } catch (\SoapFault $e) {
            throw new Exception(
                sprintf(
                    self::ERROR_SERVER,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Create send request type as required on emailvision wsdl
     *
     * @param string   $recipient
     * @param array    $config
     * @param array    $content
     * @param array    $dyn
     * @param DateTime $date
     * @param int      $notificationId
     *
     * @return \stdClass
     */
    protected function createSendRequestObject($recipient, array $config, array $content, array $dyn, \DateTime $date, $notificationId)
    {
        $sendRequest                 = new \stdClass;
        $sendRequest->email          = $recipient;
        $sendRequest->encrypt        = $config['encrypt'];
        $sendRequest->random         = $config['random'];
        $sendRequest->senddate       = $date->format('c');
        $sendRequest->synchrotype    = $config['stype'];
        $sendRequest->uidkey         = $config['uidkey'];
        $sendRequest->notificationId = $notificationId;
        $sendRequest->content        = $content;
        $sendRequest->dyn            = $dyn;

        return $sendRequest;
    }

    /**
     * Convert array key=>val To look like Entry type sequence on emailvision wsdl
     *
     * @param $array
     *
     * @return array
     */
    protected function arrayToEntries(array $array)
    {
        $entries = array();

        foreach ($array as $key => $parameter) {
            $entries[] = array('key' => $key, 'value' => $parameter);
        }

        return $entries;
    }

    /**
     * Validates the configuration parameters required by Emailvision.
     *
     * @param array $config
     *
     * @return array
     * @throws \InvalidArgumentException
     * @throws Exception\Configuration
     */
    protected function validateConfiguration(array $config)
    {
        foreach ($config as $template => $templateConfig) {
            foreach ($this->mandatoryAttributes as $attribute) {
                if (!is_array($templateConfig) || !array_key_exists($attribute, $templateConfig)) {
                    throw new ConfigurationException($attribute, $template);
                }
            }

            $config[$template] = $templateConfig;
        }

        return $config;
    }
}