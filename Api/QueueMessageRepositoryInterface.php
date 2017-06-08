<?php

namespace Rcason\MqMysql\Api;

use Rcason\MqMysql\Api\Data\QueueMessageInterface;

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
     * @return \Rcason\MqMysql\Api\Data\QueueMessageInterface
     */
    public function peek();
    
    /**
     * Get message by id
     * 
     * @return \Rcason\MqMysql\Api\Data\QueueMessageInterface
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
