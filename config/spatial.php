<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Spatial Competitor Radius
    |--------------------------------------------------------------------------
    |
    | Defines the default radius (in kilometers) to search for competitors
    | around a given coordinate.
    |
    */
    'competitor_radius' => env('SPATIAL_COMPETITOR_RADIUS', 5),

    /*
    |--------------------------------------------------------------------------
    | Dataset Paths
    |--------------------------------------------------------------------------
    |
    | The file paths to the CSV datasets used for importing spatial records.
    | These paths are relative to the application's base directory.
    |
    */
    'dataset_competitors' => env('SPATIAL_DATASET_COMPETITORS', 'dataset/data_pesaing_aqiqah_jabar.csv'),
    
    'dataset_rph' => env('SPATIAL_DATASET_RPH', 'dataset/data_rph_jabar.csv'),
];
