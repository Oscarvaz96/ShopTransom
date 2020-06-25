<?php
namespace Transom\Pakke\Controller\Adminhtml\Download;

use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface;
use Transom\Pakke\Logger\Logger;

class Pdf extends Action
{
    /**
     * @var PageFactory
     */
    protected $request;
    protected $logger;
 
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */

    public function __construct(
        RequestInterface $request,
        Logger $logger
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->execute();
    }
 
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    { 
        $str = str_replace (' ','+', $this->request->getParam('key'));
        $decoded = base64_decode($str);
        $name = $this->request->getParam('name');
        $file = $name.'.pdf';
        file_put_contents($file, $decoded);

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }
 
}