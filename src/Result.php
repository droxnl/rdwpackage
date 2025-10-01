<?php

namespace Drox\Rdw;

use Illuminate\Support\Arr;

/**
 * @property string|null $registration_number
 * @property string|null $vehicle_type
 * @property string|null $brand
 * @property string|null $trade_name
 * @property string|null $expiry_date_mot
 * @property string|null $configuration
 * @property string|null $number_of_seats
 * @property string|null $first_color
 * @property string|null $second_color
 * @property string|null $number_of_cylinders
 * @property string|null $cylinder_capacity
 * @property string|null $mass_empty_vehicle
 * @property string|null $max_permitted_vehicle_mass
 * @property string|null $ready_to_drive_mass
 * @property string|null $max_unbraked_towing_mass
 * @property string|null $max_braked_towing_mass
 * @property string|null $date_first_admission
 * @property string|null $date_first_registration_nl
 * @property string|null $awaiting_inspection
 * @property string|null $list_price
 * @property string|null $wam_insured
 * @property string|null $number_of_doors
 * @property string|null $number_of_wheels
 * @property string|null $distance_coupling_to_rear
 * @property string|null $distance_front_to_coupling
 * @property string|null $length
 * @property string|null $width
 * @property string|null $european_vehicle_category
 * @property string|null $chassis_number_location
 * @property string|null $technical_max_vehicle_mass
 * @property string|null $type
 * @property string|null $type_approval_number
 * @property string|null $variant
 * @property string|null $execution
 * @property string|null $eu_type_approval_change_sequence
 * @property string|null $power_ready_to_drive
 * @property string|null $wheelbase
 * @property string|null $export_indicator
 * @property string|null $outstanding_recall_indicator
 * @property string|null $taxi_indicator
 * @property string|null $max_combination_weight
 * @property string|null $number_of_wheelchair_spaces
 * @property string|null $max_supportive_speed
 * @property string|null $year_last_reg_mileage
 * @property string|null $mileage_judgment
 * @property string|null $mileage_judgment_code
 * @property string|null $possible_registration_holder
 * @property string|null $expiry_date_mot_dt
 * @property string|null $date_first_admission_dt
 * @property string|null $date_first_registration_nl_dt
 * @property string|null $efficiency_classification
 * @property string|null $api_registered_vehicle_axes
 * @property string|null $api_registered_vehicle_fuel
 * @property string|null $api_registered_vehicle_body
 * @property string|null $api_registered_vehicle_body_specific
 * @property string|null $api_registered_vehicle_class
 * @property string|null $fuel_sequence_number
 * @property string|null $fuel_description
 * @property string|null $fuel_consumption_outside
 * @property string|null $fuel_consumption_combined
 * @property string|null $fuel_consumption_urban
 * @property string|null $co2_emissions_combined
 * @property string|null $idle_noise_level
 * @property string|null $emission_code_description
 * @property string|null $environmental_class_eu_light
 * @property string|null $light_particle_emissions
 * @property string|null $net_max_power
 * @property string|null $soot_emissions
 * @property string|null $noise_level_speed
 * @property string|null $exhaust_emission_level
 * @property string|null $eu_type_approval_key
 * @property string|null $eec_variant_code
 * @property string|null $eec_execution_code
 * @property string|null $execution_modification_number
 * @property string|null $sequence_number
 * @property string|null $gearbox_type
 * @property string|null $lower_gear_limits
 * @property string|null $upper_gear_limits
 */
class Result
{
    /** @var array $data */
    private $data = [];

    /** @var array $fuelTypes */
    private $fuelTypes = [];

    /**
     * @param array $rawData
     * @param array $rawFuelTypes
     */
    public function __construct(array $rawData, array $rawFuelTypes)
    {
        $translation = require(__DIR__ . '/Translation/en.php');

        foreach($rawData as $key => $value) {
            if (isset($translation[$key])) {
                $this->data[$translation[$key]] = $value;
            }
        }

        foreach($rawFuelTypes as $fuelTypeKey => $fielType) {
            foreach ($fielType as $key => $value) {
                if (isset($translation[$key])) {
                    $this->fuelTypes[$fuelTypeKey][$translation[$key]] = $value;
                }
            }
        }

        if (in_array('Benzine', $this->fuelTypes()) && in_array('Elektriciteit', $this->fuelTypes())) {
            $this->fuel_description = 'Hybride';
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->data, [
            'fuels' => $this->fuelTypes,
        ]);
    }

    /**
     * @return array
     */
    public function fuels(): array
    {
        return $this->fuelTypes;
    }

    /**
     * @return array
     */
    public function fuelTypes(): array
    {
        return Arr::pluck($this->fuelTypes, 'fuel_description');
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return data_get($this->data, $key);
    }

    /**
     * @param $key
     * @return bool
     */
    function __isset($key)
    {
        return isset($this->data[$key]);
    }
}


