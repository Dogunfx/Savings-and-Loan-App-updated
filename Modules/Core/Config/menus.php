<?php

return [
    ['name' => 'Manage Modules', 'is_parent' => 1, 'module' => 'Core', 'slug' => 'modules', 'parent_slug' => '', 'url' => 'module', 'icon' => 'fas fa-plug', 'order' => 30, 'permissions' => 'core.modules.index'],
    ['name' => 'Manage Menu', 'is_parent' => 1, 'module' => 'Core', 'slug' => 'menu', 'parent_slug' => '', 'url' => 'menu', 'icon' => 'fas fa-bars', 'order' => 31, 'permissions' => 'core.menu.index'],
    ['name' => 'Payment Gateways', 'is_parent' => 0, 'module' => 'Core', 'slug' => 'menu', 'parent_slug' => 'settings', 'url' => 'settings/payment_gateway', 'icon' => 'fas fa-money-bill', 'order' => 32, 'permissions' => 'core.payment_gateways.index'],
    ['name' => 'Themes', 'is_parent' => 1, 'module' => 'Core', 'slug' => 'themes', 'parent_slug' => '', 'url' => 'theme', 'icon' => 'fas fa-palette', 'order' => 40, 'permissions' => 'core.themes.index'],
];