<?php
/**
 * DEBUG FILE - Check if Modern UI is working
 * Access: /plugins/mod/public/test_modern_ui.php
 */

// Define GLPI_ROOT if not already defined
if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
}

include_once(GLPI_ROOT . "/inc/includes.php");

use GlpiPlugin\Mod\BrandManager;

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html><head><title>Modern UI Debug</title></head><body>";
echo "<h1>🔍 Modern UI Branding - Debug Information</h1>";

echo "<h2>1. Plugin Status</h2>";
echo "<p><strong>Plugin Active:</strong> " . (Plugin::isPluginActive("mod") ? "✅ YES" : "❌ NO") . "</p>";

echo "<h2>2. Login Page Settings</h2>";
echo "<p><strong>Login Page Modified:</strong> " . (BrandManager::isLoginPageModified() ? "✅ YES" : "❌ NO") . "</p>";
echo "<p><strong>Modern UI Enabled:</strong> " . (BrandManager::isModernUIEnabled() ? "✅ YES" : "❌ NO") . "</p>";

echo "<h2>3. Current Settings</h2>";
$settings = BrandManager::getModernUISettings();
echo "<pre>";
print_r($settings);
echo "</pre>";

echo "<h2>4. Generated CSS Classes</h2>";
echo "<p><code>" . BrandManager::generateBodyClasses() . "</code></p>";

echo "<h2>5. Generated CSS Variables</h2>";
echo "<pre>" . htmlspecialchars(BrandManager::generateCSSVariables()) . "</pre>";

echo "<h2>6. Available Themes</h2>";
echo "<pre>";
print_r(BrandManager::AVAILABLE_THEMES);
echo "</pre>";

echo "<h2>7. Available Layouts</h2>";
echo "<pre>";
print_r(BrandManager::AVAILABLE_LAYOUTS);
echo "</pre>";

echo "<h2>8. Files Check</h2>";
$files = [
    'BrandManager.php' => GLPI_ROOT . '/plugins/mod/src/BrandManager.php',
    'UIBranding.php' => GLPI_ROOT . '/plugins/mod/src/UIBranding.php',
    'mod_anonymous.css' => GLPI_ROOT . '/plugins/mod/public/css/mod_anonymous.css',
    'mod_responsive.css' => GLPI_ROOT . '/plugins/mod/public/css/mod_responsive.css',
    'dynamic.css.php' => GLPI_ROOT . '/plugins/mod/public/dynamic.css.php',
    'modifiers.ini' => BrandManager::FILES_DIR . '/modifiers.ini',
];

foreach ($files as $name => $path) {
    $exists = file_exists($path);
    $readable = $exists && is_readable($path);
    echo "<p><strong>$name:</strong> " . 
         ($exists ? "✅ EXISTS" : "❌ NOT FOUND") . 
         ($readable ? " | ✅ READABLE" : ($exists ? " | ❌ NOT READABLE" : "")) . 
         "</p>";
}

echo "<h2>9. CSS Files URLs (Test these manually)</h2>";
echo "<ul>";
echo "<li><a href='/plugins/mod/public/css/mod_anonymous.css' target='_blank'>mod_anonymous.css</a></li>";
echo "<li><a href='/plugins/mod/public/css/mod_responsive.css' target='_blank'>mod_responsive.css</a></li>";
echo "<li><a href='/plugins/mod/public/dynamic.css.php' target='_blank'>dynamic.css.php</a></li>";
echo "</ul>";

echo "<h2>10. Next Steps</h2>";
echo "<ol>";
echo "<li>Make sure 'Show login background' is set to YES in the plugin settings</li>";
echo "<li>OR make sure at least one Modern UI feature is enabled</li>";
echo "<li>Select a LAYOUT (not 'default') - try 'Split Screen' or 'Centered Card'</li>";
echo "<li>Clear GLPI cache: Setup → General → Cache → Clear cache</li>";
echo "<li>Clear browser cache (Ctrl+Shift+Delete)</li>";
echo "<li>Test login page in incognito/private mode</li>";
echo "<li>Check browser console (F12) for CSS loading errors</li>";
echo "<li><strong>Look for colored debug badge on login page (top-left)</strong></li>";
echo "</ol>";

echo "<h2>11. Quick Layout Test</h2>";
echo "<p>Open the login page and check:</p>";
echo "<ul>";
echo "<li><strong>Split Screen:</strong> Should see RED badge + background on left, form on right</li>";
echo "<li><strong>Full Width:</strong> Should see GREEN badge + full background with centered form</li>";
echo "<li><strong>Corner:</strong> Should see BLUE badge + form in top-right corner</li>";
echo "<li><strong>Centered:</strong> Should see PURPLE badge + centered card with animation</li>";
echo "</ul>";

echo "<p style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
echo "<strong>⚠️ Important:</strong> If you don't see the colored badge, the CSS classes are NOT being applied to the body!";
echo "</p>";

echo "</body></html>";
?>