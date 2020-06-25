<?php

 namespace Transom\Pakke\Controller\TrackShipment;


 use Magento\Framework\App\Action\Action;
 use Magento\Framework\App\Action\Context;
 use Magento\Framework\View\Result\PageFactory;
 use Magento\Sales\Api\Data\OrderInterface;
 use Transom\Pakke\Model\Connect;
 use \Transom\Pakke\Logger\Logger;
 use \Magento\Framework\Json\Helper\Data;

 class Info extends Action
  {
	protected $resultPageFactory;
	protected $orderInterface;
	protected $connect;
	protected $logger;
	protected $_jsonHelper;

	public function __construct(Context $context, PageFactory $pageFactory, OrderInterface $orderInterface, Connect $connect, Logger $logger, Data $jsonHelper)
	{
		
		$this->orderInterface= $orderInterface;
		$this->resultPageFactory = $pageFactory;
		$this->_jsonHelper = $jsonHelper;
		$this->connect = $connect;
		$this->logger = $logger;
		parent::__construct($context);
	}
	
	public function execute()
	{
		
	   $order_id = $this->getRequest()->getParam("order_id");
	   $order = $this->orderInterface->loadByIncrementId($order_id);
	 
	   $pakkeShipment = $order->getPakkeShipment();
	   $method = \Zend\Http\Request::METHOD_GET;

	   $url = "https://seller.pakke.mx/api/v1/Shipments/".$pakkeShipment."/history";
	   $response = $this->connect->getConnection($url, $method);

	   $url2 = "https://seller.pakke.mx/api/v1/Shipments/".$pakkeShipment."/";
       $response2 = $this->connect->getConnection($url2, $method);
		
		$resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        $block = $resultPage->getLayout()
                ->getBlock('tracking_info')
				->setShipmentHistory($response)
				->setShipmentStatus($response2)
				->toHtml();
		$this->getResponse()->setBody($block);
	}
}