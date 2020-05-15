<?php

return [

    'model' => [
        'class'              => Template::class,
        'templateNameColumn' => 'name',
        'contentField'       => 'content',
        'expires'            => 'updated_at',
    ],

];