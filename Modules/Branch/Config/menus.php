<?php

return [
    ['name' => 'Branches', 'is_parent' => 1, 'module' => 'Branch', 'slug' => 'branches', 'parent_slug' => '', 'url' => 'branch', 'icon' => 'fas fa-building', 'order' => 6, 'permissions' => ''],
    ['name' => 'View Branches', 'is_parent' => 0, 'module' => 'Branch', 'slug' => 'view_branches', 'parent_slug' => 'branches', 'url' => 'branch', 'icon' => 'far fa-circle', 'order' => 7, 'permissions' => 'branch.branches.index'],
    ['name' => 'Create Branch', 'is_parent' => 0, 'module' => 'Branch', 'slug' => 'create_branch', 'parent_slug' => 'branches', 'url' => 'branch/create', 'icon' => 'far fa-circle', 'order' => 8, 'permissions' => 'branch.branches.create'],
];