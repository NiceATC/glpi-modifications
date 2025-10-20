/**
 * -------------------------------------------------------------------------
 * UI Branding plugin for GLPI - Modern UI JavaScript
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

document.addEventListener('DOMContentLoaded', function() {
    // Theme descriptions
    const themeDescriptions = {
        'default': 'Standard GLPI login interface',
        'glass': 'Modern glassmorphism effect with blur and transparency',
        'dark': 'Elegant dark theme with sophisticated gradients',
        'gradient': 'Vibrant gradient background with modern styling',
        'neon': 'Futuristic neon cyber theme with glowing effects'
    };

    // Layout descriptions
    const layoutDescriptions = {
        'default': 'Standard centered layout',
        'centered': 'Centered card with animation effects',
        'split': 'Split screen with background on one side',
        'fullwidth': 'Full width background with overlay',
        'corner': 'Login form positioned in corner'
    };

    // Add theme descriptions
    const themeSelect = document.getElementById('theme_select');
    if (themeSelect) {
        themeSelect.addEventListener('change', function() {
            updateDescription(this, themeDescriptions);
        });
        updateDescription(themeSelect, themeDescriptions);
    }

    // Add layout descriptions
    const layoutSelect = document.getElementById('layout_select');
    if (layoutSelect) {
        layoutSelect.addEventListener('change', function() {
            updateDescription(this, layoutDescriptions);
        });
        updateDescription(layoutSelect, layoutDescriptions);
    }

    // Color picker live preview
    const colorInputs = ['primary_color', 'secondary_color', 'accent_color'];
    colorInputs.forEach(function(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                updateColorPreview();
            });
        }
    });

    // Range slider updates
    const rangeInputs = ['border_radius_slider', 'blur_intensity_slider'];
    rangeInputs.forEach(function(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                updatePreviewStyles();
            });
        }
    });

    // Feature toggles
    const featureToggles = [
        'enable_modern_inputs',
        'enable_floating_labels', 
        'enable_modern_buttons',
        'enable_logo_effects',
        'enable_particles'
    ];

    featureToggles.forEach(function(toggleId) {
        const toggle = document.querySelector(`select[name="${toggleId}"]`);
        if (toggle) {
            toggle.addEventListener('change', function() {
                updateFeaturePreview(toggleId, this.value === '1');
            });
        }
    });

    // Initialize preview
    initializePreview();

    function updateDescription(selectElement, descriptions) {
        const value = selectElement.value;
        const description = descriptions[value] || '';
        
        // Remove existing description
        const existingDesc = selectElement.parentNode.querySelector('.field-description');
        if (existingDesc) {
            existingDesc.remove();
        }
        
        // Add new description
        if (description) {
            const descElement = document.createElement('small');
            descElement.className = 'field-description text-muted mt-1 d-block';
            descElement.textContent = description;
            selectElement.parentNode.appendChild(descElement);
        }
    }

    function updateColorPreview() {
        const primary = document.getElementById('primary_color')?.value || '#2563eb';
        const secondary = document.getElementById('secondary_color')?.value || '#1e40af';
        const accent = document.getElementById('accent_color')?.value || '#3b82f6';

        // Create or update preview styles
        let previewStyle = document.getElementById('mod-color-preview');
        if (!previewStyle) {
            previewStyle = document.createElement('style');
            previewStyle.id = 'mod-color-preview';
            document.head.appendChild(previewStyle);
        }

        previewStyle.textContent = `
            .btn-primary {
                background: linear-gradient(135deg, ${primary}, ${secondary}) !important;
                border-color: ${primary} !important;
            }
            .btn-primary:hover {
                background: linear-gradient(135deg, ${secondary}, ${primary}) !important;
            }
            .form-control:focus {
                border-color: ${accent} !important;
                box-shadow: 0 0 0 0.2rem ${hexToRgba(accent, 0.25)} !important;
            }
        `;
    }

    function updatePreviewStyles() {
        const borderRadius = document.getElementById('border_radius_slider')?.value || '16';
        const blurIntensity = document.getElementById('blur_intensity_slider')?.value || '20';

        let previewStyle = document.getElementById('mod-style-preview');
        if (!previewStyle) {
            previewStyle = document.createElement('style');
            previewStyle.id = 'mod-style-preview';
            document.head.appendChild(previewStyle);
        }

        previewStyle.textContent = `
            .card {
                border-radius: ${borderRadius}px !important;
            }
            .form-control {
                border-radius: ${Math.max(borderRadius - 4, 4)}px !important;
            }
            .btn {
                border-radius: ${Math.max(borderRadius - 4, 4)}px !important;
            }
        `;
    }

    function updateFeaturePreview(feature, enabled) {
        const body = document.body;
        const className = `mod-preview-${feature.replace('enable_', '')}`;
        
        if (enabled) {
            body.classList.add(className);
        } else {
            body.classList.remove(className);
        }
    }

    function initializePreview() {
        // Add preview styles for the admin interface
        const previewStyles = document.createElement('style');
        previewStyles.id = 'mod-admin-preview';
        previewStyles.textContent = `
            .mod-preview-modern_inputs .form-control {
                background: rgba(255, 255, 255, 0.95) !important;
                border: 2px solid transparent !important;
                transition: all 0.3s ease !important;
            }
            
            .mod-preview-modern_buttons .btn {
                transition: all 0.3s ease !important;
                position: relative;
                overflow: hidden;
            }
            
            .mod-preview-modern_buttons .btn:hover {
                transform: translateY(-2px);
            }
            
            .mod-preview-logo_effects img {
                filter: drop-shadow(0 8px 25px rgba(0, 0, 0, 0.15));
                transition: all 0.3s ease;
            }
            
            .mod-preview-logo_effects img:hover {
                transform: scale(1.05);
            }
        `;
        document.head.appendChild(previewStyles);

        // Initialize color preview
        updateColorPreview();
        updatePreviewStyles();
    }

    function hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // Add smooth transitions to form elements
    const allInputs = document.querySelectorAll('input, select, textarea');
    allInputs.forEach(function(input) {
        input.style.transition = 'all 0.3s ease';
    });

    // Add preview button for login page
    const previewButton = document.createElement('button');
    previewButton.type = 'button';
    previewButton.className = 'btn btn-secondary me-2';
    previewButton.innerHTML = '<i class="ti ti-eye"></i> <span>Preview Login</span>';
    previewButton.addEventListener('click', function() {
        // Open login page in new window/tab
        const loginUrl = window.location.origin + '/';
        window.open(loginUrl, '_blank', 'width=1200,height=800');
    });

    // Add preview button to form buttons area
    const formButtons = document.querySelector('.form-button-separator');
    if (formButtons) {
        const saveButton = formButtons.querySelector('.btn-primary');
        if (saveButton && saveButton.parentNode) {
            saveButton.parentNode.insertBefore(previewButton, saveButton);
        }
    }
});