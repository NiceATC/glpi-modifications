<?php

/**
 * -------------------------------------------------------------------------
 * UI Branding plugin for GLPI
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

namespace GlpiPlugin\Mod;

use Glpi\Application\View\TemplateRenderer;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class UIBranding
{

    /**
     * @param array $data
     * @param array $files
     * @return void
     */
    public function save(array $data, array $files): void
    {
        $brandManager = new BrandManager();
        $backgroundChanged = false;
        $logosChanged = false;
        $faviconChanged = false;

        if (isset($files['background']['name']) && $files['background']['name'] !== '') {
            $backgroundChanged = $brandManager->uploadResource("background", $files['background']);
        }
        if (isset($files['logo_s']['name']) && $files['logo_s']['name'] !== '') {
            $logosChanged = $brandManager->uploadResource("logo_s", $files['logo_s']);
        }
        if (isset($files['logo_m']['name']) && $files['logo_m']['name'] !== '') {
            $logosChanged = $brandManager->uploadResource("logo_m", $files['logo_m']);
        }
        if (isset($files['logo_l']['name']) && $files['logo_l']['name'] !== '') {
            $logosChanged = $brandManager->uploadResource("logo_l", $files['logo_l']);
        }

        if (isset($files['favicon']['name']) && $files['favicon']['name'] !== '') {
            $faviconChanged = $brandManager->uploadResource("favicon", $files['favicon']);
        }

        if (isset($data['show_background'])) {
            if ($data['show_background'] === '1') {
                // overwrite background if changed or not overwritten yet
                if ($backgroundChanged || !$brandManager::isLoginPageModified()) {
                    $brandManager->applyResource("background");
                }
                $brandManager->applyLoginPageModifier();
            } else if ($brandManager::isLoginPageModified()) {
                $brandManager->restoreResource("background");
                $brandManager->disableLoginPageModifier();
            }
        } else if ($backgroundChanged && $brandManager::isLoginPageModified()) {
            $brandManager->applyResource("background");
        }

        if (isset($data['show_custom_logos'])) {
            if ($data['show_custom_logos'] === '1') {
                // overwrite background if changed or not overwritten yet
                if (
                    $logosChanged
                    || !$brandManager::isActiveResourceModified("logo_s")
                    || !$brandManager::isActiveResourceModified("logo_m")
                    || !$brandManager::isActiveResourceModified("logo_l")
                ) {
                    $brandManager->applyResource("logo_s");
                    $brandManager->applyResource("logo_m");
                    $brandManager->applyResource("logo_l");
                }
            } else if (
                $brandManager::isActiveResourceModified("logo_s")
                || $brandManager::isActiveResourceModified("logo_m")
                || $brandManager::isActiveResourceModified("logo_l")
            ) {
                $brandManager->restoreResource("logo_s");
                $brandManager->restoreResource("logo_m");
                $brandManager->restoreResource("logo_l");
            }
        } else if ($logosChanged) {
            $brandManager->applyResource("logo_s");
            $brandManager->applyResource("logo_m");
            $brandManager->applyResource("logo_l");
        }

        if (isset($data['show_custom_favicon'])) {
            if ($data['show_custom_favicon'] === '1') {
                // overwrite background if changed or not overwritten yet
                if ($faviconChanged || !$brandManager::isActiveResourceModified("favicon")) {
                    $brandManager->applyResource("favicon");
                }
            } else if ($brandManager::isActiveResourceModified("favicon")) {
                $brandManager->restoreResource("favicon");
            }
        } else if ($faviconChanged && $brandManager::isActiveResourceModified("favicon")) {
            $brandManager->applyResource("favicon");
        }

        if (isset($data['title'])) {
            $brandManager->changeTitle($data["title"]);
        }

        // Save modern UI settings
        $modernUISettings = [
            'theme' => $data['theme'] ?? 'default',
            'layout' => $data['layout'] ?? 'default', 
            'animation' => $data['animation'] ?? 'fade',
            'enable_modern_inputs' => $data['enable_modern_inputs'] ?? '0',
            'enable_floating_labels' => $data['enable_floating_labels'] ?? '0',
            'enable_modern_buttons' => $data['enable_modern_buttons'] ?? '0',
            'enable_logo_effects' => $data['enable_logo_effects'] ?? '0',
            'enable_particles' => $data['enable_particles'] ?? '0',
            'primary_color' => $data['primary_color'] ?? '#2563eb',
            'secondary_color' => $data['secondary_color'] ?? '#1e40af',
            'accent_color' => $data['accent_color'] ?? '#3b82f6',
            'border_radius' => $data['border_radius'] ?? '16',
            'blur_intensity' => $data['blur_intensity'] ?? '20',
            'video_background_url' => $data['video_background_url'] ?? '',
            'attribution_text' => $data['attribution_text'] ?? '',
            
            // Interactive panel
            'panel_enabled' => $data['panel_enabled'] ?? '1',
            'panel_image_url' => $data['panel_image_url'] ?? '',
            'panel_title' => $data['panel_title'] ?? 'Bem-vindo!',
            'panel_message' => $data['panel_message'] ?? '',
            'panel_show_notifications' => $data['panel_show_notifications'] ?? '1',
            'panel_show_events' => $data['panel_show_events'] ?? '1',
            'panel_show_countdown' => $data['panel_show_countdown'] ?? '0',
            'panel_countdown_date' => $data['panel_countdown_date'] ?? '',
            'panel_countdown_text' => $data['panel_countdown_text'] ?? '',
            'panel_notifications' => $data['panel_notifications'] ?? '',
            'panel_events' => $data['panel_events'] ?? ''
        ];

        $brandManager->saveModernUISettings($modernUISettings);
    }

    /**
     * @return bool
     */
    public function display(): bool
    {
        global $CFG_GLPI;
        $modernSettings = BrandManager::getModernUISettings();
        
        TemplateRenderer::getInstance()->display('@mod/uibranding.html.twig', [
            "url" => $CFG_GLPI['root_doc'] . "/plugins/mod/front/uibranding.php",
            "show_background" => BrandManager::isLoginPageModified(),
            "show_custom_logos" => BrandManager::isActiveResourceModified("logo_s")
                || BrandManager::isActiveResourceModified("logo_m")
                || BrandManager::isActiveResourceModified("logo_l"),
            "show_custom_favicon" => BrandManager::isActiveResourceModified("favicon"),
            "title" => BrandManager::getCurrentTitle(),
            
            // Modern UI settings
            "available_themes" => BrandManager::AVAILABLE_THEMES,
            "available_layouts" => BrandManager::AVAILABLE_LAYOUTS,
            "available_animations" => BrandManager::AVAILABLE_ANIMATIONS,
            
            // Current settings
            "theme" => $modernSettings['theme'],
            "layout" => $modernSettings['layout'],
            "animation" => $modernSettings['animation'],
            "enable_modern_inputs" => $modernSettings['enable_modern_inputs'],
            "enable_floating_labels" => $modernSettings['enable_floating_labels'],
            "enable_modern_buttons" => $modernSettings['enable_modern_buttons'],
            "enable_logo_effects" => $modernSettings['enable_logo_effects'],
            "enable_particles" => $modernSettings['enable_particles'],
            "primary_color" => $modernSettings['primary_color'],
            "secondary_color" => $modernSettings['secondary_color'],
            "accent_color" => $modernSettings['accent_color'],
            "border_radius" => $modernSettings['border_radius'],
            "blur_intensity" => $modernSettings['blur_intensity'],
            "video_background_url" => $modernSettings['video_background_url'],
            "attribution_text" => $modernSettings['attribution_text'],
            
            // Interactive panel settings
            "panel_enabled" => $modernSettings['panel_enabled'],
            "panel_image_url" => $modernSettings['panel_image_url'],
            "panel_title" => $modernSettings['panel_title'],
            "panel_message" => $modernSettings['panel_message'],
            "panel_show_notifications" => $modernSettings['panel_show_notifications'],
            "panel_show_events" => $modernSettings['panel_show_events'],
            "panel_show_countdown" => $modernSettings['panel_show_countdown'],
            "panel_countdown_date" => $modernSettings['panel_countdown_date'],
            "panel_countdown_text" => $modernSettings['panel_countdown_text'],
            "panel_notifications" => $modernSettings['panel_notifications'],
            "panel_events" => $modernSettings['panel_events'],
        ]);
        return true;
    }

}