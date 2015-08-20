<?php

/**
 * Adds editable columns to the ShippableRegion model.
 */
class StreakRegionalShipping_ShippableRegionExtension extends GridSheetModelExtension {

    const ModelClass = 'StreakRegionalShipping_ShippableRegion';

    private static $enable_add_new_inline = true;

    public function updateCMSFields(FieldList $fields) {
        /** @var FormField $idField */
        $fields->removeByName('ShippableID');


        // TODO make this more friendly for multiple extended classes
        $shippables = Product::get()->map()->toArray();

        $fields->insertBefore(
            new Select2Field(
                'ShippableID',
                'Shippable',
                $shippables,
                $this->owner->ShippableID
            ),
            'Price'
        );

        $regions = Region_Shipping::get()->map()->toArray();

        $fields->insertBefore(
            new Select2Field(
                'RegionID',
                'Region',
                $regions,
                $this->owner->RegionID
            ),
            'Price'
        );
    }

    public function provideGridSheetData($modelClass, $isRelatedModel) {
        if (self::ModelClass == $modelClass && !$isRelatedModel) {
            return StreakRegionalShipping_ShippableRegion::get();
        }
    }

    /**
     * Provide editable columns to edit all shipping records e.g. in StreakModelAdmin.
     *
     * @param array $fieldSpecs
     */
    public function provideEditableColumns(array &$fieldSpecs) {
        // TODO make this deal with all shippable extended models
        $regions = Region_Shipping::get()->map()->toArray();
        $shippables = DataObject::get('Product')->map()->toArray();

        $fieldSpecs += array(
            'ShippableID' => array(
                'title' => 'Shippable',
                'callback' => function ($record, $col) use ($shippables) {
                    return new Select2Field(
                        'ShippableID',
                        '',
                        $shippables,
                        $record ? $record->$col : null
                    );
                }
            ),
            'RegionID' => array(
                'title' => 'Region',
                'callback' => function ($record, $col) use ($regions) {
                    return new Select2Field(
                        'RegionID',
                        '',
                        $regions,
                        $record ? $record->$col : null
                    );
                }
            ),
            'Price' => array(
                'title' => 'Shipping Price',
                'callback' => function ($record, $col) {
                    return new PriceField(
                        'Price',
                        '',
                        $record ? $record->$col : null
                    );
                }
            ),
            'ID' => array(
                'callback' => function ($record, $col) {
                    return new HiddenField(
                        'ID',
                        '',
                        $record ? $record->$col : null
                    );
                }
            ),
        );
    }

    /**
     * Provide editable columns for a related instance of the extended model where the 'parent' record needs a hidden
     * ID.
     *
     * @param $relatedModelClass
     * @param $relatedID
     * @param array $fieldSpecs
     * @return mixed|void
     */
    public function provideRelatedEditableColumns($relatedModelClass, $relatedID, array &$fieldSpecs) {
        $regions = Region_Shipping::get()->map()->toArray();

        $fieldSpecs += array(
            'RegionID' => array(
                'title' => 'Region',
                'callback' => function ($record, $col) use ($regions) {
                    return new Select2Field(
                        'RegionID',
                        '',
                        $regions,
                        $record ? $record->$col : null
                    );
                }
            ),
            'Price' => array(
                'title' => 'Shipping Price',
                'callback' => function ($record, $col) {
                    return new PriceField(
                        'Price',
                        '',
                        $record ? $record->$col : null
                    );
                }
            ),
            'ShippableID' => array(
                'callback' => function ($record, $col) use ($relatedID) {
                    return new HiddenField(
                        'ShippableID',
                        '',
                        $relatedID
                    );
                }
            ),
            'ID' => array(
                'callback' => function ($record, $col) {
                    return new HiddenField(
                        'ID',
                        '',
                        $record ? $record->$col : null
                    );
                }
            )
        );
    }

    /**
     * Called for each new row in a grid when it is saved.
     *
     * @param $record
     * @return bool
     */
    public function gridSheetHandleNewRow(array &$record) {
        $updateData = $this->getUpdateColumns(
            $this->owner->class,
            $record
        );
        $this->owner->update($updateData);
    }

    /**
     * Called to each existing row in a grid when it is saved.
     *
     * @param $record
     * @return bool
     */
    public function gridSheetHandleExistingRow(array &$record) {
        $updateData = $this->getUpdateColumns(
            $this->owner->class,
            $record
        );

        $this->owner->update($updateData);

    }
}