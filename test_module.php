<?php
/**
 * Test script to verify the requestSupport module can be loaded
 * Run this from the HumHub root directory
 */

// Include HumHub bootstrap
require_once __DIR__ . '/../../../protected/config/common.php';
require_once __DIR__ . '/../../../protected/humhub/config/common.php';

// Test module loading
try {
    $moduleClass = 'humhub\modules\requestSupport\Module';
    if (class_exists($moduleClass)) {
        echo "✓ Module class exists: $moduleClass\n";
        
        $module = new $moduleClass('requestSupport');
        echo "✓ Module instance created successfully\n";
        echo "✓ Module name: " . $module->getName() . "\n";
        echo "✓ Module description: " . $module->getDescription() . "\n";
        
        $permissions = $module->getPermissions();
        echo "✓ Module has " . count($permissions) . " permissions defined\n";
        
        echo "\n✅ Module is properly configured and ready to use!\n";
        echo "\nTo enable the module:\n";
        echo "1. Go to Admin Panel > Modules\n";
        echo "2. Find 'Request Support' and click Enable\n";
        echo "3. Clear cache if needed\n";
        
    } else {
        echo "✗ Module class not found: $moduleClass\n";
    }
} catch (Exception $e) {
    echo "✗ Error loading module: " . $e->getMessage() . "\n";
} 