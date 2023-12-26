<?php
namespace Solwin\ContactwidgetExtend\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class AdditionalField1 extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['additional_field_1'] == 'friend') {                    
                    $item[$this->getData('name')] = 'From a friend';
                } else if ($item['additional_field_1'] == 'search') {
                    $item[$this->getData('name')] = 'Found in a search engine';
                } else if ($item['additional_field_1'] == 'social_media'){
                    $item[$this->getData('name')] = 'Social Media';
                }
            }
        }
        return $dataSource;
    }
}
?>