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
 * @copyright  Copyright (c) 2017 Phoenix Media GmbH (http://www.phoenix-media.eu)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Phoenix\BankPayment\Model;


class Serialized
{
    /**
     * @param String $value
     * @return array|mixed
     */
    public function unserialize(String $value)
    {
        $result = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // not in json format, try serialized array as fallback for pre magento 2.2 versions
            $result = @unserialize($value);
        }
        if (!is_array($result)) {
            $result = [];
        }
        return $result;
    }
}
