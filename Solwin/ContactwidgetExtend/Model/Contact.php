<?php
/**
 * Solwin Infotech
 * Solwin Contact Form Widget Extension
 *
 * @category   Solwin
 * @package    Solwin_ContactwidgetExtend
 * @copyright  Copyright © 2006-2020 Solwin (https://www.solwininfotech.com)
 * @license    https://www.solwininfotech.com/magento-extension-license/
 */
namespace Solwin\ContactwidgetExtend\Model;

use Magento\Framework\Model\AbstractModel;

class Contact extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Solwin\ContactwidgetExtend\Model\ResourceModel\Contact::class);
    }
}
