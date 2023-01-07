<?php

return [
    ['name' => 'Users', 'is_parent' => 1, 'module' => 'User', 'slug' => 'users', 'parent_slug' => '', 'url' => 'user', 'icon' => 'fas fa-users', 'order' => 27, 'permissions' => ''],
    ['name' => 'View Users', 'is_parent' => 0, 'module' => 'User', 'slug' => 'view_loans', 'parent_slug' => 'users', 'url' => 'user', 'icon' => 'far fa-circle', 'order' => 28, 'permissions' => 'user.users.index'],
    ['name' => 'Create User', 'is_parent' => 0, 'module' => 'User', 'slug' => 'create_loan', 'parent_slug' => 'users', 'url' => 'user/create', 'icon' => 'far fa-circle', 'order' => 29, 'permissions' => 'user.users.create'],
    ['name' => 'Manage Roles', 'is_parent' => 0, 'module' => 'User', 'slug' => 'manage_roles', 'parent_slug' => 'users', 'url' => 'user/role', 'icon' => 'far fa-circle', 'order' => 30, 'permissions' => 'user.roles.index'],
];