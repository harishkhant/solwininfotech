<?php
namespace Solwin\ContactwidgetExtend\Controller\Adminhtml\Contactwidget;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Custom ContactForm Registration')));
        return $resultPage;       
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Solwin_ContactwidgetExtend::contactwidgetextend_index');
    }
}