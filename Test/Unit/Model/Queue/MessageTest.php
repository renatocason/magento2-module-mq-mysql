<?php

namespace Rcason\MqMysql\Test\Unit\Model\Queue;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Rcason\MqMysql\Api\Data\QueueMessageInterface;
use Rcason\MqMysql\Model\Queue\Message;

class MessageTest extends \PHPUnit\Framework\TestCase
{
    const VALUE_CREATED_AT = '2018-01-22 10:08:01';
    const VALUE_UPDATED_AT = '2018-01-22 11:03:07';
    const VALUE_STATUS = 1;
    const VALUE_RETRIES = 7;
    const VALUE_QUEUE_NAME = 'QueueName';
    const VALUE_MESSAGE_CONTENT = 'Test message body';
    
    /**
     * @var Message
     */
    private $message;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->message = $objectManager->getObject(Message::class);
        
        parent::setUp();
    }
    
    public function testServiceContract()
    {
        $this->assertInstanceOf(QueueMessageInterface::class, $this->message);
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\Message::setCreatedAt
     * @covers Rcason\MqMysql\Model\Queue\Message::getCreatedAt
     */
    public function testCreatedAt()
    {
        $this->message->setCreatedAt(self::VALUE_CREATED_AT);
        $this->assertEquals(
            $this->message->getCreatedAt(),
            self::VALUE_CREATED_AT
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\Message::setUpdatedAt
     * @covers Rcason\MqMysql\Model\Queue\Message::getUpdatedAt
     */
    public function testUpdatedAt()
    {
        $this->message->setUpdatedAt(self::VALUE_UPDATED_AT);
        $this->assertEquals(
            $this->message->getUpdatedAt(),
            self::VALUE_UPDATED_AT
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\Message::setStatus
     * @covers Rcason\MqMysql\Model\Queue\Message::getStatus
     */
    public function testStatus()
    {
        $this->message->setStatus(self::VALUE_STATUS);
        $this->assertEquals(
            $this->message->getStatus(),
            self::VALUE_STATUS
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\Message::setRetries
     * @covers Rcason\MqMysql\Model\Queue\Message::getRetries
     */
    public function testRetries()
    {
        $this->message->setRetries(self::VALUE_RETRIES);
        $this->assertEquals(
            $this->message->getRetries(),
            self::VALUE_RETRIES
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\Message::setQueueName
     * @covers Rcason\MqMysql\Model\Queue\Message::getQueueName
     */
    public function testQueueName()
    {
        $this->message->setQueueName(self::VALUE_QUEUE_NAME);
        $this->assertEquals(
            $this->message->getQueueName(),
            self::VALUE_QUEUE_NAME
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\Message::setMessageContent
     * @covers Rcason\MqMysql\Model\Queue\Message::getMessageContent
     */
    public function testMessageContent()
    {
        $this->message->setMessageContent(self::VALUE_MESSAGE_CONTENT);
        $this->assertEquals(
            $this->message->getMessageContent(),
            self::VALUE_MESSAGE_CONTENT
        );
    }
}
