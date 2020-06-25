<?php

namespace Transom\Tema\Controller\Index;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Action\Action;
 use Magento\Framework\App\Action\Context;
 use Magento\Framework\View\Result\PageFactory;
 use Magento\Framework\Filesystem;
 use Magento\Framework\App\Filesystem\DirectoryList;
 use Magento\Framework\Controller\ResultFactory; 
use Psr\Log\LoggerInterface;

class SendServiceForm extends Action
{
	
	  /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var LoggerInterface
     */
	protected $logger;

    /**
     * @var PageFactory
     */
	protected $resultPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Product\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    private $post;
    /**
     * @var Data
     */
    protected $customerSession;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository ,
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param Filesystem $filesystem
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $pageFactory
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param Data $customerSession
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
		\Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\Filesystem $filesystem,
		Context $context, 
		PageFactory $pageFactory,
		LoggerInterface $logger,
		TransportBuilder $transportBuilder

    ) {

		$this->logger = $logger;
		$this->transportBuilder = $transportBuilder;
        $this->storeManager     = $storeManager;
        $this->productRepository     = $productRepository;
        $this->productFactory  = $productFactory;
        $this->resultPageFactory = $pageFactory;
        $this->stockRegistry = $stockRegistry;
        parent::__construct($context);
    }
  
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $formData = array('name'=>$post["name"],'email'=>$post["email"],'telephone'=>$post["telephone"],'service'=>$post["service"],'state'=>$post["state"],'city'=>$post["city"],'comment'=>$post["comment"]);

      /*Send email to admin*/
      
      $receiverInfo = [
        'name' => 'Admin',
        'email' => 'ovazquez@transom-group.com'
        ];

        $emails = ['ovazquez@transom-group.com','shop_contact@transom-group.com']; //users that will receive the email

        $store = $this->storeManager->getStore();

      

        $transport = $this->transportBuilder->setTemplateIdentifier(
            'Transom_Tema_Servicio'
        )->setTemplateOptions(
            ['area' => 'frontend', 'store' => $store->getId()]
        )->addTo(
            $emails, $receiverInfo['name']
        )->setTemplateVars(
            $formData
        )->setFrom(
            'general'
        )->getTransport();

        try {
            // Send an email
            $transport->sendMessage();
            header("Location: https://shop.transom-group.com/servicios-instalacion-success");
				exit();
        } catch (\Exception $e) {
            // Write a log message whenever get errors
            $this->logger->critical($e->getMessage());
        }
    
		
    }
}