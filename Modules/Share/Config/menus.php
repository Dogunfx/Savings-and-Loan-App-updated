<?php

return [
    ['name' => 'Shares', 'is_parent' => 1, 'module' => 'Share', 'slug' => 'shares', 'parent_slug' => '', 'url' => 'share', 'icon' => 'fas fa-database', 'order' => 30, 'permissions' => 'share.shares.index'],
    ['name' => 'View Shares', 'is_parent' => 0, 'module' => 'Share', 'slug' => 'view_shares', 'parent_slug' => 'shares', 'url' => 'share', 'icon' => 'far fa-circle', 'order' => 1, 'permissions' => 'share.shares.index'],
    ['name' => 'Create Share', 'is_parent' => 0, 'module' => 'Share', 'slug' => 'create_share', 'parent_slug' => 'shares', 'url' => 'share/create', 'icon' => 'far fa-circle', 'order' => 2, 'permissions' => 'share.shares.create'],
    ['name' => 'Manage Products', 'is_parent' => 0, 'module' => 'Share', 'slug' => 'manage_share_products', 'parent_slug' => 'shares', 'url' => 'share/product', 'icon' => 'far fa-circle', 'order' => 3, 'permissions' => 'share.shares.products.index'],
    ['name' => 'Manage Charges', 'is_parent' => 0, 'module' => 'Share', 'slug' => 'manage_share_charges', 'parent_slug' => 'shares', 'url' => 'share/charge', 'icon' => 'far fa-circle', 'order' => 3, 'permissions' => 'share.shares.charges.index'],
];