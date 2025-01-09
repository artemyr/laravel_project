import './bootstrap';

import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

import './main'

import.meta.glob([
    '../images/**',
    '../fonts/**',
])
