

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Prevent numeric input value changes on mouse scroll
document.addEventListener('wheel', function () {
    if (document.activeElement && document.activeElement.tagName === 'INPUT' && document.activeElement.type === 'number') {
        document.activeElement.blur();
    }
}, { passive: true });

Alpine.start();

