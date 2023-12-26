<?php
namespace Solwin\ContactwidgetExtend\Block\Widget;

class ContactUsWidget extends \Solwin\Contactwidget\Block\Widget\ContactUsWidget
{
    /**
     * Get form action URL
     */
    public function getFormActionUrl()
    {
        // Customize the form action URL if needed
        return $this->getUrl('contactwidget/widget/index');
    }

    /**
     * Get options for Additional Field 1
     *
     * @return array
     */
    public function getAdditionalField1Options()
    {
        // Return an array of options for Additional Field 1
        return [
            ['value' => 'friend', 'label' => __('From a friend')],
            ['value' => 'search', 'label' => __('Found in a search engine')],
            ['value' => 'social_media', 'label' => __('Social Media')],
            // ... Add more options as needed ...
        ];
    }

    /**
     * Get options for Additional Field 2
     *
     * @return array
     */
    public function getAdditionalField2Options()
    {
        // Return an array of options for Additional Field 2
        return [
            ['value' => 'sports', 'label' => __('Sports')],
            ['value' => 'technology', 'label' => __('Technology')],
            ['value' => 'food', 'label' => __('Food')],
            // ... Add more options as needed ...
        ];
    }

    /**
     * Get options for Additional Field 3
     *
     * @return array
     */
    public function getAdditionalField3Options()
    {
        // Return an array of options for Additional Field 3
        return [
            ['value' => 'yes', 'label' => __('Yes')],
            ['value' => 'no', 'label' => __('No')],
            // ... Add more options as needed ...
        ];
    }
}

?>