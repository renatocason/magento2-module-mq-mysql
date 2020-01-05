<?php

namespace Rcason\MqMysql\Test\Unit\Model\Queue;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Rcason\MqMysql\Api\Data\QueueMessageInterface;
use Rcason\MqMysql\Api\Data\QueueMessageInterfaceFactory;
use Rcason\MqMysql\Api\QueueMessageRepositoryInterface;
use Rcason\MqMysql\Model\Queue\Message;
use Rcason\MqMysql\Model\Queue\MessageRepository;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message as ResourceModel;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message\Collection;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message\CollectionFactory;

class MessageRepositoryTest extends \PHPUnit\Framework\TestCase
{
    const QUEUE_NAME = 'test';
    const MAX_RETRIES = 5;
    const MESSAGE_ID = 7;
    
    /**
     * @var QueueMessageInterfaceFactory|MockObject
     */
    private $queueMessageFactory;
    
    /**
     * @var ResourceModel|MockObject
     */
    private $resourceModel;
    
    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactory;
    
    /**
     * @var Message
     */
    private $message;
    
    /**
     * @var MessageRepository
     */
    private $messageRepository;

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
        
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        
        $this->resourceModel = $this->createMock(ResourceModel::class);
        $this->collection = $this->createMock(Collection::class);
        
        $this->message = $objectManager->getObject(Message::class);
        $this->messageRepository = $objectManager->getObject(MessageRepository::class, [
            'queueMessageFactory' => $this->queueMessageFactory,
            'resourceModel' => $this->resourceModel,
            'collectionFactory' => $this->collectionFactory,
            'maxRetries' => self::MAX_RETRIES,
        ]);
        
        parent::setUp();
    }
    
    public function testServiceContract()
    {
        $this->assertInstanceOf(QueueMessageRepositoryInterface::class, $this->messageRepository);
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\MessageRepository::create
     */
    public function testCreate()
    {
        $this->resourceModel->expects($this->once())
            ->method('save')
            ->with($this->message);
        
        $this->messageRepository->create($this->message);
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\MessageRepository::peek
     */
    public function testPeek()
    {
        $collectionMethods = [
            'addFieldToFilter',
            'setOrder',
            'setCurPage',
            'setPageSize',
        ];
        
        foreach ($collectionMethods as $method) {
            $this->collection->expects($this->any())
                ->method($method)
                ->willReturn($this->collection);
        }
        
        $this->collection->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($this->message);
        
        $this->collectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->collection);
        
        $this->assertEquals(
            $this->messageRepository->peek(self::QUEUE_NAME),
            $this->message
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\MessageRepository::get
     */
    public function testGet()
    {
        $this->queueMessageFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->message);
        
        $this->resourceModel->expects($this->once())
            ->method('load')
            ->with($this->message, self::MESSAGE_ID);
        
        $this->message->setId(self::MESSAGE_ID);
        
        $this->assertEquals(
            $this->messageRepository->get(self::MESSAGE_ID),
            $this->message
        );
    }
    
    /**
     * @covers Rcason\MqMysql\Model\Queue\MessageRepository::get
     * @expectedException \Magento\Framework\Exception\NotFoundException
     */
    public function testGetInvalid()
    {
        $this->messageRepository->get(0);
    }
    
    /**
     * @covers Rcason\MqMysql\Model\Queue\MessageRepository::get
     * @expectedException \Magento\Framework\Exception\NotFoundException
     */
    public function testGetNotFound()
    {
        $this->queueMessageFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->message);
        
        $this->resourceModel->expects($this->once())
            ->method('load')
            ->with($this->message, self::MESSAGE_ID);
        
        $this->messageRepository->get(self::MESSAGE_ID);
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\MessageRepository::requeue
     */
    public function testRequeue()
    {
        $retries = self::MAX_RETRIES - 2;
        
        $this->message->setStatus(QueueMessageInterface::STATUS_TO_PROCESS);
        $this->message->setRetries($retries);
        
        $this->messageRepository->requeue($this->message);
        
        $this->assertEquals(
            $this->message->getRetries(),
            $retries + 1
        );
        
        $this->assertEquals(
            $this->message->getStatus(),
            QueueMessageInterface::STATUS_TO_PROCESS
        );
    }
    
    /**
     * @covers Rcason\MqMysql\Model\Queue\MessageRepository::requeue
     */
    public function testRequeueLimit()
    {
        $retries = self::MAX_RETRIES;
        
        $this->message->setStatus(QueueMessageInterface::STATUS_TO_PROCESS);
        $this->message->setRetries($retries);
        
        $this->messageRepository->requeue($this->message);
        
        $this->assertEquals(
            $this->message->getRetries(),
            $retries + 1
        );
        
        $this->assertEquals(
            $this->message->getStatus(),
            QueueMessageInterface::STATUS_MAX_RETRIES_EXCEEDED
        );
    }

    /**
     * @covers Rcason\MqMysql\Model\Queue\MessageRepository::remove
     */
    public function testRemove()
    {
        $this->resourceModel->expects($this->once())
            ->method('delete')
            ->with($this->message);
        
        $this->messageRepository->remove($this->message);
    }
}
