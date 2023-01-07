<?php

return [
    ['name' => 'Communication', 'is_parent' => 1, 'module' => 'Communication', 'slug' => 'communication', 'parent_slug' => '', 'url' => 'communication', 'icon' => 'fas fa-envelope', 'order' => 13, 'permissions' => ''],
    ['name' => 'View Campaigns', 'is_parent' => 0, 'module' => 'Communication', 'slug' => 'view_campaigns', 'parent_slug' => 'communication', 'url' => 'communication/campaign', 'icon' => 'far fa-circle', 'order' => 14, 'permissions' => 'communication.campaigns.index'],
    ['name' => 'Create Campaign', 'is_parent' => 0, 'module' => 'Communication', 'slug' => 'create_campaign', 'parent_slug' => 'communication', 'url' => 'communication/campaign/create', 'icon' => 'far fa-circle', 'order' => 15, 'permissions' => 'communication.campaigns.create'],
    ['name' => 'View Logs', 'is_parent' => 0, 'module' => 'Communication', 'slug' => 'view_logs', 'parent_slug' => 'communication', 'url' => 'communication/log', 'icon' => 'far fa-circle', 'order' => 16, 'permissions' => 'communication.logs.index'],
    ['name' => 'Manage SMS Gateways', 'is_parent' => 0, 'module' => 'Communication', 'slug' => 'manage_sms_gateways', 'parent_slug' => 'communication', 'url' => 'communication/sms_gateway', 'icon' => 'far fa-circle', 'order' => 17, 'permissions' => 'communication.sms_gateways.index'],
];