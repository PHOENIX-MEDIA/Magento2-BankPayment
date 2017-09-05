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


namespace Phoenix\BankPayment\Block;

/**
 * Class Info
 * @package Phoenix\BankPayment\Block
 */
class Info extends \Magento\Payment\Block\Info
{

    /**
     * @var string
     */
    protected $_template = 'info.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Phoenix_BankPayment::info/pdf.phtml');
        return $this->toHtml();
    }

    /**
     * @return mixed
     */
    public function getAccounts()
    {
        return $this->getMethod()->getAccounts();
    }

    /**
     * @return mixed
     */
    public function getShowBankAccountsInPdf()
    {
        return $this->getMethod()->getConfigData('show_bank_accounts_in_pdf');
    }

    /**
     * @return mixed
     */
    public function getShowCustomTextInPdf()
    {
        return $this->getMethod()->getConfigData('show_customtext_in_pdf');
    }
}
