<?php 
/*
* values for the flags on person
*
* TODO: maybe instead array of configs, set in table 
*
*   Example:
* 
*   'unique key' =>  [
*       'name' => 'FAT',
*       'status' => true|false, //default true, if key doesnt exist
*   ],
*/
return [
    'cleaning_volunteer' => [
        'name' => 'Cleaning volunteer',
        'status' => true
    ],
    'driver_volunteer' => [
        'name' => 'Driver volunteer',
        'status' => true
    ],
    'medication_volunteer' => [
        'name' => 'Medication volunteer',
        'status' => true
    ],
    'temporary_family' => [
        'name' => 'Temporary host family',
        'status' => true
    ],
    'veterinary' => [
        'name' => 'Veterinary',
        'status' => true
    ],
    'adopter' => [
        'name' => 'Adopter',
        'status' => true
    ],
    'sponsor' => [
        'name' => 'Sponsor',
        'status' => true
    ],
    'black_list' => [
        'name' => 'Black list',
        'status' => true
    ],
]

?>