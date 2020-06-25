<?php

namespace Transom\IncreasePrice\Plugin;

use Magento\Eav\Api\AttributeSetRepositoryInterface;

class ProductPlugin
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
			
			$percentage =  $subject->getCustomAttribute("porcentaje_incremento")->getValue();
			
			return $result+(($result*$percentage)/100);
		} else {
			return $result;
		}
    }
}
