// Import core dependencies
import './bootstrap';
import Alpine from 'alpinejs';

// Import theme
import './theme';

// Import utilities
import * as helpers from './utils/helpers';
import * as dom from './utils/dom';
import * as validation from './utils/validation';

// Import components
import { showNotification } from './components/notifications';

// Make available globally
window.Alpine = Alpine;
window.helpers = helpers;
window.dom = dom;
window.validation = validation;
window.showNotification = showNotification;

// Initialize Alpine.js
Alpine.start();
