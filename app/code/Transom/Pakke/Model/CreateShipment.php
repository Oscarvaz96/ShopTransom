<?php
namespace Transom\Pakke\Model;

use \Magento\Sales\Model\Order\Shipment\TrackFactory;
use \Magento\Sales\Api\Data\ShipmentCommentCreationInterface;
use \Magento\Sales\Model\ShipOrder;
use \Magento\Sales\Model\Convert\Order;
use Transom\Pakke\Logger\Logger;
use \Magento\Sales\Api\Data\OrderInterface;
use \Magento\Framework\Json\Helper\Data;

/**
 * Custom shipping model
 */
class CreateShipment
{
    protected $trackingFactory;
    protected $commentInterface;
    protected $shipOrderService;
    protected $orderConverter;
    protected $logger;
    protected $orderInterface;
    protected $_jsonHelper;

    public function __construct(
       TrackFactory $trackingFactory,
       ShipmentCommentCreationInterface $commentInterface,
       ShipOrder $shipOrderService,
       Order $orderConverter,
       Logger $logger,
       OrderInterface $orderInterface,
       Data $jsonHelper
    ) {
       $this->trackingFactory = $trackingFactory;
       $this->commentInterface = $commentInterface;
       $this->shipOrderService = $shipOrderService;
       $this->orderConverter = $orderConverter;
       $this->logger = $logger;
       $this->orderInterface = $orderInterface;
       $this->_jsonHelper = $jsonHelper;
    }

    protected function setTrackingData($trackingNumber,$carrierCode)
    {
        $track = $this->trackingFactory->create();
        $track->setTrackNumber($trackingNumber);
        $track->setCarrierCode('Custom Value');
        $track->setTitle($carrierCode);
        $trackInfo[] = $track;

        return $trackInfo;
    }

    protected function setShipmentComment($comment)
    {
        $comment = !empty($comment) ? $comment : 'Not Available';
        return $this->commentInterface->setComment($comment);
    }

    public function createShipment($orderId,array $items, $trackingNumber, $carrierCode)
    {
        $this->logger->info("Order ID: ".$orderId->getIncrementId());
        $order = $this->orderInterface->loadByIncrementId($orderId->getIncrementId());
        $comment = "";
        $notify = true;
        $includeComment =true;
         
            try {
                $orderId = $order->getId();
                $tracks = $this->setTrackingData($trackingNumber,$carrierCode);
                $comment = $this->setShipmentComment($comment);
                $shippedItems = $this->createShipmentItems($items,$order);
                
                //creates shipment 
                $shipmentId = $this->shipOrderService->execute($orderId,
                    $shippedItems,
                    $notify,
                    $includeComment,
                    $comment,
                    $tracks);
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }

        return true;
    }

    protected function createShipmentItems(array $items, $order)
    {
        $shipmentItem = [];
        foreach ($order->getAllItems() as $orderItem) {
            if (array_key_exists($orderItem->getId(), $items)) {
                $shipmentItem[] = $this->orderConverter
                    ->itemToShipmentItem($orderItem)
                    ->setQty($items[$orderItem->getId()]);
            }
        } 

        return $shipmentItem;
    }
}
