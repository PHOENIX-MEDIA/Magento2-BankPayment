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

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class BankPaymentConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string
     */
    protected $_methodCode = BankPayment::PAYMENT_METHOD_PHOENIX_BANKPAYMENT_CODE;

    /**
     * @var BankPayment
     */
    protected $_method;

    /**
     * @var Serialized $_serialized
     */
    protected $_serialized;

    /**
     * @var Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_cmsPageFactory;


    /**
     * @var \Magento\Cms\Helper\Page
     */
    protected $_cmsPageHelper;


    /**
     * @var array
     */
    protected $_accounts;

    /**
     * BankPaymentConfigProvider constructor.
     * @param \Magento\Cms\Model\PageFactory $cmsPageFactory
     * @param \Magento\Cms\Helper\Page $cmsPageHelper
     * @param PaymentHelper $paymentHelper
     * @param \Phoenix\BankPayment\Model\Serialized $serialized
     * @param Escaper $escaper
     */
    public function __construct(
        \Magento\Cms\Model\PageFactory $cmsPageFactory,
        \Magento\Cms\Helper\Page $cmsPageHelper,
        PaymentHelper $paymentHelper,
        Serialized $serialized,
        Escaper $escaper
    ) {
        $this->_cmsPageFactory = $cmsPageFactory;
        $this->_cmsPageHelper = $cmsPageHelper;
        $this->_escaper = $escaper;
        $this->_serialized = $serialized;
        $this->_method = $paymentHelper->getMethodInstance($this->_methodCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->_method->isAvailable() ? [
            'payment' => [
                $this->_methodCode => [
                    'formcmsurl'        => $this->getFormCmsUrl(),
                    'customtext'        => nl2br($this->_escaper->escapeHtml($this->_method->getCustomText())),
                    'instructions'      => nl2br($this->_escaper->escapeHtml($this->getInstructions())),
                    'accounts'          => $this->getAccounts()
                ],
            ],
        ] : [];
    }

    /**
     * @return \Magento\Framework\Phrase|null|string
     */
    public function getFormCmsUrl()
    {
        $pageUrl = null;
        $pageCode = $this->_method->getConfigData('form_cms_page');
        if (!empty($pageCode)) {
            if ($pageId = $this->_cmsPageFactory->create()->checkIdentifier($pageCode, $this->_method->getStore())) {
                $pageUrl = $this->_cmsPageHelper->getPageUrl($pageId);
                $pageUrl = __('More information on this payment method can be found <a target="_blank" href="%1">here</a>.', $pageUrl);
            }
        }
        return $pageUrl;
    }

    /**
     * @return array
     */
    public function getAccounts()
    {

        if (!$this->_accounts) {

            $accounts = $this->_serialized->unserialize($this->_method->getConfigData('bank_accounts'));

            $this->_accounts = array();
            $fields = is_array($accounts) ? array_keys($accounts) : null;
            if (!empty($fields)) {
                foreach ($accounts[$fields[0]] as $i => $k) {
                    if ($k) {
                        $account = array();
                        foreach ($fields as $field) {
                            $account[$field] = $this->_escaper->escapeHtml($accounts[$field][$i]);
                        }
                        $this->_accounts[] = $account;
                    }
                }
            }
        }
        return $this->_accounts;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getInstructions()
    {

        if (count($this->getAccounts()) == 1) {
            if ($this->_method->getPayWithinXDays() > 0) {
                return __('Please transfer the money within %1 days to the following bank account', $this->_method->getPayWithinXDays());
            } else {
                return __('Please transfer the money to the following bank account');
            }
        } else {
            if ($this->_method->getPayWithinXDays() > 0) {
                return __('Please transfer the money within %1 days to one of the following bank accounts', $this->_method->getPayWithinXDays());
            } else {
                return __('Please transfer the money to one of the following bank accounts');
            }
        }
    }
}
