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

use Glpi\Plugin\Hooks;
use GlpiPlugin\Mod\BrandManager;

const PLUGIN_MOD_VERSION = "11.0.0";
function plugin_init_mod()
{
    global $PLUGIN_HOOKS, $CFG_GLPI;

    $PLUGIN_HOOKS['config_page']['mod'] = './front/uibranding.php';
    if (Plugin::isPluginActive("mod")) {
        $CFG_GLPI["app_name"] = BrandManager::getCurrentTitle();
        
        // Apply modern UI styles if enabled OR if login background is enabled
        if (BrandManager::isLoginPageModified() || BrandManager::isModernUIEnabled()) {
            // Enable public access to background and dynamic CSS
            \Glpi\Http\Firewall::addPluginStrategyForLegacyScripts("mod", '/^\/background.php$/', \Glpi\Http\Firewall::STRATEGY_NO_CHECK);
            \Glpi\Http\Firewall::addPluginStrategyForLegacyScripts("mod", '/^\/dynamic.css.php$/', \Glpi\Http\Firewall::STRATEGY_NO_CHECK);

            // Add CSS files to login page
            $PLUGIN_HOOKS[Hooks::ADD_CSS_ANONYMOUS_PAGE]["mod"] = [
                "./public/css/mod_anonymous.css",
                "./public/css/mod_split_layouts.css",
                "./public/css/mod_responsive.css",
                "./public/dynamic.css.php"
            ];
            
            // Add JavaScript to login page
            $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT_ANONYMOUS_PAGE]["mod"] = [
                "./public/js/mod_login.js"
            ];

            // Add body classes for modern UI via JavaScript injection
            $PLUGIN_HOOKS[Hooks::DISPLAY_LOGIN]["mod"] = 'plugin_mod_display_login';
        }
    }
}

function plugin_version_mod()
{
    return array(
        'name' => __('Modern Login Designer', 'mod'),
        'version' => PLUGIN_MOD_VERSION,
        'author' => 'GLPI Community',
        'license' => 'GPLv3',
        'homepage' => 'https://github.com/glpi-community/mod',
        'requirements' => [
            'glpi' => [
                'min' => "11.0",
                'max' => "12.0"
            ]
        ]
    );
}

/**
 * Display login hook to add modern UI classes to body
 */
