<?php
namespace Solwin\ContactwidgetExtend\Block\Adminhtml\CustomButton\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
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
    public function getButtonData()
    {
        return [
			'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\''
                    . __('Are you sure you want to delete this Info ?')
                    . '\', \'' . $this->getDeleteUrl() . '\')',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'delete']],
                    'form-role' => 'delete',
                ],
                'sort_order' => 20,
		];
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        $entity_id = $this->context->getRequest()->getParam('entity_id');
        return $this->getUrl('*/*/delete', ['entity_id' => $entity_id]);
    }
}