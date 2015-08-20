<?php

class StreakRegionalShipping_OrderExtension extends CrackerJackDataExtension {
    public function updateOrderEditForm(FieldList $fields) {
        $fields->push(
            new Select2Field(
                'ShippingRegionCode',
                'Shipping Region',
                Region_Shipping::get()->map('Code', 'Title'),
                $this->owner->ShippingRegionCode
            )
        );
    }
}