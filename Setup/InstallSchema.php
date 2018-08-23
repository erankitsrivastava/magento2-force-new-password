<?php

namespace Godogi\Forcenewpassword\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Get godogi_forcenewpassword table
        $tableName = $installer->getTable('godogi_forcenewpassword');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
            			'entity_id',
            			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            			null,
            			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            			'Entity Id'
        			)->addColumn(
            			'customer_id',
           			 	\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            			null,
            			[],
            			'Customer ID'
        			)->addColumn(
            			'password_updated',
            			\Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            			null,
            			[],
            			'Password Updated ?'
        			)->setComment('ForceNewPassword Table')
        			->setOption('type', 'InnoDB')
              	->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
