<?php
namespace Solwin\ContactwidgetExtend\Controller\Adminhtml\Contactwidget;
 
use Magento\Backend\App\Action;
 
class Delete extends Action
{
    private $_model;

    public function __construct(
    Action\Context $context,
    \Solwin\ContactwidgetExtend\Model\Contact $model
    ) {
        parent::__construct($context);
        $this->_model = $model;
      }  
 
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_model;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Successfully deleted'));
                return $resultRedirect->setPath('contactwidget_extend/contactwidget/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('contactwidget_extend/contactwidget/edit', ['entity_id' => $id]);
            }
        }
        $this->messageManager->addError(__('Data does not exist'));
        return $resultRedirect->setPath('contactwidget_extend/contactwidget/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Solwin_ContactwidgetExtend::contactwidgetextend_delete');
    }
}