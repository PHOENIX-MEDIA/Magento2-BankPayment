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


namespace Phoenix\BankPayment\Block\Adminhtml;

/**
 * Class BankAccount - Frontend model for account configuration
 * @package Phoenix\BankPayment\Block\Adminhtml
 */
class BankAccount extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $addRowButtonHtml = [];
    protected $removeRowButtonHtml = [];

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);

        $html = '<div id="bank_account_template" style="display:none">';
        $html .= $this->_getRowTemplateHtml();
        $html .= '</div>';

        $html .= '<div id="bank_account_container">';
        if ($this->_getValue('account_holder')) {
            foreach ($this->_getValue('account_holder') as $i => $f) {
                if ($i) {
                    $html .= $this->_getRowTemplateHtml($i);
                }
            }
        }
        $html .= '</div><br />';
        $html .= $this->_getAddRowButtonHtml(
            'bank_account_container',
            'bank_account_template', __('Add Bank Account')
        );

        return $html;
    }

    /**
     * @param int $i
     * @return string
     */
    protected function _getRowTemplateHtml($i = 0)
    {
        $html = '<fieldset>';
        $html .= '<label>' . __('Account holder') . '</label>';
        $html .= '<input class="input-text" type="text" name="' . $this->getElement()->getName() . '[account_holder][]" value="' . $this->_getValue('account_holder/' . $i) . '" ' . $this->_getDisabled() . ' />';
        $html .= '<label>' . __('Bank name') . '</label>';
        $html .= '<input class="input-text" type="text" name="' . $this->getElement()->getName() . '[bank_name][]" value="' . $this->_getValue('bank_name/' . $i) . '" ' . $this->_getDisabled() . ' />';
        $html .= '<label>' . __('IBAN') . '</label>';
        $html .= '<input class="input-text" type="text" name="' . $this->getElement()->getName() . '[iban][]" value="' . $this->_getValue('iban/' . $i) . '" ' . $this->_getDisabled() . ' />';
        $html .= '<label>' . __('BIC') . '</label>';
        $html .= '<input class="input-text" type="text" name="' . $this->getElement()->getName() . '[bic][]" value="' . $this->_getValue('bic/' . $i) . '" ' . $this->_getDisabled() . ' />';
        $html .= '<br />&nbsp;<br />';
        $html .= $this->_getRemoveRowButtonHtml();
        $html .= '</fieldset>';

        return $html;
    }

    /**
     * @return string
     */
    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function _getValue($key)
    {
        return $this->getElement()->getData('value/' . $key);
    }

    /**
     * @param $key
     * @param $value
     * @return string
     */
    protected function _getSelected($key, $value)
    {
        return $this->getElement()->getData('value/' . $key) == $value ? 'selected="selected"' : '';
    }

    /**
     * @param $container
     * @param $template
     * @param string $title
     * @return mixed
     */
    protected function _getAddRowButtonHtml($container, $template, $title = 'Add')
    {
        if (!isset($this->addRowButtonHtml[$container])) {
            $this->addRowButtonHtml[$container] = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Button')
                ->setType('button')
                ->setClass('add')
                ->setLabel(__($title))
                ->setOnClick("Element.insert($('" . $container . "'), {bottom: $('" . $template . "').innerHTML})")
                ->setDisabled($this->_getDisabled())
                ->toHtml();
        }
        return $this->addRowButtonHtml[$container];
    }

    /**
     * @param string $selector
     * @param string $title
     * @return array
     */
    protected function _getRemoveRowButtonHtml($selector = 'fieldset', $title = 'Delete Account')
    {
        if (!$this->removeRowButtonHtml) {
            $this->removeRowButtonHtml = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Button')
                ->setType('button')
                ->setClass('delete v-middle')
                ->setLabel(__($title))
                ->setOnClick("Element.remove($(this).up('" . $selector . "'))")
                ->setDisabled($this->_getDisabled())
                ->toHtml();
        }
        return $this->removeRowButtonHtml;
    }
}
