<?php

return [
    ['name' => 'Settings', 'is_parent' => 1, 'module' => 'Setting', 'slug' => 'settings', 'parent_slug' => '', 'url' => 'setting', 'icon' => 'fas fa-cogs', 'order' => 31, 'permissions' => 'setting.setting.index'],
    ['name' => 'Organisation Settings', 'is_parent' => 0, 'module' => 'Setting', 'slug' => 'organisation_settings', 'parent_slug' => 'settings', 'url' => 'setting/organisation', 'icon' => 'far fa-circle', 'order' => 32, 'permissions' => 'setting.setting.index'],
    ['name' => 'General Settings', 'is_parent' => 0, 'module' => 'Setting', 'slug' => 'general_settings', 'parent_slug' => 'settings', 'url' => 'setting/general', 'icon' => 'far fa-circle', 'order' => 33, 'permissions' => 'setting.setting.index'],
    ['name' => 'Email Settings', 'is_parent' => 0, 'module' => 'Setting', 'slug' => 'email_settings', 'parent_slug' => 'settings', 'url' => 'setting/email', 'icon' => 'far fa-circle', 'order' => 34, 'permissions' => 'setting.setting.index'],
    ['name' => 'SMS Settings', 'is_parent' => 0, 'module' => 'Setting', 'slug' => 'sms_settings', 'parent_slug' => 'settings', 'url' => 'setting/sms', 'icon' => 'far fa-circle', 'order' => 35, 'permissions' => 'setting.setting.index'],
    ['name' => 'System Settings', 'is_parent' => 0, 'module' => 'Setting', 'slug' => 'system_settings', 'parent_slug' => 'settings', 'url' => 'setting/system', 'icon' => 'far fa-circle', 'order' => 36, 'permissions' => 'setting.setting.index'],
    ['name' => 'System Update', 'is_parent' => 0, 'module' => 'Setting', 'slug' => 'system_update', 'parent_slug' => 'settings', 'url' => 'setting/system_update', 'icon' => 'far fa-circle', 'order' => 37, 'permissions' => 'setting.setting.index'],
    ['name' => 'Other Settings', 'is_parent' => 0, 'module' => 'Setting', 'slug' => 'other_settings', 'parent_slug' => 'settings', 'url' => 'setting/other', 'icon' => 'far fa-circle', 'order' => 38, 'permissions' => 'setting.setting.index'],
];