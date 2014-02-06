<?php

namespace Namshi\Emailvision\Test;

use Guzzle\Http\Message\Request;
use \PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    
    public function setUp()
    {
        $this->config = array('sample_email_template' => array(
            'random'            => 'iTag',
            'encrypt'           => 'sTag',
            'uidkey'            => 'uKey',
            'stype'             => 'stype',
        ));
        
        $this->client = new \Namshi\Emailvision\Client($this->config);
    }
    
    public function testCreationOfTheClient()
    {
        $this->assertInstanceOf("Namshi\\Emailvision\Client", $this->client);
    }
    
    /**
     * @expectedException Namshi\Emailvision\Exception\Configuration
     */
    public function testValidatingTheConfiguration()
    {
        new \Namshi\Emailvision\Client(array('helllo'));
    }
    
    public function testYouCanInstantiateAClientWithoutSendDate()
    {
        $this->config = array('sample_email_template' => array(
            'random'            => 'iTag',
            'encrypt'           => 'sTag',
            'uidkey'            => 'uKey',
            'stype'             => 'stype',
        ));

        $this->client = new \Namshi\Emailvision\Client($this->config);
    }
    
    public function testSendingItReal()
    {
        $realConfigFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'emailvision.config';

        if (file_exists($realConfigFile)) {
            require $realConfigFile;
            
            $client = new \Namshi\Emailvision\Client(array('sample_email_template' => array(
                'uidkey'    => 'EMAIL',
                'encrypt'   => $encrypt,
                'stype'     => 'NOTHING',
                'random'    => $random,
                'senddate'  => new \DateTime('2012-01-01 00:00:00   '),
            )));

            $client->sendEmail('sample_email_template', $email, array(
                'name'  => 'Hisham!',
                'var'   => 'This text comes directly from a unit test!  ',
            ));
        } else {
            $this->markTestSkipped();
        }
    }
    
    /**
     * @expectedException Namshi\Emailvision\Exception
     * 
     * The error comes since the email is invalid.
     */
    public function testSendingItRealWithoutAValidEmailHasAnError()
    {
        $realConfigFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'emailvision.config';

        if (file_exists($realConfigFile)) {
            require $realConfigFile;
            
            $client = new \Namshi\Emailvision\Client(array('sample_email_template' => array(
                'uidkey'    => 'EMAIL',
                'encrypt'   => $encrypt,
                'stype'     => 'NOTHING',
                'random'    => $random,
                'senddate'  => new \DateTime('2012-01-01 00:00:00   '),
            )));

            $client->sendEmail('sample_email_template', 'a');
        } else {
            $this->markTestSkipped();
        }
    }
}