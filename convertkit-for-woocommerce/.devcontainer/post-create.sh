#!/usr/bin/env bash

# Install Free Third Party WordPress Plugins 
wp plugin install classic-editor woocommerce

# Install Default WordPress Theme
wp theme install twentytwentyfive

# Symlink Plugin
ln -s /workspaces/convertkit-woocommerce /wp/wp-content/plugins/convertkit-for-woocommerce

# Run Composer in Plugin Directory to build
cd /wp/wp-content/plugins/convertkit-for-woocommerce
composer update

# Activate Plugins
wp plugin activate convertkit-for-woocommerce
wp plugin activate woocommerce