<?php

namespace Rcason\MqMysql\Model\ResourceModel\Queue;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Message extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ce_queue_message', 'entity_id');
    }
}
