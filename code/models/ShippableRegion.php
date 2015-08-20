<?php

/**
 * Adds many-to-many relationship model linking from e.g. a Product to a Region with a shipping Price.
 *
 */
class StreakRegionalShipping_ShippableRegion extends DataObject {
    private static $db = array(
        'Price' => StreakModule::PriceSchema,
        'Currency' => StreakModule::CurrencySchema
    );
    private static $has_one = array(
        'Shippable' => 'SiteTree',
        'Region' => 'Region'
    );

    private static $summary_fields = array(
        'Shippable.Title' => 'Shippable',
        'Region.Title' => 'Region'
    );

    private static $singular_name = 'Regional Shipping Cost';

    private static $enable_add_new_inline = true;

    public function validate() {
        if (!$this->isInDB()) {
            if (StreakRegionalShipping_ShippableRegion::get()->filter(
                array(
                    'ShippableID' => $this->ShippableID,
                    'RegionID' => $this->RegionID
                )
            )->count()
            ) {
                throw new ValidationException("That region is already set for the Product");
            };
        }
        if (!$this->Price) {
            throw new ValidationException("Please enter a Price");
        }
        return parent::validate();
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (!$this->Currency) {
            $this->Currency = ShopConfig::current_shop_config()->BaseCurrency;
        }
    }

}