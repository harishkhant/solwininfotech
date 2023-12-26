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
namespace Solwin\ContactwidgetExtend\Controller\Widget;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Area;
use Solwin\ContactwidgetExtend\Model\ContactFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    const EMAIL_TEMPLATE = 'contactwidget_section/emailsend/emailtemplate';
    const EMAIL_SENDER = 'contactwidget_section/emailsend/emailsenderto';
    const XML_PATH_EMAIL_RECIPIENT = 'contactwidget_section/emailsend/emailto';

    protected $_transportBuilder;
    protected $_inlineTranslation;
    protected $_scopeConfig;
    protected $_helper;
    protected $_storeManager;
    protected $_contactFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Solwin\Contactwidget\Helper\Data $helper,
        ContactFactory $contactFactory
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_contactFactory = $contactFactory;
    }

    public function execute()
    {
        $remoteAddr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
        $data = $this->getRequest()->getParams();
        if (array_key_exists('additional_field_3', $data)) {
            $additionalField3 = $data['additional_field_3'];
        } else {
            $data['additional_field_3'] = 'off';
            $additionalField3 = $data['additional_field_3'];
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        $data['name'] = $this->removeScriptTag($data['name']);
        if ($data['name'] == "") {
            $this->messageManager->addError(__('Name is required field. Please Enter correct value.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data['subject'] = $this->removeScriptTag($data['subject']);
        if ($data['subject'] == "") {
            $this->messageManager->addError(__('Subject is required field. Please Enter correct value.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data['comment'] = $this->removeScriptTag($data['comment']);
        if ($data['comment'] == "") {
            $this->messageManager->addError(__('What’s on your mind? is required field. Please Enter correct value.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
        
        $redirectUrl = $data['currUrl'];
        $secretkey = $this->_helper->getConfigValue('contactwidget_section/recaptcha/recaptcha_secretkey');
        $sitekey = $this->_helper->getConfigValue('contactwidget_section/recaptcha/recaptcha_sitekey');
        $enablerecaptcha = $data['enablerecaptcha'];

        if ($enablerecaptcha && ($sitekey == "" || $secretkey == "")) {
            $this->messageManager->addError('Recaptcha enabled but the captcha site key or secret key may be empty. Please contact the store admin for this issue.');
            return $resultRedirect->setUrl($redirectUrl);
        }

        $captchaErrorMsg = $this->_helper
        ->getConfigValue(
            'contactwidget_section/recaptcha/recaptcha_errormessage'
        );

        if ($data['enablerecaptcha']) {
            if ($captchaErrorMsg == '') {
                $captchaErrorMsg = 'Invalid captcha. Please try again.';
            }
            $captcha = '';
            if (filter_input(INPUT_POST, 'g-recaptcha-response') !== null) {
                $captcha = filter_input(INPUT_POST, 'g-recaptcha-response');
            }

            if (!$captcha) {
                $this->messageManager->addError($captchaErrorMsg);
                return $resultRedirect->setUrl($redirectUrl);
            } else {
                $response = file_get_contents(
                    "https://www.google.com/recaptcha/api/siteverify"
                        . "?secret=" . $secretkey
                        . "&response=" . $captcha
                    . "&remoteip=" . $remoteAddr
                );
                $response = json_decode($response, true);

                if ($response["success"] === false) {
                    $this->messageManager->addError($captchaErrorMsg);
                    return $resultRedirect->setUrl($redirectUrl);
                }
            }
        }

        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);

            
            $error = false;
            $notEmptyValidator = new \Magento\Framework\Validator\NotEmpty();

            if (!$notEmptyValidator->isValid(trim($data['name']))) {
                $error = true;
            }

            if (!$notEmptyValidator->isValid(trim($data['email']))) {
                $error = true;
            }

            if (!$notEmptyValidator->isValid(trim($data['subject']))) {
                $error = true;
            }

            if (!$notEmptyValidator->isValid(trim($data['comment']))) {
                $error = true;
            }

            if ($error) {
                throw new \Exception();
            }

            $contactModel = $this->_contactFactory->create();
            $contactModel->setData('name', $data['name']);
            $contactModel->setData('email', $data['email']);
            $contactModel->setData('telephone', $data['telephone']);
            $contactModel->setData('subject', $data['subject']);
            $contactModel->setData('comment', $data['comment']);
            $contactModel->setData('additional_field_1', $data['additional_field_1']);
            $contactModel->setData('additional_field_2', implode(',', $data['additional_field_2']));
            $contactModel->setData('additional_field_3', $data['additional_field_3']);
            $contactModel->save();
            
            // Include the contact model in the templateVars
            $additionalField1Value = $this->getadditionalField1Value($data['additional_field_1']);
            $additionalField2Value = $this->getadditionalField2Value(implode(',', $data['additional_field_2']));
            $additionalField3Value = $this->getadditionalField3Value($data['additional_field_3']);

            // send mail to recipients
            $this->_inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
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
            return $resultRedirect->setUrl($redirectUrl);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError($e->getMessage());
            $this->messageManager->addException($e, __('Something went wrong while sending the contact us request.'));
        }

        return $resultRedirect->setUrl($redirectUrl);
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

    public function removeScriptTag($original_string, $replace_string = "")
    {
        return preg_replace("/<\s*script.*?>.*?(<\s*\/script.*?>|$)/is", $replace_string, $original_string);
    }
}