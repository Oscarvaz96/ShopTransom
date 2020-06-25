<?php

namespace Transom\Pakke\Plugin;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
/**
 * Class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
    /**
     * Order feedback field name
     */
    const FIELD_NAME = 'pakke_shipment';
    const FIELD_NAME2 = 'pakke_label';
    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;
    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }
    /**
     * Add "pakke_shipment" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $pakkeShipment = $order->getData(self::FIELD_NAME);
        $pakkeLabel = $order->getData(self::FIELD_NAME2);
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $extensionAttributes->setPakkeShipment($pakkeShipment);
        $extensionAttributes->setPakkeLabel($pakkeLabel);
        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }
    /**
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();
        foreach ($orders as &$order) {
            $pakkeShipment = $order->getData(self::FIELD_NAME);
            $pakkeLabel = $order->getData(self::FIELD_NAME2);
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $extensionAttributes->setPakkeShipment($pakkeShipment);
            $extensionAttributes->setPakkeLabel($pakkeLabel);
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $searchResult;
    }
}