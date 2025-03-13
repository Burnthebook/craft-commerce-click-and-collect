<?php

namespace burnthebook\craftcommerceclickandcollect\services;

use Craft;
use craft\base\Component;
use craft\fields\PlainText;
use craft\commerce\elements\Order;
use craft\models\FieldLayoutTab;
use craft\models\FieldLayout;
use craft\fieldlayoutelements\CustomField;

class OrderService extends Component
{
    /**
     * Creates the necessary fields and adds them to the order layout.
     *
     * @return void
    */
    public function createFields()
    {
        // Ensure the "Click & Collect" tab exists in the order layout
        $clickAndCollectTab = $this->createTab('Click & Collect', Order::class);

        // Create fields (NO GROUPS REQUIRED)
        $this->createField('mobileNumber', 'Mobile Number', 'Customer mobile number for contact.', $clickAndCollectTab);
        $this->createField('collectionPoint', 'Collection Point', 'Customer collection point.', $clickAndCollectTab);
        $this->createField('collectionTime', 'Collection Time', 'Customer collection time.', $clickAndCollectTab);
    }

    /**
     * Creates a new tab for a given element type.
     *
     * @param string $tabName The name of the tab to be created.
     * @param string $elementType The type of element the tab will be associated with.
     * @return FieldLayoutTab|false The newly created tab, or false if there was an error.
    */
    private function createTab($tabName, $elementType)
    {
        $fieldsService = Craft::$app->getFields();
        $fieldLayout = $fieldsService->getLayoutByType($elementType);

        if (!$fieldLayout) {
            $fieldLayout = new FieldLayout();
            $fieldLayout->type = $elementType;
        }

        // Search for the tab by name
        foreach ($fieldLayout->getTabs() as $tab) {
            if ($tab->name === $tabName) {
                return $tab;
            }
        }

        // Create the tab if it doesn't exist
        $newTab = new FieldLayoutTab();
        $newTab->name = $tabName;
        $newTab->sortOrder = count($fieldLayout->getTabs()) + 1;
        $fieldLayout->setTabs(array_merge($fieldLayout->getTabs(), [$newTab]));

        if (!$fieldsService->saveLayout($fieldLayout)) {
            Craft::error('Could not save the field layout. Errors: ' . print_r($fieldLayout->getErrors(), true), __METHOD__);
            return false;
        }

        return $newTab;
    }

    /**
     * Creates a new field and assigns it to a tab.
     *
     * @param string $handleSuffix The suffix to use for the field handle.
     * @param string $name The name of the field.
     * @param string $instructions The instructions for the field.
     * @param FieldLayoutTab $tab The tab to add the field to.
     * @return bool Whether the field was successfully created and added to the tab.
    */
    private function createField($handleSuffix, $name, $instructions, $tab)
    {
        $fieldsService = Craft::$app->getFields();

        // Generate field handle based on the suffix
        $fieldHandle = 'btbCnc' . ucfirst($handleSuffix);

        // Check if the field already exists
        $field = $fieldsService->getFieldByHandle($fieldHandle);
        if (!$field) {
            $field = new PlainText([
                'name' => $name,
                'handle' => $fieldHandle,
                'instructions' => $instructions,
                'required' => false,
            ]);

            if (!$fieldsService->saveField($field)) {
                Craft::error("Could not save the $name field. Errors: " . print_r($field->getErrors(), true), __METHOD__);
                return false;
            }
        }

        // Add the field to the tab
        $this->addFieldToTab($field, $tab, Order::class);
    }

    /**
     * Adds a field to a tab in a field layout for a specific element type.
     *
     * @param Field $field The field to be added.
     * @param FieldLayoutTab $tab The tab in the field layout to add the field to.
     * @param string $elementType The type of element the field layout is for.
     * @return bool Whether the field was successfully added to the tab.
    */
    private function addFieldToTab($field, $tab, $elementType)
    {
        $fieldsService = Craft::$app->getFields();
        $fieldLayout = $fieldsService->getLayoutByType($elementType);

        // Wrap the field in a CustomField element
        $customFieldElement = new CustomField($field);

        // Ensure the field is not already in the layout for this tab
        $existingElements = $tab->getElements();
        foreach ($existingElements as $element) {
            if ($element instanceof CustomField && $element->getField()->id === $field->id) {
                // Field already exists in the tab, no need to add it again
                return;
            }
        }

        // Add the field element to the tab
        $tab->setElements(array_merge($existingElements, [$customFieldElement]));

        // Save the updated field layout
        if (!$fieldsService->saveLayout($fieldLayout)) {
            Craft::error('Could not save the field layout. Errors: ' . print_r($fieldLayout->getErrors(), true), __METHOD__);
            return false;
        }
    }
}