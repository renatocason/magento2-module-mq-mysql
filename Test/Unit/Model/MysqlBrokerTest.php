<?php

namespace Rcason\MqMysql\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Rcason\Mq\Api\BrokerInterface;
use Rcason\Mq\Api\Data\MessageEnvelopeInterfaceFactory;
use Rcason\Mq\Model\MessageEnvelope;
use Rcason\MqMysql\Api\Data\QueueMessageInterfaceFactory;
use Rcason\MqMysql\Api\QueueMessageRepositoryInterface;
use Rcason\MqMysql\Model\Queue\Message;
use Rcason\MqMysql\Model\MysqlBroker;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class MysqlBrokerTest extends \PHPUnit\Framework\TestCase
{
    const QUEUE_NAME = 'test_queue';
    const MESSAGE_ID = 294;
    const MESSAGE_CONTENT = 'Message content';
    
    /**
     * @var QueueMessageInterfaceFactory|MockObject
     */
    private $queueMessageFactory;
    
    /**
     * @var MessageEnvelopeInterfaceFactory|MockObject
     */
    private $messageEnvelopeFactory;
    
    /**
     * @var QueueMessageRepositoryInterface|MockObject
     */
    private $queueMessageRepository;
    
    /**
     * @var MessageEnvelope
     */
    private $messageEnvelope;
    
    /**
     * @var Message
     */
    private $message;
    
    /**
     * @var MysqlBroker
     */
    private $mysqlBroker;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        
        $this->queueMessageFactory = $this->getMockBuilder(QueueMessageInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        
        $this->messageEnvelopeFactory = $this->getMockBuilder(MessageEnvelopeInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        
        $this->queueMessageRepository = $this->getMockForAbstractClass(QueueMessageRepositoryInterface::class);
        $this->messageEnvelope = $objectManager->getObject(MessageEnvelope::class);
        $this->message = $objectManager->getObject(Message::class);
        
        $this->mysqlBroker = $objectManager->getObject(MysqlBroker::class, [
            'queueMessageFactory' => $this->queueMessageFactory,
            'messageEnvelopeFactory' => $this->messageEnvelopeFactory,
            'queueMessageRepository' => $this->queueMessageRepository,
            'queueName' => self::QUEUE_NAME,
        ]);
        
        parent::setUp();
    }
    
    public function testServiceContract()
    {
        $this->assertInstanceOf(BrokerInterface::class, $this->mysqlBroker);
    }

    /**
     * @covers Rcason\MqMysql\Model\MysqlBroker::enqueue
     */
    public function testEnqueue()
    {
        $result = true;
        
        $this->queueMessageFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->message);
        
        $this->queueMessageRepository->expects($this->once())
            ->method('create')
            ->with($this->message)
            ->willReturn($result);
        
        $this->messageEnvelope->setContent(self::MESSAGE_CONTENT);
        
        $this->assertEquals(
            $this->mysqlBroker->enqueue($this->messageEnvelope),
            $result
        );
        
        $this->assertEquals(
            $this->message->getQueueName(),
            self::QUEUE_NAME
        );
        
        $this->assertEquals(
            $this->message->getMessageContent(),
            $this->messageEnvelope->getContent()
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\MysqlBroker::peek
     */
    public function testPeek()
    {
        $this->message->setId(self::MESSAGE_ID);
        $this->message->setMessageContent(self::MESSAGE_CONTENT);
        
        $this->queueMessageRepository->expects($this->once())
            ->method('peek')
            ->willReturn($this->message);
        
        $this->messageEnvelopeFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->messageEnvelope);
        
        $this->assertEquals(
            $this->mysqlBroker->peek(),
            $this->messageEnvelope
        );
        
        $this->assertEquals(
            $this->messageEnvelope->getBrokerRef(),
            self::MESSAGE_ID
        );
        
        $this->assertEquals(
            $this->messageEnvelope->getContent(),
            self::MESSAGE_CONTENT
        );
    }
    
    /**
     * @covers Rcason\MqMysql\Model\MysqlBroker::peek
     */
    public function testPeekEmptyQueue()
    {
        $this->queueMessageRepository->expects($this->once())
            ->method('peek')
            ->willReturn(null);
        
        $this->assertEquals(
            $this->mysqlBroker->peek(),
            false
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\MysqlBroker::acknowledge
     */
    public function testAcknowledge()
    {
        $this->queueMessageRepository->expects($this->once())
            ->method('get')
            ->with(self::MESSAGE_ID)
            ->willReturn($this->message);
        
        $this->queueMessageRepository->expects($this->once())
            ->method('remove')
            ->with($this->message);
        
        $this->messageEnvelope->setBrokerRef(self::MESSAGE_ID);
        $this->mysqlBroker->acknowledge($this->messageEnvelope);
    }

    /**
     * @covers Rcason\MqMysql\Model\MysqlBroker::reject
     */
    public function testReject()
    {
        $this->queueMessageRepository->expects($this->once())
            ->method('get')
            ->with(self::MESSAGE_ID)
            ->willReturn($this->message);
        
        $this->queueMessageRepository->expects($this->once())
            ->method('remove')
            ->with($this->message);
        
        $this->messageEnvelope->setBrokerRef(self::MESSAGE_ID);
        $this->mysqlBroker->reject($this->messageEnvelope, false);
    }
    
    /**
     * @covers Rcason\MqMysql\Model\MysqlBroker::reject
     */
    public function testRejectRequeue()
    {
        $this->queueMessageRepository->expects($this->once())
            ->method('get')
            ->with(self::MESSAGE_ID)
            ->willReturn($this->message);
        
        $this->queueMessageRepository->expects($this->once())
            ->method('requeue')
            ->with($this->message);
        
        $this->messageEnvelope->setBrokerRef(self::MESSAGE_ID);
        $this->mysqlBroker->reject($this->messageEnvelope, true);
    }
}
