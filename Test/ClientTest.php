<?php

namespace Namshi\Emailvision\Test;

use \PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    
    public function setUp()
    {
        $this->config = array(
            'random'            => 'iTag',
            'encrypt'           => 'sTag',
            'senddate'          => new \DateTime('2012-01-01 12:12:12'),
            'uidkey'            => 'uKey',
            'stype'             => 'stype',
        );
        
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
        new \Namshi\Emailvision\Client(array());
    }
    
    public function testThatTheTargetUrlIsCorrect()
    {
        $url = "http://api.notificationmessaging.com/NMSREST?random=iTag&encrypt=sTag&senddate=2012-01-01%2012%3A12%3A12&uidkey=uKey&stype=stype";
        // &dyn={syncKey: value|field:value|field:value}
        
        $this->assertEquals($url, $this->client->createRequest()->getUrl());
    }
    
    public function testCreationOfTheFinalEmailVisionUrl()
    {
        $url = "http://api.notificationmessaging.com/NMSREST?random=iTag&encrypt=sTag&senddate=2012-01-01%2012%3A12%3A12&uidkey=uKey&stype=stype&email=email%40email.com";
        $client = new StubEmailVisionClient($this->config);
        
        $this->assertEquals($url, $client->sendEmail('email@email.com'));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThesenddateParameterNeedsToBeADateTimeObject()
    {
        $this->config['senddate'] = 'sss';
        $client = new StubEmailVisionClient($this->config);
    }
    
    public function testPassingDynamicVariablesToEmailvision()
    {
        $this->markTestIncomplete("Test to be implemented");
    }
    
    public function testSendingItReal()
    {
        $realConfigFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'emailvision.config';
        
        if (file_exists($realConfigFile)) {
            require $realConfigFile;
            
            $client = new \Namshi\Emailvision\Client(array(
                'uidkey'    => 'EMAIL',
                'encrypt'   => $encrypt,
                'stype'     => 'NOTHING',
                'random'    => $random,
                'senddate'  => new \DateTime('2012-01-01 00:00:00   '),
            ));

            $res = $client->sendEmail($email);
        }
    }
    
    /**
     * @expectedException Namshi\Emailvision\Exception
     */
    public function testSendingItRealHasAnError()
    {
        $realConfigFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'emailvision.config';
        
        if (file_exists($realConfigFile)) {
            require $realConfigFile;
            
            $client = new \Namshi\Emailvision\Client(array(
                'uidkey'    => 'EMAIL',
                'encrypt'   => $encrypt,
                'stype'     => 'NOTHING',
                'random'    => $random,
                'senddate'  => new \DateTime('2012-01-01 00:00:00   '),
            ));

            $res = $client->sendEmail('a');
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