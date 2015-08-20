<?php

/**
 * Extension to OrderForm adds ShippingRegionCode and BillingRegionCode fields.
 */
class StreakRegionalShipping_OrderFormExtension extends Extension {
    public function updateFields(FieldList $fields) {
        /** @var CompositeField $shippingAddressFields */
        if ($shippingAddressFields = $fields->fieldByName('ShippingAddress')) {
            /** @var DropdownField $countryCodeField */
            if ($countryCodeField = $shippingAddressFields->fieldByName('ShippingCountryCode')) {
                $countryCodeField->setReadonly(true);

                $shippingAddressFields->insertBefore(
                    DropdownField::create(
                        'ShippingRegionCode',
                        _t('CheckoutPage.REGION', 'Region'),
                        Region_Shipping::get()->map('Code', 'Title')->toArray()
                    )->setCustomValidationMessage(
                        _t('CheckoutPage.PLEASE_ENTER_REGION',"Please enter a shipping region.")
                    )->addExtraClass('region-code'),
                    'ShippingCountryCode'
                );
            }
        }

    }
}