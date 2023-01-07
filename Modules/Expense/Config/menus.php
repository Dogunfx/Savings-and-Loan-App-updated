<?php

return [
    ['name' => 'Expenses', 'is_parent' => 1, 'module' => 'Expense', 'slug' => 'expenses', 'parent_slug' => '', 'url' => 'expense', 'icon' => 'fas fa-share', 'order' => 20, 'permissions' => 'expense.expenses.index'],
    ['name' => 'View Expenses', 'is_parent' => 0, 'module' => 'Expense', 'slug' => 'view_expenses', 'parent_slug' => 'expenses', 'url' => 'expense', 'icon' => 'far fa-circle', 'order' => 0, 'permissions' => 'expense.expenses.index'],
    ['name' => 'Create Expense', 'is_parent' => 0, 'module' => 'Expense', 'slug' => 'create_expense', 'parent_slug' => 'expenses', 'url' => 'expense/create', 'icon' => 'far fa-circle', 'order' => 1, 'permissions' => 'expense.expenses.create'],
    ['name' => 'Manage Expense Types', 'is_parent' => 0, 'module' => 'Expense', 'slug' => 'manage_expense_types', 'parent_slug' => 'expenses', 'url' => 'expense/type', 'icon' => 'far fa-circle', 'order' => 2, 'permissions' => 'expense.expenses.types.index'],
];