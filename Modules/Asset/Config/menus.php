<?php

return [
    ['name' => 'Assets', 'is_parent' => 1, 'module' => 'Asset', 'slug' => 'assets', 'parent_slug' => '', 'url' => 'asset', 'icon' => 'fas fa-building', 'order' => 30, 'permissions' => 'asset.assets.index'],
    ['name' => 'View Assets', 'is_parent' => 0, 'module' => 'Asset', 'slug' => 'view_assets', 'parent_slug' => 'assets', 'url' => 'asset', 'icon' => 'far fa-circle', 'order' => 7, 'permissions' => 'asset.assets.index'],
    ['name' => 'Create Asset', 'is_parent' => 0, 'module' => 'Asset', 'slug' => 'create_asset', 'parent_slug' => 'assets', 'url' => 'asset/create', 'icon' => 'far fa-circle', 'order' => 8, 'permissions' => 'asset.assets.create'],
    ['name' => 'Manage Asset Types', 'is_parent' => 0, 'module' => 'Asset', 'slug' => 'manage_asset_types', 'parent_slug' => 'assets', 'url' => 'asset/type', 'icon' => 'far fa-circle', 'order' => 9, 'permissions' => 'asset.assets.types.index'],
];