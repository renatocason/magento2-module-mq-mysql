<?php

namespace Rcason\MqMysql\Model;

use Rcason\Mq\Api\Data\MessageEnvelopeInterface;
use Rcason\Mq\Api\Data\MessageEnvelopeInterfaceFactory;
use Rcason\MqMysql\Api\Data\QueueMessageInterface;
use Rcason\MqMysql\Api\Data\QueueMessageInterfaceFactory;
use Rcason\MqMysql\Api\QueueMessageRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class MysqlBroker implements \Rcason\Mq\Api\BrokerInterface
{
    /**
     * @var QueueMessageInterfaceFactory
     */
    protected $queueMessageFactory;
    
    /**
     * @var MessageEnvelopeInterfaceFactory
     */
    protected $messageEnvelopeFactory;
    
    /**
     * @var QueueMessageRepositoryInterface
     */
    protected $queueMessageRepository;
    
    /**
     * @var string
     */
    protected $queueName;
    
    /**
     * @param QueueMessageInterfaceFactory $queueMessageFactory
     * @param MessageEnvelopeInterfaceFactory $messageEnvelopeFactory
     * @param QueueMessageRepositoryInterface $queueMessageRepository
     */
    public function __construct(
        QueueMessageInterfaceFactory $queueMessageFactory,
        MessageEnvelopeInterfaceFactory $messageEnvelopeFactory,
        QueueMessageRepositoryInterface $queueMessageRepository,
        $queueName = null
    ) {
        $this->queueMessageFactory = $queueMessageFactory;
        $this->messageEnvelopeFactory = $messageEnvelopeFactory;
        $this->queueMessageRepository = $queueMessageRepository;
        $this->queueName = $queueName;
    }
    
    /**
     * @inheritdoc
     */
    public function enqueue(MessageEnvelopeInterface $messageEnvelope)
    {
        $queueMessage = $this->queueMessageFactory->create()
            ->setQueueName($this->queueName)
            ->setMessageContent($messageEnvelope->getContent());
        
        return $this->queueMessageRepository->create($queueMessage);
    }
    
    /**
     * @inheritdoc
     */
    public function peek()
    {
        $queueMessage = $this->queueMessageRepository->peek($this->queueName);
        if(!$queueMessage || !$queueMessage->getId()) {
            return false;
        }
        
        return $this->messageEnvelopeFactory->create()
            ->setBrokerRef($queueMessage->getId())
            ->setContent($queueMessage->getMessageContent());
    }
    
    /**
     * @inheritdoc
     */
    public function acknowledge(MessageEnvelopeInterface $message)
    {
        $message = $this->queueMessageRepository->get($message->getBrokerRef());
        $this->queueMessageRepository->remove($message);
    }
    
    /**
     * @inheritdoc
     */
    public function reject(MessageEnvelopeInterface $message, bool $requeue)
    {
        $message = $this->queueMessageRepository->get($message->getBrokerRef());
        
        if($requeue) {
            $this->queueMessageRepository->requeue($message);
            return;
        }
        $this->queueMessageRepository->remove($message);
    }
}
