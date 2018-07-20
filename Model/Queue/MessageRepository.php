<?php

namespace Rcason\MqMysql\Model\Queue;

use Magento\Framework\Exception\NotFoundException;
use Rcason\MqMysql\Api\Data\QueueMessageInterface;
use Rcason\MqMysql\Api\Data\QueueMessageInterfaceFactory;
use Rcason\MqMysql\Api\QueueMessageRepositoryInterface;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message as ResourceModel;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class MessageRepository implements QueueMessageRepositoryInterface
{
    /**
     * @var QueueMessageInterfaceFactory
     */
    protected $queueMessageFactory;
    
    /**
     * @var ResourceModel
     */
    protected $resourceModel;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var int
     */
    protected $maxRetries;
    
    /**
     * @param QueueMessageInterfaceFactory $queueMessageFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param int $maxRetries
     */
    public function __construct(
        QueueMessageInterfaceFactory $queueMessageFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory,
        $maxRetries = 5
    ) {
        $this->queueMessageFactory = $queueMessageFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->maxRetries = $maxRetries;
    }
    
    /**
     * @inheritdoc
     */
    public function create(QueueMessageInterface $message)
    {
        $this->resourceModel->save($message);
    }
    
    /**
     * @inheritdoc
     */
    public function peek(string $queueName)
    {
        // Create collection instance and apply filter
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', 0)
            ->addFieldToFilter('queue_name', $queueName)
            ->setOrder('updated_at', 'ASC')
            ->setCurPage(1)
            ->setPageSize(1);
        
        return $collection->getFirstItem();
    }
    
    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if(!$id) {
            throw new NotFoundException(__('No id specified in queue message get'));
        }
        
        $queueMessage = $this->queueMessageFactory->create();
        $this->resourceModel->load($queueMessage, $id);

        if($id != $queueMessage->getId()) {
            throw new NotFoundException(__('Queue message not found'));
        }

        return $queueMessage;
    }
    
    /**
     * @inheritdoc
     */
    public function requeue(QueueMessageInterface $message)
    {
        // Trigger date update
        $message->setUpdatedAt(null);
        
        // Increase retries count
        $message->setRetries($message->getRetries() + 1);
        if($message->getRetries() >= $this->maxRetries) {
            $message->setStatus(QueueMessageInterface::STATUS_MAX_RETRIES_EXCEEDED);
        }
        
        $this->resourceModel->save($message);
    }
    
    /**
     * @inheritdoc
     */
    public function remove(QueueMessageInterface $message)
    {
        $this->resourceModel->delete($message);
    }
}
