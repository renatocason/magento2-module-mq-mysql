<?php

namespace Rcason\MqMysql\Model\Queue;

use Magento\Framework\Model\AbstractModel;
use Rcason\MqMysql\Api\Data\QueueMessageInterface;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message as ResourceModel;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Message extends AbstractModel
    implements QueueMessageInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'ce_queue_message';
    
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
    
    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(QueueMessageInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($timestamp)
    {
        $this->setData(QueueMessageInterface::CREATED_AT, $timestamp);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(QueueMessageInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($timestamp)
    {
        $this->setData(QueueMessageInterface::UPDATED_AT, $timestamp);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(QueueMessageInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(QueueMessageInterface::STATUS, $status);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getRetries()
    {
        return $this->getData(QueueMessageInterface::RETRIES);
    }

    /**
     * @inheritdoc
     */
    public function setRetries($retries)
    {
        $this->setData(QueueMessageInterface::RETRIES, $retries);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getQueueName()
    {
        return $this->getData(QueueMessageInterface::QUEUE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setQueueName($queueName)
    {
        $this->setData(QueueMessageInterface::QUEUE_NAME, $queueName);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getMessageContent()
    {
        return $this->getData(QueueMessageInterface::MESSAGE_CONTENT);
    }

    /**
     * @inheritdoc
     */
    public function setMessageContent($messageContent)
    {
        $this->setData(QueueMessageInterface::MESSAGE_CONTENT, $messageContent);
        return $this;
    }
}
