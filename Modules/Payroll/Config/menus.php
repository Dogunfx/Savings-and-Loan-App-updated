<?php

return [
    ['name' => 'Payroll', 'is_parent' => 1, 'module' => 'Payroll', 'slug' => 'payroll', 'parent_slug' => '', 'url' => 'payroll', 'icon' => 'fab fa-paypal', 'order' => 10, 'permissions' => 'payroll.payroll.index'],
    ['name' => 'View Payroll', 'is_parent' => 0, 'module' => 'Payroll', 'slug' => 'view_payroll', 'parent_slug' => 'payroll', 'url' => 'payroll', 'icon' => 'far fa-circle', 'order' => 11, 'permissions' => 'payroll.payroll.index'],
    ['name' => 'Create Payroll', 'is_parent' => 0, 'module' => 'Payroll', 'slug' => 'create_payroll', 'parent_slug' => 'payroll', 'url' => 'payroll/create', 'icon' => 'far fa-circle', 'order' => 12, 'permissions' => 'payroll.payroll.create'],
    ['name' => 'Manage Payroll Items', 'is_parent' => 0, 'module' => 'Payroll', 'slug' => 'manage_payroll_items', 'parent_slug' => 'payroll', 'url' => 'payroll/item', 'icon' => 'far fa-circle', 'order' => 12, 'permissions' => 'payroll.payroll.items.index'],
    ['name' => 'Manage Payroll Templates', 'is_parent' => 0, 'module' => 'Payroll', 'slug' => 'manage_payroll_templates', 'parent_slug' => 'payroll', 'url' => 'payroll/template', 'icon' => 'far fa-circle', 'order' => 12, 'permissions' => 'payroll.payroll.templates.index'],
];