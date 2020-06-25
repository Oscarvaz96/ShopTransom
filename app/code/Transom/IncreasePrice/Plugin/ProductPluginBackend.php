<?php

namespace Transom\IncreasePrice\Plugin;

use Magento\Eav\Api\AttributeSetRepositoryInterface;

class ProductPluginBackend
{
	protected $_attributeRepository;
	
	public function __construct(
		AttributeSetRepositoryInterface $attributeRepository
	) {
		$this->_attributeRepository = $attributeRepository;
	}
   
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
		
			return $result;
		
	}
		
}
