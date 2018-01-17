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

namespace Phoenix\BankPayment\Helper;


trait Account
{
    /**
     * @param \Magento\Framework\DataObject $account
     * @return bool
     */
    public function displayFullAccountData($account) {
        return ($this->displaySepaAccountData($account) && $this->displayNonSepaAccountData($account));
    }

    /**
     * @param \Magento\Framework\DataObject $account
     * @return bool
     */
    public function displayNonSepaAccountData($account) {
        return ($account->getAccountNumber() && $account->getSortCode());
    }

    /**
     * @param \Magento\Framework\DataObject $account
     * @return bool
     */
    public function displaySepaAccountData($account) {
        return ($account->getIban());
    }
}