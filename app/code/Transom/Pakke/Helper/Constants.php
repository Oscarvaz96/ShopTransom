<?php
/**
 * Transom Group Inc.

 *
 * @category    Transom
 * @package     Transom_Group
 * @copyright   Copyright (c) Transom Group. All rights reserved. (https://transom-group.com/)
 */

namespace Transom\Pakke\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;

class Constants extends AbstractHelper
{

    //URL
    const URL_RATES = "https://seller.pakke.mx/api/v1/Shipments/rates";
    const URL_CREATE_SHIPMENT = "https://seller.pakke.mx/api/v1/Shipments";

    public function __construct(
        Context $context
    ) {
        parent::__construct($context);

    }


}