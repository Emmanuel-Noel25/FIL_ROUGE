<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie','prestataire/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173'], // Remplacez avec votre URL frontend
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Important pour Sanctum
];