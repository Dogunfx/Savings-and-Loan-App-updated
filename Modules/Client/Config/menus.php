<?php

return [
    ['name' => 'Clients', 'is_parent' => 1, 'module' => 'Client', 'slug' => 'clients', 'parent_slug' => '', 'url' => 'client', 'icon' => 'fas fa-users', 'order' => 10, 'permissions' => ''],
    ['name' => 'View Clients', 'is_parent' => 0, 'module' => 'Client', 'slug' => 'view_clients', 'parent_slug' => 'clients', 'url' => 'client', 'icon' => 'far fa-circle', 'order' => 11, 'permissions' => 'client.clients.index'],
    ['name' => 'Create Client', 'is_parent' => 0, 'module' => 'Client', 'slug' => 'create_client', 'parent_slug' => 'clients', 'url' => 'client/create', 'icon' => 'far fa-circle', 'order' => 12, 'permissions' => 'client.clients.create'],
];