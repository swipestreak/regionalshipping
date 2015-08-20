<?php

/**
 * Adds a 'Shippable' relationship to the extended model which is a has_many to SteakRegionalShippingShippable model.
 *
 * Provides editable columns for a gridsheet which is showing
 */
class StreakRegionalShipping_ShippableExtension extends GridSheetModelExtension
{
    const GridFieldName = 'Shippable';
    const DefaultTabName = 'Root.RegionalShipping';

    const ModelClass = 'StreakRegionalShipping_Shippable';

    const RelatedModelClass = 'StreakRegionalShipping_ShippableRegion';

    const RelationshipField = 'StreakRegionalShipping_ShippableRegion.Shippable';
    const RelationshipName = 'Shippables';

    private static $has_many = array(
        self::RelationshipName => self::RelationshipField
    );

    private static $tab_name = self::DefaultTabName;

    public function provideGridSheetData($modelClass, $relatedID) {
        if (self::RelatedModelClass == $modelClass) {
            return DataObject::get(self::RelatedModelClass)->filter('ShippableID', $relatedID);
        }
    }

    /**
     * Provides editable columns for the extended models related StreakRegionalShipping_Shippable models.
     *
     * @param array $fieldSpecs
     * @return bool
     */
    public function provideEditableColumns(array &$fieldSpecs) {
        if (static::ModelClass == $this->owner->class) {
            $shippingRegions = Region_Shipping::get()->map()->toArray();

            $fieldSpecs += array(
                'RegionID' => array(
                    'title' => 'Region',
                    'callback' => function ($record, $col) use ($shippingRegions) {
                        $field = new Select2Field(
                            'RegionID',
                            '',
                            $shippingRegions,
                            $record ? $record->$col : null
                        );
                        $field->setValue($record->$col);
                        return $field;
                    },
                ),
                'Price' => array(
                    'title' => 'Price',
                    'callback' => function ($record, $col) {
                        return new NumericField(
                            $col,
                            $col,
                            $record ? $record->$col : null
                        );
                    }
                ),
                'ID' => array(
                    'title' => '',
                    'callback' => function ($record, $col) {
                        return new HiddenField(
                            'ID',
                            '',
                            $record ? $record->$col : null
                        );
                    }
                )
            );
            return true;
        }
        return false;
    }

    /**
     * Called when a grid sheet is displaying a model related to another model. e.g. as a grid for a models ItemEditForm
     * in ModelAdmin.
     *
     * @param $relatedModelClass
     * @param $relatedID
     * @param array $fieldSpecs
     * @return mixed
     */
    public function provideRelatedEditableColumns($relatedModelClass, $relatedID, array &$fieldSpecs) {
        if (static::RelatedModelClass == $relatedModelClass) {
            $shippingRegions = Region_Shipping::get()->map()->toArray();

            $fieldSpecs += array(
                'RegionID' => array(
                    'title' => 'Region',
                    'callback' => function ($record, $col) use ($shippingRegions) {
                        $field = new Select2Field(
                            'RegionID',
                            '',
                            $shippingRegions,
                            $record ? $record->$col : null
                        );
                        return $field;
                    },
                ),
                'Price' => array(
                    'title' => 'Price',
                    'callback' => function ($record, $col) {
                        return new NumericField(
                            $col,
                            $col,
                            $record ? $record->$col : null
                        );
                    }
                ),
                'ShippableID' => array(
                    'callback' => function($record) use ($relatedID) {
                        return new HiddenField(
                            'ShippableID',
                            '',
                            $relatedID
                        );
                    }
                )
            );
            return true;
        }
        return false;
    }
    /**
     * Called for each new row in a grid when it is saved.
     *
     * @param $record
     * @return bool
     */
    public function gridSheetHandleNewRow(array &$record) {
        if (self::ModelClass == $this->owner->class) {
            $updateData = $this->getUpdateColumns(
                $this->owner->class,
                $record
            );
            $shippable = new StreakRegionalShipping_ShippableRegion($updateData);

            $this->owner->Shippables()->add($shippable);
        }
    }

    /**
     * Called to each existing row in a grid when it is saved.
     *
     * @param $record
     * @return bool
     */
    public function gridSheetHandleExistingRow(array &$record) {
        if (self::ModelClass == $this->owner->class) {
            $updateData = $this->getUpdateColumns(
                $this->owner->class,
                $record
            );

            $shippable = $this->owner->Shippables()->filter(array(
                'RegionID' => $record['RegionID'],
                'ShippableID' => $record['ShippableID']
            ))->first();


            if (!($shippable && $shippable->exists())) {
                $this->gridSheetHandleNewRow($record);
            } else {
                $shippable->update($updateData);
            }
            $shippable->write();
        }
    }
}