function plugin_mod_display_login()
{
    $bodyClasses = BrandManager::generateBodyClasses();
    $cssVariables = BrandManager::generateCSSVariables();
    $modernSettings = BrandManager::getModernUISettings();
    $videoUrl = $modernSettings['video_background_url'] ?? '';
    $attributionText = $modernSettings['attribution_text'] ?? '';
    
    if (!empty($bodyClasses) || !empty($cssVariables)) {
        // Add CSS variables inline
        if (!empty($cssVariables)) {
            echo "<style>\n" . $cssVariables . "</style>\n";
        }
        
        // Add video URL meta tag if configured
        if (!empty($videoUrl)) {
            echo '<meta name="mod-video-background" content="' . htmlspecialchars($videoUrl, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        }
        
        // Add body classes immediately and to welcome-anonymous element
        if (!empty($bodyClasses)) {
            echo "<script>
            (function() {
                // Add classes to body and html immediately
                var classes = '" . $bodyClasses . "'.split(' ');
                for (var i = 0; i < classes.length; i++) {
                    if (classes[i]) {
                        document.documentElement.classList.add(classes[i]);
                        document.body.classList.add(classes[i]);
                    }
                }
                
                // Also add to welcome-anonymous and page-anonymous when DOM is ready
                document.addEventListener('DOMContentLoaded', function() {
                    var welcomeEl = document.querySelector('.welcome-anonymous');
                    var pageEl = document.querySelector('.page-anonymous');
                    if (welcomeEl) {
                        for (var i = 0; i < classes.length; i++) {
                            if (classes[i]) {
                                welcomeEl.classList.add(classes[i]);
                            }
                        }
                    }
                    if (pageEl) {
                        for (var i = 0; i < classes.length; i++) {
                            if (classes[i]) {
                                pageEl.classList.add(classes[i]);
                            }
                        }
                    }
                    
                    // Move logo to card-header and hide original container
                    var logoContainer = document.querySelector('.card-body .text-center');
                    var cardHeader = document.querySelector('.col-md-5 .card-header');
                    
                    // Hide the original text-center container
                    if (logoContainer) {
                        logoContainer.style.display = 'none';
                    }
                    
                    // Create new logo span inside card-header (the one inside col-md-5)
                    if (cardHeader) {
                        var logoSpan = document.createElement('span');
                        logoSpan.className = 'glpi-logo mb-4';
                        logoSpan.title = 'GLPI';
                        
                        // Insert logo BEFORE the h2
                        var h2 = cardHeader.querySelector('h2');
                        if (h2) {
                            cardHeader.insertBefore(logoSpan, h2);
                        } else {
                            cardHeader.appendChild(logoSpan);
                        }
                        
                        // Ensure card-header is visible and styled
                        cardHeader.style.display = 'block';
                        cardHeader.style.textAlign = 'center';
                        cardHeader.style.padding = '20px 0';
                        cardHeader.style.background = 'transparent';
                    }
                    
                    // Add attribution text if configured
                    var attribution = " . json_encode($attributionText) . ";
                    if (attribution) {
                        var formPanel = document.querySelector('.col-md-5');
                        if (formPanel) {
                            var attrDiv = document.createElement('div');
                            attrDiv.className = 'mod-attribution';
                            attrDiv.style.cssText = 'text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.08); font-size: 12px; color: #666;';
                            attrDiv.textContent = attribution;
                            formPanel.appendChild(attrDiv);
                        }
                    }
                    
                    // Add interactive panel content
                    var decorativePanel = document.querySelector('.col-auto');
                    var panelEnabled = " . json_encode($modernSettings['panel_enabled'] ?? '1') . ";
                    var panelTitle = " . json_encode($modernSettings['panel_title'] ?? 'Bem-vindo!') . ";
                    var panelMessage = " . json_encode($modernSettings['panel_message'] ?? 'Configure mensagens, eventos e notificações no painel administrativo.') . ";
                    var showNotifications = " . json_encode($modernSettings['panel_show_notifications'] ?? '1') . ";
                    var showEvents = " . json_encode($modernSettings['panel_show_events'] ?? '1') . ";
                    var showCountdown = " . json_encode($modernSettings['panel_show_countdown'] ?? '0') . ";
                    var countdownDate = " . json_encode($modernSettings['panel_countdown_date'] ?? '') . ";
                    var countdownText = " . json_encode($modernSettings['panel_countdown_text'] ?? '') . ";
                    var notifications = " . json_encode($modernSettings['panel_notifications'] ?? '') . ";
                    var events = " . json_encode($modernSettings['panel_events'] ?? '') . ";
                    
                    if (decorativePanel) {
                        if (panelEnabled === '1') {
                            // Add animations keyframes
                            var styleEl = document.createElement('style');
                            styleEl.textContent = '@keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}.mod-fade-in{animation:fadeInUp 0.6s ease-out forwards}.mod-pulse{animation:pulse 2s ease-in-out infinite}';
                            document.head.appendChild(styleEl);
                            
                            var panelHTML = '<div class=\"mod-interactive-panel\" style=\"padding: 35px 30px; height: 100%; width: 100%; display: flex !important; flex-direction: column; justify-content: flex-start; background: #ffffff; overflow-y: auto;\">' +
                                '<div class=\"mod-panel-header mod-fade-in\" style=\"margin-bottom: 30px; opacity: 0; animation-delay: 0s;\">' +
                                '<h2 style=\"color: #1a1a1a; font-size: 28px; font-weight: 700; margin: 0 0 8px 0; font-family: -apple-system, BlinkMacSystemFont, \\'Segoe UI\\', Roboto, \\'Helvetica Neue\\', Arial, sans-serif; letter-spacing: -0.5px;\">' + panelTitle + '</h2>' +
                                '<p style=\"color: #6b7280; font-size: 14px; line-height: 1.5; margin: 0; font-weight: 400;\">' + panelMessage + '</p>' +
                                '</div>' +
                                '<div class=\"mod-panel-grid\" style=\"display: flex; flex-direction: column; gap: 20px;\">';
                        
                            // Add countdown if enabled
                            if (showCountdown === '1' && countdownDate) {
                                panelHTML += '<div class=\"mod-countdown-card mod-fade-in\" style=\"background: #ffffff; padding: 0; animation-delay: 0.1s; opacity: 0; border-bottom: 1px solid #e5e7eb;\">' +
                                    '<div style=\"display: flex; align-items: center; gap: 12px; padding: 16px 0; border-bottom: 1px solid #e5e7eb;\">' +
                                    '<div style=\"width: 32px; height: 32px; background: #3b82f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;\">⏰</div>' +
                                    '<h3 style=\"color: #1a1a1a; font-size: 15px; font-weight: 600; margin: 0; font-family: -apple-system, BlinkMacSystemFont, \\'Segoe UI\\', Roboto, sans-serif;\">' + countdownText + '</h3>' +
                                    '</div>' +
                                    '<div id=\"countdown-timer\" style=\"display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; padding: 16px 0; text-align: center;\"></div>' +
                                    '</div>';
                            }
                            
                            // Add notifications if enabled
                            if (showNotifications === '1') {
                                panelHTML += '<div class=\"mod-notifications-card mod-fade-in\" style=\"background: #ffffff; padding: 0; animation-delay: 0.2s; opacity: 0;\">' +
                                    '<div style=\"display: flex; align-items: center; gap: 12px; padding: 16px 0; border-bottom: 1px solid #e5e7eb;\">' +
                                    '<div style=\"width: 32px; height: 32px; background: #ef4444; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;\">🔔</div>' +
                                    '<h3 style=\"color: #1a1a1a; font-size: 15px; font-weight: 600; margin: 0; font-family: -apple-system, BlinkMacSystemFont, \\'Segoe UI\\', Roboto, sans-serif;\">Notificações</h3>' +
                                    '</div>' +
                                    '<div style=\"padding: 12px 0; max-height: 180px; overflow-y: auto;\">';
                                
                                if (notifications && notifications.trim()) {
                                    var notifList = notifications.split('\\n').filter(function(n) { return n.trim(); });
                                    panelHTML += '<div style=\"display: flex; flex-direction: column; gap: 0;\">';
                                    notifList.forEach(function(notif, idx) {
                                        panelHTML += '<div style=\"padding: 12px 0; border-bottom: 1px solid #f3f4f6; transition: all 0.2s;\" onmouseover=\"this.style.background=\\'#f9fafb\\'; this.style.paddingLeft=\\'8px\\'\" onmouseout=\"this.style.background=\\'transparent\\'; this.style.paddingLeft=\\'0\\'\">' +
                                            '<div style=\"display: flex; align-items: start; gap: 10px;\">' +
                                            '<div style=\"width: 5px; height: 5px; background: #ef4444; border-radius: 50%; margin-top: 5px; flex-shrink: 0;\"></div>' +
                                            '<p style=\"color: #374151; font-size: 13px; margin: 0; line-height: 1.5; font-weight: 400;\">' + notif + '</p>' +
                                            '</div></div>';
                                    });
                                    panelHTML += '</div>';
                                } else {
                                    panelHTML += '<p style=\"color: #9ca3af; font-size: 13px; text-align: center; font-style: italic; padding: 16px 0;\">Nenhuma notificação</p>';
                                }
                                panelHTML += '</div></div>';
                            }
                            
                            // Add events if enabled
                            if (showEvents === '1') {
                                panelHTML += '<div class=\"mod-events-card mod-fade-in\" style=\"background: #ffffff; padding: 0; animation-delay: 0.3s; opacity: 0;\">' +
                                    '<div style=\"display: flex; align-items: center; gap: 12px; padding: 16px 0; border-bottom: 1px solid #e5e7eb;\">' +
                                    '<div style=\"width: 32px; height: 32px; background: #8b5cf6; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;\">📅</div>' +
                                    '<h3 style=\"color: #1a1a1a; font-size: 15px; font-weight: 600; margin: 0; font-family: -apple-system, BlinkMacSystemFont, \\'Segoe UI\\', Roboto, sans-serif;\">Próximos Eventos</h3>' +
                                    '</div>' +
                                    '<div style=\"padding: 12px 0; max-height: 180px; overflow-y: auto;\">';
                                
                                if (events && events.trim()) {
                                    var eventList = events.split('\\n').filter(function(e) { return e.trim(); });
                                    panelHTML += '<div style=\"display: flex; flex-direction: column; gap: 0;\">';
                                    eventList.forEach(function(event, idx) {
                                        panelHTML += '<div style=\"padding: 12px 0; border-bottom: 1px solid #f3f4f6; transition: all 0.2s;\" onmouseover=\"this.style.background=\\'#f9fafb\\'; this.style.paddingLeft=\\'8px\\'\" onmouseout=\"this.style.background=\\'transparent\\'; this.style.paddingLeft=\\'0\\'\">' +
                                            '<div style=\"display: flex; align-items: start; gap: 10px;\">' +
                                            '<div style=\"width: 5px; height: 5px; background: #8b5cf6; border-radius: 50%; margin-top: 5px; flex-shrink: 0;\"></div>' +
                                            '<p style=\"color: #374151; font-size: 13px; margin: 0; line-height: 1.5; font-weight: 400;\">' + event + '</p>' +
                                            '</div></div>';
                                    });
                                    panelHTML += '</div>';
                                } else {
                                    panelHTML += '<p style=\"color: #9ca3af; font-size: 13px; text-align: center; font-style: italic; padding: 16px 0;\">Nenhum evento agendado</p>';
                                }
                                panelHTML += '</div></div>';
                            }
                            
                            panelHTML += '</div></div>';
                            decorativePanel.innerHTML = panelHTML;
                            
                            // Trigger animations with proper delay
                            setTimeout(function() {
                                var header = decorativePanel.querySelector('.mod-panel-header');
                                if (header) header.style.opacity = '1';
                                
                                var fadeIns = decorativePanel.querySelectorAll('.mod-fade-in');
                                fadeIns.forEach(function(el, idx) {
                                    setTimeout(function() {
                                        el.style.opacity = '1';
                                    }, idx * 100);
                                });
                            }, 100);
                            
                            // Start countdown timer if enabled
                            if (showCountdown === '1' && countdownDate) {
                                var countdownEl = document.getElementById('countdown-timer');
                                if (countdownEl) {
                                    var targetDate = new Date(countdownDate).getTime();
                                    
                                    function updateCountdown() {
                                        var now = new Date().getTime();
                                        var distance = targetDate - now;
                                        
                                        if (distance < 0) {
                                            countdownEl.innerHTML = '<div style=\"grid-column: 1/-1; color: #3b82f6; font-size: 14px; font-weight: 600; padding: 8px 0; text-align: center;\">🎉 Evento Iniciado!</div>';
                                        } else {
                                            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                            
                                            countdownEl.innerHTML = 
                                                '<div style=\"background: #f0f9ff; border-radius: 8px; padding: 12px 6px; border: 1px solid #e0f2fe;\">' +
                                                '<div style=\"font-size: 20px; font-weight: 700; color: #3b82f6; margin-bottom: 2px;\">' + (days < 10 ? '0' + days : days) + '</div>' +
                                                '<div style=\"font-size: 10px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.3px;\">Dias</div>' +
                                                '</div>' +
                                                '<div style=\"background: #f0f9ff; border-radius: 8px; padding: 12px 6px; border: 1px solid #e0f2fe;\">' +
                                                '<div style=\"font-size: 20px; font-weight: 700; color: #3b82f6; margin-bottom: 2px;\">' + (hours < 10 ? '0' + hours : hours) + '</div>' +
                                                '<div style=\"font-size: 10px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.3px;\">Horas</div>' +
                                                '</div>' +
                                                '<div style=\"background: #f0f9ff; border-radius: 8px; padding: 12px 6px; border: 1px solid #e0f2fe;\">' +
                                                '<div style=\"font-size: 20px; font-weight: 700; color: #3b82f6; margin-bottom: 2px;\">' + (minutes < 10 ? '0' + minutes : minutes) + '</div>' +
                                                '<div style=\"font-size: 10px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.3px;\">Min</div>' +
                                                '</div>' +
                                                '<div style=\"background: #f0f9ff; border-radius: 8px; padding: 12px 6px; border: 1px solid #e0f2fe;\">' +
                                                '<div style=\"font-size: 20px; font-weight: 700; color: #3b82f6; margin-bottom: 2px;\">' + (seconds < 10 ? '0' + seconds : seconds) + '</div>' +
                                                '<div style=\"font-size: 10px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.3px;\">Seg</div>' +
                                                '</div>';
                                        }
                                    }
                                    
                                    updateCountdown();
                                    setInterval(updateCountdown, 1000);
                                }
                            }
                        } else {
                            console.log('MOD Debug - Panel disabled or not found');
                        }
                    }
                });
            })();
            </script>\n";
        }
    }
}
