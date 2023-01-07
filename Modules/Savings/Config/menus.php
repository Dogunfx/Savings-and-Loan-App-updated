<?php

return [
    ['name' => 'Savings', 'is_parent' => 1, 'module' => 'Savings', 'slug' => 'savings', 'parent_slug' => '', 'url' => 'savings', 'icon' => 'fas fa-university', 'order' => 25, 'permissions' => ''],
    ['name' => 'View Savings', 'is_parent' => 0, 'module' => 'Savings', 'slug' => 'view_savings', 'parent_slug' => 'savings', 'url' => 'savings', 'icon' => 'far fa-circle', 'order' => 26, 'permissions' => 'savings.savings.index'],
    ['name' => 'Create Savings', 'is_parent' => 0, 'module' => 'Savings', 'slug' => 'create_savings', 'parent_slug' => 'savings', 'url' => 'savings/create', 'icon' => 'far fa-circle', 'order' => 27, 'permissions' => 'savings.savings.create'],
    ['name' => 'Manage Products', 'is_parent' => 0, 'module' => 'Savings', 'slug' => 'manage_products', 'parent_slug' => 'savings', 'url' => 'savings/product', 'icon' => 'far fa-circle', 'order' => 28, 'permissions' => 'savings.savings.products.index'],
    ['name' => 'Manage Charges', 'is_parent' => 0, 'module' => 'Savings', 'slug' => 'manage_charges', 'parent_slug' => 'savings', 'url' => 'savings/charge', 'icon' => 'far fa-circle', 'order' => 29, 'permissions' => 'savings.savings.charges.index'],
];