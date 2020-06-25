<?php
namespace Transom\Pakke\Plugin\Order;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Transom\Pakke\Logger\Logger;
use Transom\Pakke\Helper\ConfigFunctions;

class View
{
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $logger;
    protected $configFunctions;

    public function __construct(
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Logger $logger,
        ConfigFunctions $configFunctions
    ){
    $this->orderRepository = $orderRepository;
    $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    $this->logger = $logger;
    $this->configFunctions = $configFunctions;
    }

    public function beforeSetLayout(OrderView $subject)
    {
        $order_id = $subject->getOrderId();
        $order = $this->orderRepository->get($order_id);
        $pakkeShipment = $order->getPakkeShipment();
        $pakkeLabel = $order->getPakkeLabel();
        $orderIncrementId = $order->getIncrementId();
        
        if($this->configFunctions->getLogger() == true){
            $this->logger->info("OrderId: ".$order_id);
            $this->logger->info("PAKKE SHIPMENT FROM PLUGIN: ".$pakkeShipment);
            $this->logger->info("PAKKE LABEL FROM PLUGIN: ".$pakkeLabel);
            $this->logger->info("Increment ID: ".$orderIncrementId);
        }
        
        $subject->addButton(
            'order_custom_button',
            [
                'label' => __("Descargar Etiqueta"),
                'class' => __('custom-button'),
                'id' => 'order-view-custom-button',
                'onclick' => "setLocation('/ShopTransom/admin_1evvgb/testpakke/download/pdf?key=$pakkeLabel&name=$orderIncrementId')"

            ]
        );
    }
}       