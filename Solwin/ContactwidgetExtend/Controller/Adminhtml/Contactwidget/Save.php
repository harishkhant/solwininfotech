<?php
namespace Solwin\ContactwidgetExtend\Controller\Adminhtml\Contactwidget;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Solwin\ContactwidgetExtend\Model\ContactFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\ScopeInterface;

class Save extends Action
{
    const EMAIL_TEMPLATE = 'contactwidget_section/emailsend/emailtemplate';
    const EMAIL_SENDER = 'contactwidget_section/emailsend/emailsenderto';
    const XML_PATH_EMAIL_RECIPIENT = 'contactwidget_section/emailsend/emailto';

    protected $_transportBuilder;
    protected $_inlineTranslation;
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_contactFactory;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ContactFactory $contactFactory
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_contactFactory = $contactFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data['data']);

            $error = false;
            $notEmptyValidator = new \Magento\Framework\Validator\NotEmpty();

            if (!$notEmptyValidator->isValid(trim($data['data']['name']))) {
                $error = true;
            }

            if (!$notEmptyValidator->isValid(trim($data['data']['email']))) {
                $error = true;
            }

            if (!$notEmptyValidator->isValid(trim($data['data']['subject']))) {
                $error = true;
            }

            if (!$notEmptyValidator->isValid(trim($data['data']['comment']))) {
                $error = true;
            }

            if ($error) {
                throw new \Exception();
            }

            $contactModel = $this->_contactFactory->create();
            $contactModel->setData('name', $data['data']['name']);
            $contactModel->setData('email', $data['data']['email']);
            $contactModel->setData('telephone', $data['data']['telephone']);
            $contactModel->setData('subject', $data['data']['subject']);
            $contactModel->setData('comment', $data['data']['comment']);
            $contactModel->setData('additional_field_1', $data['data']['how_hear_about_us']);
            $contactModel->setData('additional_field_2', implode(',', $data['data']['interests']));
            $contactModel->setData('additional_field_3', $data['data']['subscribe_to_newsletter']);
            $contactModel->save();

            // Include the contact model in the templateVars
            $additionalField1Value = $this->getadditionalField1Value($data['data']['how_hear_about_us']);
            $additionalField2Value = $this->getadditionalField2Value(implode(',', $data['data']['interests']));
            $additionalField3Value = $this->getadditionalField3Value($data['data']['subscribe_to_newsletter']);
            
            // send mail to recipients
            $this->_inlineTranslation->suspend();
            $storeScope = ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder->setTemplateIdentifier(
                $this->_scopeConfig->getValue(
                    self::EMAIL_TEMPLATE,
                    $storeScope
                )
            )->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->_storeManager
                        ->getStore()
                        ->getId(),
                ]
            )->setTemplateVars([
                'data' => $postObject,
                'additionalField1Value' => $additionalField1Value,
                'additionalField2Value' => $additionalField2Value,
                'additionalField3Value' => $additionalField3Value,
            ])->setFrom($this->_scopeConfig->getValue(
                self::EMAIL_SENDER,
                $storeScope
            ))
                ->addTo($this->_scopeConfig->getValue(
                    self::XML_PATH_EMAIL_RECIPIENT,
                    $storeScope
                ))
                ->getTransport();

            $transport->sendMessage();
            $this->_inlineTranslation->resume();

            $this->messageManager->addSuccess(__('Contact Us request has been received. We\'ll respond to you very soon.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError($e->getMessage());
            $this->messageManager->addException($e, __('Something went wrong while sending the contact us request.'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    public function getadditionalField1Value($dataSource)
    {
        if ($dataSource == 'friend') {                    
            return 'From a friend';
        } else if ($dataSource == 'search') {
            return 'Found in a search engine';
        } else if ($dataSource == 'social_media'){
            return 'Social Media';
        }
    }

    public function getadditionalField2Value($dataSource)
    {
        // Define the strings to replace and their corresponding replacements
        $replaceMap = [
            'sports' => 'Sports',
            'technology' => 'Technology',
            'food' => 'Food',
        ];

        // Perform the replacement
        $newValue = str_replace(array_keys($replaceMap), array_values($replaceMap), $dataSource);

        return $newValue;
    }

    public function getadditionalField3Value($dataSource)
    {
        if ($dataSource == 'on') {                    
            return 'Yes';
        } else {
            return 'No';
        } 
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Solwin_ContactwidgetExtend::contactwidgetextend_save');
    }
}