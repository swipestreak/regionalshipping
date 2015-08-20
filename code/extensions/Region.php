<?php

/**
 * Adds has_many relationship from a Region to a ShippableRegion and so a Shippable.
 */
class StreakRegionalShipping_RegionExtension extends CrackerJackDataExtension {
    private static $has_many = array(
        'Shippables' => 'StreakRegionalShipping_ShippableRegion.Region',
        'StreakRegionalShippingModification' => 'StreakRegionalShipping_Modification'
    );
}