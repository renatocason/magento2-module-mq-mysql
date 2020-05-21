<?php
namespace Rcason\MqMysql\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.1.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ce_queue_message'),
                'run_task_at',
                [
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Do not run task before this date and time'
                ]
            );
        }

        $setup->endSetup();
    }
}
