<?php

namespace Transom\IncreasePrice\Plugin;

use Magento\Eav\Api\AttributeSetRepositoryInterface;

class ProductPluginPrice
{
	protected $_attributeRepository;
	
	public function __construct(
		AttributeSetRepositoryInterface $attributeRepository
	) {
		$this->_attributeRepository = $attributeRepository;
	}
   
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
		
			$attributeSetRepository = $this->_attributeRepository->get($subject->getAttributeSetId());
		$attributeSetName = $attributeSetRepository->getAttributeSetName();
		
		if($attributeSetName == "Incrementar_Precio"){
			
			
			
			return $result+(($result*35)/100);
		} else {
			return $result;
		}
		
	}
		
}
