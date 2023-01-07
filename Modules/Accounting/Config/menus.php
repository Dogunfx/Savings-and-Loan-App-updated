<?php

return [
    ['name' => 'Accounting', 'is_parent' => 1, 'module' => 'Accounting', 'slug' => 'accounting', 'parent_slug' => '', 'url' => 'accounting', 'icon' => 'fas fa-money-bill', 'order' => 3, 'permissions' => ''],
    ['name' => 'View Charts of Accounts', 'is_parent' => 0, 'module' => 'Accounting', 'slug' => 'view_charts_of_accounts', 'parent_slug' => 'accounting', 'url' => 'accounting/chart_of_account', 'icon' => 'far fa-circle', 'order' => 4, 'permissions' => 'accounting.chart_of_accounts.index'],
    ['name' => 'Journal Entries', 'is_parent' => 0, 'module' => 'Accounting', 'slug' => 'journal_entries', 'parent_slug' => 'accounting', 'url' => 'accounting/journal_entry', 'icon' => 'far fa-circle', 'order' => 5, 'permissions' => 'accounting.journal_entries.index'],
];