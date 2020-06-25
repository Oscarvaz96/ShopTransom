<?php
 
namespace Transom\Pakke\Model;
 
use Transom\Pakke\Api\PakkeWebhooksInterface;
use \Magento\Framework\Webapi\Rest\Request;
use Transom\Pakke\Logger\Logger;

class ReadWebhooks implements PakkeWebhooksInterface
{
    protected $request;
    protected $logger;

    public function __construct(
      Request $request,
      Logger $logger
    ) {
        $this->request = $request;
        $this->logger = $logger;
    }

 
    /**
     * Returns a webhook response
     *
     * @api
     * @return array webhook response.
     */
    public function getWebhook()
    {
        $response = $this->request->getParams();
        return $response;

    }
 
}