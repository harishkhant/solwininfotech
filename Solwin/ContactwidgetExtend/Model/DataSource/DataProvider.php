<?php
namespace Solwin\ContactwidgetExtend\Model\DataSource;

use Solwin\ContactwidgetExtend\Model\ResourceModel\Contact\CollectionFactory;
use Solwin\ContactwidgetExtend\Model\Contact;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $contactCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $contactCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = array();
        
        foreach ($items as $rowdata) {
           
            $this->loadedData[$rowdata->getId()]['entity_id'] = $rowdata->getData();
            
        }
        //print_r($this->loadedData);die;
        return $this->loadedData;
    }
}