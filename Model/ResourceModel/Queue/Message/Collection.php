<?php

namespace Rcason\MqMysql\Model\ResourceModel\Queue\Message;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Rcason\MqMysql\Model\Queue\Message;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message as ResourceModel;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collection extends AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Message::class, ResourceModel::class);
    }
}
