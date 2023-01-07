<?php

return [
    ['name' => 'Wallets', 'is_parent' => 1, 'module' => 'Wallet', 'slug' => 'wallet', 'parent_slug' => '', 'url' => 'wallet', 'icon' => 'far fa-google-wallet', 'order' => 70, 'permissions' => 'wallet.wallets.index'],
    ['name' => 'View Wallets', 'is_parent' => 0, 'module' => 'Wallet', 'slug' => 'view_wallets', 'parent_slug' => 'wallet', 'url' => 'wallet', 'icon' => 'far fa-circle', 'order' => 1, 'permissions' => 'wallet.wallets.index'],
    ['name' => 'Create Wallet', 'is_parent' => 0, 'module' => 'Wallet', 'slug' => 'create_wallets', 'parent_slug' => 'wallet', 'url' => 'wallet/create', 'icon' => 'far fa-circle', 'order' => 2, 'permissions' => 'wallet.wallets.create'],
 ];