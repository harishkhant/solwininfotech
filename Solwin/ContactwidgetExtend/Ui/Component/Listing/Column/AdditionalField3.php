<?php
namespace Solwin\ContactwidgetExtend\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class AdditionalField3 extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $additionalField3 = $item['additional_field_3'];

                // Convert 'on' to 'Yes' and 'off' to 'No'
                $item[$this->getData('name')] = ($additionalField3 == 'on') ? __('Yes') : __('No');
            }
        }

        return $dataSource;
    }
}
?>