/**
 * MOD Plugin - Login Page Enhancement Script
 * Ensures classes are applied and handles video background
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModLogin);
    } else {
        initModLogin();
    }
    
    function initModLogin() {
        // Find the welcome-anonymous container
        const welcomeContainer = document.querySelector('.welcome-anonymous');
        const pageAnonymous = document.querySelector('.page-anonymous');
        
        if (!welcomeContainer && !pageAnonymous) {
            return;
        }
        
        // Get all mod classes from body
        const bodyClasses = Array.from(document.body.classList);
        const modClasses = bodyClasses.filter(cls => cls.startsWith('mod-'));
        
        // Apply body classes to containers
        if (welcomeContainer) {
            modClasses.forEach(cls => {
                if (!welcomeContainer.classList.contains(cls)) {
                    welcomeContainer.classList.add(cls);
                }
            });
        }
        
        if (pageAnonymous) {
            modClasses.forEach(cls => {
                if (!pageAnonymous.classList.contains(cls)) {
                    pageAnonymous.classList.add(cls);
                }
            });
        }
        
        // Also add to html element for better CSS targeting
        const layoutClass = modClasses.find(cls => cls.startsWith('mod-layout-'));
        if (layoutClass && !document.documentElement.classList.contains(layoutClass)) {
            document.documentElement.classList.add(layoutClass);
        }
        
        // Check if video background is configured
        checkAndLoadVideoBackground();
    }
    
    function checkAndLoadVideoBackground() {
        // Check if there's a video URL configured
        const videoUrl = getVideoBackgroundUrl();
        
        if (videoUrl) {
            createVideoBackground(videoUrl);
        }
    }
    
    function getVideoBackgroundUrl() {
        // Try to get from meta tag or data attribute
        const metaVideo = document.querySelector('meta[name="mod-video-background"]');
        if (metaVideo) {
            return metaVideo.getAttribute('content');
        }
        
        // Try to get from body data attribute
        const bodyVideo = document.body.getAttribute('data-mod-video-bg');
        if (bodyVideo) {
            return bodyVideo;
        }
        
        return null;
    }
    
    function createVideoBackground(videoUrl) {
        // Check if video already exists
        if (document.querySelector('video.mod-background-video')) {
            return;
        }
        
        const video = document.createElement('video');
        video.className = 'mod-background-video';
        video.autoplay = true;
        video.loop = true;
        video.muted = true;
        video.playsInline = true;
        video.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            z-index: -2;
            object-fit: cover;
        `;
        
        const source = document.createElement('source');
        source.src = videoUrl;
        source.type = 'video/mp4';
        
        video.appendChild(source);
        document.body.insertBefore(video, document.body.firstChild);
        
        // Start playing
        video.play().catch(function(error) {
            console.log('Video autoplay prevented:', error);
        });
    }
    
})();
