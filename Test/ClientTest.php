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
            'senddate'          => '2012-01-01',
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
        $url = "http://api.notificationmessaging.com/NMSREST?random=iTag&encrypt=sTag&senddate=2012-01-01&uidkey=uKey&stype=stype";
        // &dyn={syncKey: value|field:value|field:value}
        
        $this->assertEquals($url, $this->client->createRequest()->getUrl());
    }
    
    public function testCreationOfTheFinalEmailVisionUrl()
    {
        $url = "http://api.notificationmessaging.com/NMSREST?random=iTag&encrypt=sTag&senddate=2012-01-01&uidkey=uKey&stype=stype&email=email%40email.com";
        $client = new StubEmailVisionClient($this->config);
        
        $this->assertEquals($url, $client->sendEmail('email@email.com'));
    }
    
    public function testPassingDynamicVariablesToEmailvision()
    {
        $this->markTestIncomplete("Test to be implemented");
    }
}

class StubEmailVisionClient extends \Namshi\Emailvision\Client
{
    public function send($request)
    {
        return $request->getUrl();
    }
}