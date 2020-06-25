<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Ktree\Zoom\Model\Config;

class Zoomtype  implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'lens', 'label' => __('Lens')], ['value' => 'window', 'label' => __('Window')],['value' => 'inner', 'label' => __('Inner')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['lens' => __('Lens'), 'window' => __('Window'),'inner' => __('inner')];
    }
}
