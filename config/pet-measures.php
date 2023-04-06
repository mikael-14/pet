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
*       'variation' => -0.1, //perigo se perder mais que -0.1 
*   ],
*/
return [
    
    'weight' => [
        'name' => 'Weight',
        'unit' => 'Kg',
        'variation' => 0.099,
    ],
    // 'height' => [
    //     'name' => 'Height',
    //     'unit' => 'cm',
    //     'variation' => null,
    // ],
]

?>