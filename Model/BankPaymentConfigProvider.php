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
     * @var BankPayment $method
     */
    private $method;

    /**
     * @var Serialized $serialized
     */
    private $serialized;

    /**
     * @var Escaper $escaper
     */
    private $escaper;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    private $cmsPageFactory;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private $cmsPageHelper;

    /**
     * @var array
     */
    private $accounts;

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
    )
    {
        $this->cmsPageFactory = $cmsPageFactory;
        $this->cmsPageHelper = $cmsPageHelper;
        $this->escaper = $escaper;
        $this->serialized = $serialized;
        $this->method = $paymentHelper->getMethodInstance(BankPayment::PAYMENT_METHOD_PHOENIX_BANKPAYMENT_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                BankPayment::PAYMENT_METHOD_PHOENIX_BANKPAYMENT_CODE => [
                    'formcmsurl' => $this->getFormCmsUrl(),
                    'customtext' => nl2br($this->escaper->escapeHtml($this->method->getCustomText())),
                    'instructions' => nl2br($this->escaper->escapeHtml($this->getInstructions())),
                    'accounts' => $this->getAccounts()
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
        $pageCode = $this->method->getConfigData('form_cms_page');
        if (!empty($pageCode)) {
            if ($pageId = $this->cmsPageFactory->create()->checkIdentifier($pageCode, $this->method->getStore())) {
                $pageUrl = $this->cmsPageHelper->getPageUrl($pageId);
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

        if (!$this->accounts) {

            $accounts = $this->serialized->unserialize($this->method->getConfigData('bank_accounts'));

            $this->accounts = [];
            $fields = is_array($accounts) ? array_keys($accounts) : null;
            if (!empty($fields)) {
                foreach ($accounts[$fields[0]] as $i => $k) {
                    if ($k) {
                        $account = [];
                        foreach ($fields as $field) {
                            $account[$field] = $this->escaper->escapeHtml($accounts[$field][$i]);
                        }
                        $this->accounts[] = $account;
                    }
                }
            }
        }
        return $this->accounts;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getInstructions()
    {

        if (count($this->getAccounts()) == 1) {
            if ($this->method->getPayWithinXDays() > 0) {
                return __('Please transfer the money within %1 days to the following bank account', $this->method->getPayWithinXDays());
            } else {
                return __('Please transfer the money to the following bank account');
            }
        } else {
            if ($this->method->getPayWithinXDays() > 0) {
                return __('Please transfer the money within %1 days to one of the following bank accounts', $this->method->getPayWithinXDays());
            } else {
                return __('Please transfer the money to one of the following bank accounts');
            }
        }
    }
}
