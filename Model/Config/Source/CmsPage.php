<?php
/**
 * Phoenix Bank Prepayment module for Magento 2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Phoenix_BankPayment
 * @copyright  Copyright (c) 2018 Phoenix Media GmbH (http://www.phoenix-media.eu)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Phoenix\BankPayment\Model\Config\Source;

/**
 * Class CmsPage
 * @package Phoenix\BankPayment\Model\Config\Source
 */
class CmsPage extends \Magento\Cms\Model\Config\Source\Page
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            parent::toOptionArray();
            array_unshift(
                $this->options,
                [
                    'value' => '',
                    'label' => __('-- Please Select --')
                ]
            );
        }
        return $this->options;
    }
}
