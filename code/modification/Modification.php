<?php

class StreakRegionalShipping_Modification extends Modification {

    private static $db = array(
        'Price' => StreakModule::PriceSchema,
        'Currency' => StreakModule::CurrencySchema
    );

    private static $has_one = array(
        'Region' => 'Region_Shipping'
    );

    private static $defaults = array(
        'SubTotalModifier' => false,
        'SortOrder' => 150
    );

    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (!$this->Currency) {
            $this->Currency = ShopConfig::current_shop_config()->BaseCurrency;
        }
    }

    /**
     * @param Order     $order
     * @param null $value
     * @throws ValidationException
     * @throws null
     */
    public function add($order, $value = null) {
        if ($order->ShippingRegionCode) {
            if ($orderRegion = Region_Shipping::get()->filter('Code', $order->ShippingRegionCode)->first()) {

                $shippingCost = 0.0;


                /** @var Item $item */
                foreach ($order->Items() as $item) {
                    $productRegion = $item->Product()
                        ->Shippables()
                        ->filter('RegionID', $orderRegion->ID)
                        ->first();

                    if ($productRegion) {
                        $shippingCost = Zend_Locale_Math::Add(
                            $shippingCost,
                            Zend_Locale_Math::Mul($productRegion->Price, $item->Quantity)
                        );
                    }
                }
                //Generate the Modification now that we have picked the correct rate
                $mod = new StreakRegionalShipping_Modification();

                $mod->OrderID = $order->ID;
                $mod->RegionID = $orderRegion->ID;
                $mod->Price = $shippingCost;
                $mod->Description = "Shipping to $orderRegion->Title";
                $mod->Value = $shippingCost;
                $mod->write();
            }
        }
    }

    protected function shippingRegionID($code) {
        if ($region = Region_Shipping::get()->filter('Code', $code)->first()) {
            return $region->ID;
        }
    }

    /**
     * Add shipping region code dropdown to form.
     * @return FieldList
     */
    public function getFormFields() {
        $fields = new FieldList();

        if ($shippingRegion = $this->Region()) {
            $field = new StreakRegionShippingModifierField(
                $this,
                $this->Description,
                $shippingRegion->ID
            );
            /** @var Price $price */
            $price = Price::create();
            $price->setAmount($this->Price);

            $field->setAmount($price);
            $fields->push($field);
        };

        if (!$fields->exists()) {
            Requirements::javascript('swipestreak-regionalshipping/javascript/RegionalShippingModifierField.js');
        }


        return $fields;
    }
}