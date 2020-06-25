<?php

namespace Transom\Pakke\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Transom\Pakke\Model\Connect;
use Transom\Pakke\Logger\Logger;
use \Magento\Framework\Json\Helper\Data;
use Transom\Pakke\Helper\ConfigFunctions;
use Magento\Checkout\Model\Session;
use Transom\Pakke\Helper\Constants;

/**
 * Custom shipping model
 */
class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = "Pakke";
    
    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;

    public $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    protected $scopeConfig;
    protected $connect;
    protected $logger;
    protected $_jsonHelper;
    protected $_cart;
    protected $configFunctions;
    protected $constants;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        Session $cart,
        Connect $connect,
        Logger $loggerPakke,
        Data $jsonHelper,
        ConfigFunctions $configFunctions,
        Constants $constants,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->scopeConfig = $scopeConfig;
        $this->connect = $connect;
        $this->logger = $loggerPakke;
        $this->_jsonHelper = $jsonHelper;
        $this->_cart = $cart;
        $this->configFunctions = $configFunctions;
        $this->constants = $constants;
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $allowedMethods = $this->getAllowedMethods();
       if(!empty($allowedMethods)){
           
        foreach ($allowedMethods as $key) {
            $method = $this->rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($key["Courier Code"]." ".$key["Courier Service Id"]);
            $method->setMethod($key["Courier Service Id"]);
            $method->setMethodTitle($key["Delivery Days"]);
            $shippingCost = $key["Total Price"];
            $method->setPrice($shippingCost);
            $method->setCost($shippingCost);
            $result->append($method);
        }

        return $result;
       }
        
    }

    public function getRates(){
        $url = $this->constants::URL_RATES;
        $method = \Zend\Http\Request::METHOD_POST;
        $zipCodeFrom = $this->configFunctions->getStoreZipCode();
        $ZipCodeTo = $this->_cart->getQuote()->getShippingAddress()->getPostcode();
        $items = $this->_cart->getQuote()->getAllVisibleItems();
        $dimensions = $this->configFunctions->getProductDimensions($items,"1");
           
        $body = [
            'ZipCodeFrom' => $zipCodeFrom,
            'ZipCodeTo' => $ZipCodeTo,
            'Parcel' => [
                'Weight' => $dimensions["weight"],
                'Width' => $dimensions["width"],
                'Height' => $dimensions["height"],
                'Length' => $dimensions["length"]
            ]
        ];

        $arrayRates = $this->connect->createConnection($url,$method,$body);
        $encodedData =  $this->_jsonHelper->jsonEncode($arrayRates);
        $quotation = array();

		if(!empty($arrayRates["Pakke"])){
			foreach ($arrayRates["Pakke"] as $valor){
				$couriers = array(
					"Courier Code" => $valor['CourierCode'],
					"Courier Service Id" => $valor['CourierServiceId'],
					"Courier Service Name" => $valor['CourierServiceName'],
					"Delivery Days" => $valor['DeliveryDays'],
					"Total Price" => $valor['TotalPrice'],
			 	);
				array_push($quotation, $couriers);
			}
			return $quotation;
		}
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
       $arrayRates = $this->getRates();
       return $arrayRates;
    }
}
