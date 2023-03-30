<?php 
/*
* values for the table (measures)
*
* TODO: maybe instead array of configs, set in table 
*
*   Example:
* 
*   'unique key' =>  [
*       'name' => 'Weight',
*       'unit' => 'Kg',
*       'variation' => 0.1,
*   ],
*/
return [
    
    'weight' => [
        'name' => 'Weight',
        'unit' => 'Kg',
        'variation' => 0.1,
    ],
    'height' => [
        'name' => 'height',
        'unit' => 'cm',
        'variation' => null,
    ],
]

?>