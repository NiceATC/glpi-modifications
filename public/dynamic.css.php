<?php

/**
 * -------------------------------------------------------------------------
 * UI Branding plugin for GLPI - Dynamic CSS Generator
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of UI Branding plugin for GLPI.
 *
 * "UI Branding plugin for GLPI" is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * "UI Branding plugin for GLPI" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with "UI Branding plugin for GLPI". If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2025 by i-Vertix/PGUM.
 * @license   GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * @link      https://github.com/i-Vertix/glpi-modifications
 * -------------------------------------------------------------------------
 */

// Define GLPI_ROOT if not already defined
if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
}

// Include GLPI
include_once(GLPI_ROOT . "/inc/includes.php");

use GlpiPlugin\Mod\BrandManager;

// Set proper headers for CSS
header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=3600'); // Cache for 1 hour

// Generate dynamic CSS variables
echo BrandManager::generateCSSVariables();

// Add any additional dynamic styles based on settings
$settings = BrandManager::getModernUISettings();

// Dynamic CSS based on settings
if ($settings['theme'] !== 'default' || BrandManager::isModernUIEnabled()) {
    echo "
/* Dynamic styles applied */
body." . BrandManager::generateBodyClasses() . " .welcome-anonymous {
    transition: all 0.3s ease;
}
";
}

// Custom primary color adjustments
if ($settings['primary_color'] !== '#2563eb') {
    echo "
.mod-modern-buttons .btn-primary {
    background: linear-gradient(135deg, " . $settings['primary_color'] . ", " . $settings['secondary_color'] . ") !important;
    box-shadow: 0 8px 25px " . hex2rgba($settings['primary_color'], 0.3) . " !important;
}

.mod-modern-buttons .btn-primary:hover {
    box-shadow: 0 12px 35px " . hex2rgba($settings['primary_color'], 0.4) . " !important;
}

.mod-modern-inputs .form-control:focus {
    border-color: " . $settings['accent_color'] . " !important;
    box-shadow: 0 0 0 4px " . hex2rgba($settings['accent_color'], 0.1) . " !important;
}
";
}

/**
 * Convert hex color to rgba
 */
function hex2rgba($hex, $alpha = 1) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "rgba($r, $g, $b, $alpha)";
}