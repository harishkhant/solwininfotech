<?php
namespace Solwin\ContactwidgetExtend\Model\ResourceModel\Contact;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('Solwin\ContactwidgetExtend\Model\Contact', 'Solwin\ContactwidgetExtend\Model\ResourceModel\Contact');
    }
}

?>