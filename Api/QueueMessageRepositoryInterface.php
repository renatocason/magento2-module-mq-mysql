<?php

namespace Rcason\MqMysql\Api;

use Magento\Framework\Exception\NotFoundException;
use Rcason\MqMysql\Api\Data\QueueMessageInterface;

/**
 * @api
 * 
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface QueueMessageRepositoryInterface
{
    /**
     * Create new queue item
     * 
     * @return void
     */
    public function create(QueueMessageInterface $message);
    
    /**
     * Take first element in the queue without removing it
     * 
     * @param string $queueName
     * @return \Rcason\MqMysql\Api\Data\QueueMessageInterface
     */
    public function peek(string $queueName);
    
    /**
     * Get message by id
     * 
     * @return \Rcason\MqMysql\Api\Data\QueueMessageInterface
     * @throws NotFoundException
     */
    public function get($id);
    
    /**
     * Requeue the message, increasing the retries count
     * 
     * @return void
     */
    public function requeue(QueueMessageInterface $message);
    
    /**
     * Remove message from queue
     * 
     * @return void
     */
    public function remove(QueueMessageInterface $message);
}
