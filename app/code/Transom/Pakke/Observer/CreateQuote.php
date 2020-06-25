<?php
namespace Transom\Pakke\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Transom\Pakke\Logger\Logger;
use  Transom\Pakke\Model\CreateShipment;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Json\Helper\Data;
use Transom\Pakke\Model\Connect;
use Zend\Http\Client;
use Transom\Pakke\Helper\ConfigFunctions;
use Transom\Pakke\Helper\Constants;

class CreateQuote implements ObserverInterface
{
    protected $logger;
    protected $scopeConfig;
    protected $_jsonHelper;
    protected $zendClient;
    protected $createShipment;
    protected $connect;
    protected $configFunctions;
    protected $_cart;
    protected $constants;

    public function __construct(Logger $logger, ScopeConfigInterface $scopeConfig, Data $jsonHelper, Client $httpClient, CreateShipment $createShipment, Connect $connect, ConfigFunctions $configFunctions, \Magento\Checkout\Model\Cart $cartModel, Constants $constants)
    {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->_jsonHelper = $jsonHelper;
        $this->zendClient = $httpClient;
        $this->createShipment = $createShipment;
        $this->connect = $connect;
        $this->configFunctions = $configFunctions;
        $this->_cart = $cartModel;
        $this->constants = $constants;
	}

    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        // $orderItems = $order->getAllItems();
        $orderItems = $order->getAllVisibleItems();
        $body = $this->getBody($order);
        $encodedBody =  $this->_jsonHelper->jsonEncode($body);

        if($this->configFunctions->getDebugger() == true){
            $this->logger->info("Pakke body: ".$encodedBody);
        }

        $trackingNumber = "00000";
        $carrierCode = "Empty";

        try {
                $url = $this->constants::URL_CREATE_SHIPMENT;
                $method = \Zend\Http\Request::METHOD_POST;
                $response = $this->connect->createConnection($url, $method, $body);
                $encodedResponse =  $this->_jsonHelper->jsonEncode($response);
                $decodedData = $this->_jsonHelper->jsonDecode($encodedResponse,true);

                if($this->configFunctions->getLogger() == true){
                    $this->logger->info("Pakke respuesta: ".$encodedResponse);
                }
                
                if(isset($decodedData["ShipmentId"])){
                    $order->setPakkeShipment($decodedData["ShipmentId"]);
                }

                if(isset($decodedData["CourierName"])){
                    $carrierCode = $decodedData["CourierName"];
                }

                if(isset($decodedData["Label"])){
                    $order->setPakkeLabel($decodedData["Label"]);
                }
            
                if(isset($decodedData["TrackingNumber"])){
                   $trackingNumber = $decodedData["TrackingNumber"];
                }
             
                $itemQty = array();
                foreach ($orderItems as $item) {
                    $itemQty[]=array('quantity'=>$item->getQyOrdered(),'description'=>$item->getDescription(),'name'=>$item->getName(),'price'=>$item->getPrice());
                }

                $shippingResponse = $this->createShipment->createShipment($order,$itemQty,$trackingNumber,$carrierCode);

		} catch (\Throwable $th) {
			print($th->getMessage());
		}
      
    }

    public function getBody($order){
        $orderItems = $order->getAllItems();
        $productDimensions = $this->configFunctions->getProductDimensions($orderItems,"2");
           
        //CourierCode and CourierServiceId
        $shippingMethod = $order->getShippingDescription();
        $shippingCode = substr($shippingMethod, 0, 3);
        $shippingMethodArr = explode(" ",$shippingMethod);
        $shippingServiceId = $shippingMethodArr[1];

        //Sender
        $SenderPhone = $this->scopeConfig->getValue('general/store_information/phone',ScopeInterface::SCOPE_STORE);
        $SenderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
       
        //Recipient
        $recipient_first_name = $order->getBillingAddress()->getFirstName();
        $recipient_last_name = $order->getBillingAddress()->getLastName();
        $recipient_name = $recipient_first_name." ".$recipient_last_name;
        $recipient_phone = $order->getBillingAddress()->getTelephone();
        $recipient_email = $order->getCustomerEmail();
        $recipient_company = $order->getBillingAddress()->getCompany();
        if($this->configFunctions->getDebugger() == true){
            $this->logger->info("RECEIPT COMPANY: ".$recipient_company);
        }

        //Address From
        $AddressFromZip = $this->scopeConfig->getValue('general/store_information/postcode',ScopeInterface::SCOPE_STORE);
        $AddressFromCity = $this->scopeConfig->getValue('general/store_information/city',ScopeInterface::SCOPE_STORE);
        $AddressFromRegion = $this->scopeConfig->getValue('general/store_information/region_id',ScopeInterface::SCOPE_STORE);
        $AddressFromStreet1 = $this->scopeConfig->getValue('general/store_information/street_line1',ScopeInterface::SCOPE_STORE);
        $AddresFromResidential = "false";
        $shippingAddress = $order->getShippingAddress();
        

        //Address To
        $AddressToZipCode = $shippingAddress->getPostcode();
        $AddressToRegion = $shippingAddress->getRegion();
        $AddressToCity = $shippingAddress->getCity();
        $AddressToStreet = $shippingAddress->getStreet();
        $AddressToStreet1    =  $AddressToStreet[0];
        $AddressToStreet2    = "";
        if(isset($AddressToStreet[1])){
            $AddressToStreet2 = $AddressToStreet[1];
        }
        $AddressToResidential = "true";
        $AddressToCompany = $shippingAddress->getCompany();

        $body = array(
            "CourierCode" => $shippingCode,
            "CourierServiceId" => $shippingServiceId,
            "ResellerReference" => "Transom-Group",
            "Content" => "Producto",
            "AddressFrom" => array(
                "ZipCode" => $AddressFromZip,
                "State" => $AddressFromRegion,
                "City" => $AddressFromCity,
                "Neighborhood" => "Vista Hermosa",
                "Address1" => $AddressFromStreet1,
                "Address2" => " ",
                "Residential" => $AddresFromResidential
            ),
            "AddressTo" => array(
                "ZipCode" => $AddressToZipCode,
                "State" => $AddressToRegion,
                "City" => $AddressToCity,
                "Neighborhood" => $AddressToStreet1,
                "Address1" => $AddressToStreet1,
                "Address2" => $AddressToStreet2,
                "Residential" => $AddressToResidential
            ),
            "Parcel" => array(
                'Length' => $productDimensions['length'],
                'Width' => $productDimensions['width'],
                'Height' => $productDimensions['height'],
                'Weight' => $productDimensions['weight']
            ),
            "Sender" => array(
                "Name"=> "Transom Group",
                "Phone1"=> $SenderPhone,
                "Phone2"=> $SenderPhone,
                "Email"=> $SenderEmail
            ),
            "Recipient" => array(
                "Name"=> $recipient_name,
                "CompanyName" => $recipient_company,
                "Phone1"=> $recipient_phone,
                "Email"=> $recipient_email
            )
        );
        return $body;
    }

}