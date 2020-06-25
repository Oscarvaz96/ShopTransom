<?php

namespace Transom\Pakke\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();


        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'pakke_shipment',
            [
                'type' => 'text',
                'nullable' => true,
                'comment' => 'Pakke Shipment Id',
            ]
        );


        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'pakke_shipment',
            [
                'type' => 'text',
                'nullable' => true,
                'comment' => 'Pakke Shipment Id',
            ]
        );

        
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'pakke_label',
            [
                'type' => 'text',
                'nullable' => true,
                'comment' => 'Pakke Shipment Label',
            ]
        );


        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'pakke_label',
            [
                'type' => 'text',
                'nullable' => true,
                'comment' => 'Pakke Shipment Label',
            ]
        );


        $setup->endSetup();
    }
}
