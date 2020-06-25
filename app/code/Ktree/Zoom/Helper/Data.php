<?php
/**
 * @author Ktree Team
 * @copyright Copyright (c) 2015 Ktree (http://www.ktree.com)
 * @package Ktree_Zoom
 */
namespace Ktree\Zoom\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterfac
     */
    protected $_scopeConfig;

    CONST ENABLE      = 'ktree_zoom/general/enable';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);

        $this->_scopeConfig = $scopeConfig;
    }
	
	public function getZoomConfig() {
		$outputHelper = $this->_scopeConfig;
		$config       = $outputHelper->getValue('ktree_zoom/general');
		
		return $config;
	}
    public function getEnable(){
        return $this->_scopeConfig->getValue(self::ENABLE);
    }

}

