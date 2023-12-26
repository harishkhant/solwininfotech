<?php
namespace Solwin\ContactwidgetExtend\Block\Adminhtml\CustomButton\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

class SaveEditButton extends GenericButton implements ButtonProviderInterface 
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }
    /**
     * Get button data
     *
     * @return array
     */
	public function getButtonData() {
		return [
			'label' => __('Save Edit'),
			'class' => 'save primary',
			'data_attribute' => [
				'mage-init' => ['button' => ['event' => 'save']],
				'form-role' => 'save',
			],
			'on_click' => sprintf("location.href= '%s';", $this->getSaveUrl()),
			'sort_order' => 90,
		];
	}

	public function getSaveUrl() {
		$entity_id = $this->context->getRequest()->getParam('entity_id');
		return $this->getUrl('*/*/saveedit', ['entity_id' => $entity_id]);
	}
}