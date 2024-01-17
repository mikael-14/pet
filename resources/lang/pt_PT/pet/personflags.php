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
        'name' => 'Voluntário Limpeza',
        'status' => true
    ],
    'driver_volunteer' => [
        'name' => 'Voluntário transporte',
        'status' => true
    ],
    'medication_volunteer' => [
        'name' => 'Voluntário Medicação',
        'status' => true
    ],
    'temporary_family' => [
        'name' => 'Família de Acolhimento Temporário',
        'status' => true
    ],
    'veterinary' => [
        'name' => 'Veterinário',
        'status' => true
    ],
    'adopter' => [
        'name' => 'Adotante',
        'status' => true
    ],
    'sponsor' => [
        'name' => 'Padrinho',
        'status' => true
    ],
    'black_list' => [
        'name' => 'Black list',
        'status' => true
    ],
]

?>