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
     * @param QueueMessageInterfaceFactory $queueMessageFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        QueueMessageInterfaceFactory $queueMessageFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->queueMessageFactory = $queueMessageFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
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
            ->addFieldToFilter(
                ['run_task_at', 'run_task_at'],
                [
                    ['null' => true],
                    ['lteq' => date($this->getTimestampFormat(), time())]
                ]
            )
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
    public function requeue(QueueMessageInterface $message, int $maxRetries, int $retryInterval)
    {
        // Trigger date update
        $message->setUpdatedAt(null);
        if (empty($retryInterval)) {
            $message->setRunTaskAt(null);
        } else {
            $message->setRunTaskAt(date($this->getTimestampFormat(), time() + $retryInterval));
        }
        
        // Increase retries count
        $message->setRetries($message->getRetries() + 1);
        if($maxRetries > 0 && $message->getRetries() >= $maxRetries) {
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

    /**
     * @return string
     */
    protected function getTimestampFormat()
    {
        return 'Y-m-d H:i:s';
    }
}
