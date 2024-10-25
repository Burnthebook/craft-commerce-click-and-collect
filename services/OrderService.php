<?php

namespace burnthebook\craftcommerceclickandcollect\services;

use Craft;
use craft\base\Component;
use craft\fields\PlainText;
use craft\models\FieldGroup;
use craft\commerce\elements\Order;
use craft\models\FieldLayoutTab;
use craft\models\FieldLayout;
use craft\fieldlayoutelements\CustomField;

class OrderService extends Component
{
    public function createFields()
    {
        // Create or retrieve the group
        $fieldGroup = $this->createGroup('Click & Collect');

        // Ensure the "Click & Collect" tab exists in the order layout
        $clickAndCollectTab = $this->createTab('Click & Collect', Order::class);

        // Create fields
        $this->createField('mobileNumber', 'Mobile Number', 'Customer mobile number for contact.', $fieldGroup, $clickAndCollectTab);
        $this->createField('collectionPoint', 'Collection Point', 'Customer collection point.', $fieldGroup, $clickAndCollectTab);
        $this->createField('collectionTime', 'Collection Time', 'Customer collection time.', $fieldGroup, $clickAndCollectTab);
    }

    // Create or retrieve a field group
    private function createGroup($groupName)
    {
        $fieldsService = Craft::$app->getFields();
        $allGroups = $fieldsService->getAllGroups();

        // Search for the group by name
        foreach ($allGroups as $group) {
            if ($group->name === $groupName) {
                return $group;
            }
        }

        // Create the group if it doesn't exist
        $fieldGroup = new FieldGroup();
        $fieldGroup->name = $groupName;
        if (!$fieldsService->saveGroup($fieldGroup)) {
            Craft::error('Could not save the field group. Errors: ' . print_r($fieldGroup->getErrors(), true), __METHOD__);
            return false;
        }

        return $fieldGroup;
    }

    // Create or retrieve a tab in the field layout
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

    // Generic method to create a field
    private function createField($handleSuffix, $name, $instructions, $fieldGroup, $tab)
    {
        $fieldsService = Craft::$app->getFields();

        // Generate field handle based on the suffix
        $fieldHandle = 'btbCnc' . ucfirst($handleSuffix);

        // Check if the field already exists
        $field = $fieldsService->getFieldByHandle($fieldHandle);
        if (!$field) {
            $field = new PlainText([
                'groupId' => $fieldGroup->id,
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

        $this->addFieldToTab($field, $tab, Order::class);
    }

    // Add a field to a given tab in the layout
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