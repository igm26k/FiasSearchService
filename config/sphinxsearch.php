<?php
return [
    'host'    => 'sphinxsearch',
    'port'    => 9312,
    'timeout' => 30,
    'indexes' => [
        'idx_fias_object' => ['table' => 'fias_object', 'column' => 'id']
    ]
];
