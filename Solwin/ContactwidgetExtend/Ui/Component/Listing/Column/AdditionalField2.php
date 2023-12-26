<?php
namespace Solwin\ContactwidgetExtend\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class AdditionalField2 extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $additionalField2 = $item['additional_field_2'];

                if (is_array($additionalField2)) {
                    $labels = [];
                    foreach ($additionalField2 as $value) {
                        $labels[] = $this->getLabelForAdditionalField2($value);
                    }
                    $item[$this->getData('name')] = implode(', ', $labels);
                } else {
                    // Replace specific strings in $additionalField2
                    $item[$this->getData('name')] = $this->replaceStringsInField2($additionalField2);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Replace specific strings in Additional Field 2
     *
     * @param string $value
     * @return string
     */
    protected function replaceStringsInField2($value)
    {
        // Define the strings to replace and their corresponding replacements
        $replaceMap = [
            'sports' => 'Sports',
            'technology' => 'Technology',
            'food' => 'Food',
        ];

        // Perform the replacement
        $newValue = str_replace(array_keys($replaceMap), array_values($replaceMap), $value);

        return $newValue;
    }

}
?>