<?php
/**
 * Transom Group Inc.

 *
 * @category    Transom
 * @package     Transom_Group
 * @copyright   Copyright (c) Transom Group. All rights reserved. (https://transom-group.com/)
 */

namespace Transom\Pakke\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use \Transom\Pakke\Logger\Logger;
use \Magento\Framework\Json\Helper\Data;

class ConfigFunctions extends AbstractHelper
{

    /**
     * Logging instance
     * @var \Transom\SiftModule\Logger\Logger
     */

    protected $_logger;
    protected $scopeConfig;
    protected $_jsonHelper;

    public function __construct(
        Logger $logger,
        Context $context,              
        Data $jsonHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->_jsonHelper = $jsonHelper;
         parent::__construct($context);

    }
  
    public function getApiKey(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $ApiKey = $this->scopeConfig->getValue('carriers/Pakke/api_key',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $ApiKey;
    }

    public function getLogger(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $pakke_logger = $this->scopeConfig->getValue('carriers/Pakke/pakke_logger',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $pakke_logger;
    }

    public function getDebugger(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $pakke_debugger = $this->scopeConfig->getValue('carriers/Pakke/pakke_debugger',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $pakke_debugger;
    }

    public function getStoreZipCode(){
        $zipcode = $this->scopeConfig->getValue('general/store_information/postcode',ScopeInterface::SCOPE_STORE);
        return $zipcode;
    }

    public function getProductDimensions(array $orderItems, $option){

        $weight = 0.0;
        $length = 0.0;
        $width = 0.0;
        $height = 0.0;
        $dimensions = array();
        $optionEntity = $option;
         foreach ($orderItems as $item) {

           
            

             $product = $item->getProduct();
             if($optionEntity == "1"){
                $weight += (float) ($product->getData('weight')*$item->getQty());
                $height += (float) ($product->getData('ts_dimensions_height')*$item->getQty()); 
               
              
             }

             if($optionEntity == "2"){
                $weight += (float) ($product->getData('weight')*$item->getQtyOrdered());
                $height += (float) ($product->getData('ts_dimensions_height')*$item->getQtyOrdered()); 
                
             }

             $length += (float) $product->getData('ts_dimensions_length');
             $width += $product->getData('ts_dimensions_width');  
            

           

            //     $length += $product->getResource()->getAttribute('ts_dimensions_length')->getFrontend()->getValue($product);
            //    $width += $product->getResource()->getAttribute('ts_dimensions_width')->getFrontend()->getValue($product);
             
         }

            $dimensions = array(
                'weight' => $weight,
                'length' => $length,
                'width' => $width,
                'height' => $height
            );


        return $dimensions;
    }


}