<?php

namespace Transom\Pakke\Block\TrackInfo;

use Magento\Framework\View\Element\Template;

class TrackingInfo extends Template
{
    public $trackingData;

    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function setTrackingInfo($trackingData){
        $this->trackingData = $trackingData;
    }

    public function getTrackingInfo(){
        return $this->trackingData;
    }
}
