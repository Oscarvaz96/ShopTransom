<?php
/**
 * @author KTree Team
 * @package Ktree_Zoom
 */
namespace Ktree\Zoom\Block\Catalog\Product;

class Zoom extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ktree\Zoom\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Ktree\Zoom\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context, $data);

        $this->_helper
            = $helper;
        $this->_objectManager = $objectManager;
    }

    public function getBlockLabel(){
        return $this->_helper->getBlockLabel();
    }

    public function getTextAlign(){
        return $this->_helper->getTextAlign();
    }

    protected function _toHtml()
    {
       if ($this->_helper->getEnable()){
            return parent::_toHtml();
       }
        else {
            return '';
        }
    }

    public function getCollection()
    {
        $model = $this->_objectManager->create('Ktree\Zoom\Model\Zoom');
        $collection = $model->getCollection();

        return $collection;
    }

}
