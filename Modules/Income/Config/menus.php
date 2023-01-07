<?php

return [
    ['name' => 'Income', 'is_parent' => 1, 'module' => 'Income', 'slug' => 'income', 'parent_slug' => '', 'url' => 'income', 'icon' => 'fas fa-money-bill', 'order' => 25, 'permissions' => 'income.income.index'],
    ['name' => 'View Income', 'is_parent' => 0, 'module' => 'Income', 'slug' => 'view_assets', 'parent_slug' => 'income', 'url' => 'income', 'icon' => 'far fa-circle', 'order' => 7, 'permissions' => 'income.income.index'],
    ['name' => 'Create Income', 'is_parent' => 0, 'module' => 'Income', 'slug' => 'create_asset', 'parent_slug' => 'income', 'url' => 'income/create', 'icon' => 'far fa-circle', 'order' => 8, 'permissions' => 'income.income.create'],
    ['name' => 'Manage Income Types', 'is_parent' => 0, 'module' => 'Income', 'slug' => 'manage_asset_types', 'parent_slug' => 'income', 'url' => 'income/type', 'icon' => 'far fa-circle', 'order' => 9, 'permissions' => 'income.income.types.index'],
];