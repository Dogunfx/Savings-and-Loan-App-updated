<?php

return [
    ['name' => 'Custom Fields', 'is_parent' => 1, 'module' => 'CustomField', 'slug' => 'custom_fields', 'parent_slug' => '', 'url' => 'custom_field', 'icon' => 'fas fa-list', 'order' => 25, 'permissions' => ''],
    ['name' => 'View Custom Fields', 'is_parent' => 0, 'module' => 'Savings', 'slug' => 'view_custom_fields', 'parent_slug' => 'custom_fields', 'url' => 'custom_field', 'icon' => 'far fa-circle', 'order' => 26, 'permissions' => 'customfield.custom_fields.index'],
    ['name' => 'Create Custom Field', 'is_parent' => 0, 'module' => 'Savings', 'slug' => 'create_custom_fields', 'parent_slug' => 'custom_fields', 'url' => 'custom_field/create', 'icon' => 'far fa-circle', 'order' => 27, 'permissions' => 'customfield.custom_fields.create'],
];