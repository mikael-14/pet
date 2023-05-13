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
        'name' => 'Cleaning Volunteer',
        'status' => true
    ],
    'driver_volunteer' => [
        'name' => 'Driver Volunteer',
        'status' => true
    ],
    'medication_volunteer' => [
        'name' => 'Medication Volunteer',
        'status' => true
    ],
    'temporary_family' => [
        'name' => 'Temporary Host Family',
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
        'name' => 'Black List',
        'status' => true
    ],
]

?>