<?php

namespace Namshi\Emailvision\Test;

use \PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    
    public function setUp()
    {
        $this->config = array('sample_email_template' => array(
            'random'            => 'iTag',
            'encrypt'           => 'sTag',
            'senddate'          => new \DateTime('2012-01-01 12:12:12'),
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
    
    public function testThatTheTargetUrlIsCorrect()
    {
        $url = "http://api.notificationmessaging.com/NMSREST?random=iTag&encrypt=sTag&senddate=2012-01-01%2012%3A12%3A12&uidkey=uKey&stype=stype&email=alessandro.nadalin%40gmail.com";
        $config = array('sample_email_template' => array(
            'random'            => 'iTag',
            'encrypt'           => 'sTag',
            'senddate'          => new \DateTime('2012-01-01 12:12:12'),
            'uidkey'            => 'uKey',
            'stype'             => 'stype',
        ));
        
        $client = new StubEmailVisionClient($config);        
        
        $this->assertEquals($url, $client->sendEmail('sample_email_template', 'alessandro.nadalin@gmail.com'));
    }
    
    public function testCreationOfTheFinalEmailVisionUrl()
    {
        $url = "http://api.notificationmessaging.com/NMSREST?random=iTag&encrypt=sTag&senddate=2012-01-01%2012%3A12%3A12&uidkey=uKey&stype=stype&email=email%40email.com";
        $client = new StubEmailVisionClient($this->config);
        
        $this->assertEquals($url, $client->sendEmail('sample_email_template', 'email@email.com'));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSendingAnInvalidEmailTemplateThrowAnException()
    {
        $url = "http://api.notificationmessaging.com/NMSREST?random=iTag&encrypt=sTag&senddate=2012-01-01%2012%3A12%3A12&uidkey=uKey&stype=stype&email=email%40email.com";
        $client = new StubEmailVisionClient($this->config);
        
        $this->assertEquals($url, $client->sendEmail('xxx', 'email@email.com'));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThesenddateParameterNeedsToBeADateTimeObject()
    {
        $this->config['sample_email_template']['senddate'] = 'sss';
        $client = new StubEmailVisionClient($this->config);
    }
    
    public function testPassingDynamicVariablesToEmailvision()
    {
        $url = "http://api.notificationmessaging.com/NMSREST?random=iTag&encrypt=sTag&senddate=2012-01-01%2012%3A12%3A12&uidkey=uKey&stype=stype&email=email%40email.com&dyn=var%3A1%7Cvar2%3A2";
        $client = new StubEmailVisionClient($this->config);
        
        $this->assertEquals($url, $client->sendEmail('sample_email_template', 'email@email.com', array('var' => 1, 'var2' => 2)));
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

            $res = $client->sendEmail('sample_email_template', $email, array(
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

            $res = $client->sendEmail('sample_email_template', 'a');
        } else {
            $this->markTestSkipped();
        }
    }
}

class StubEmailVisionClient extends \Namshi\Emailvision\Client
{
    public function send($request)
    {
        return $request->getUrl();
    }
}