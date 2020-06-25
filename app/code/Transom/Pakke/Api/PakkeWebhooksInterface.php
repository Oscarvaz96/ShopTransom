<?php
 
namespace Transom\Pakke\Api;
 
interface PakkeWebhooksInterface
{
    /**
     * Returns a webhook response
     *
     * @api
     * @return array webhook response.
     */
    public function getWebhook();
 
}