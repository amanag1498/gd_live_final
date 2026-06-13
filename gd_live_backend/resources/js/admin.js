import './bootstrap';
import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Calendar } from '@fullcalendar/core';

window.bootstrap = bootstrap;
window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        theme: 'light',
        init() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            this.theme = savedTheme || systemTheme;
            this.apply();
        },
        toggle() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', this.theme);
            this.apply();
        },
        apply() {
            const html = document.documentElement;
            const body = document.body;
            if (this.theme === 'dark') {
                html.classList.add('dark');
                body.classList.add('dark', 'bg-gray-900');
            } else {
                html.classList.remove('dark');
                body.classList.remove('dark', 'bg-gray-900');
            }
        },
    });

    Alpine.store('sidebar', {
        isExpanded: window.innerWidth >= 1280,
        isMobileOpen: false,
        isHovered: false,
        toggleExpanded() {
            this.isExpanded = !this.isExpanded;
            this.isMobileOpen = false;
        },
        toggleMobileOpen() {
            this.isMobileOpen = !this.isMobileOpen;
        },
        setMobileOpen(value) {
            this.isMobileOpen = value;
        },
        setHovered(value) {
            if (window.innerWidth >= 1280 && !this.isExpanded) {
                this.isHovered = value;
            }
        },
    });

    Alpine.store('theme').init();
});

(function applyInitialTheme() {
    const savedTheme = localStorage.getItem('theme');
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const theme = savedTheme || systemTheme;
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark', 'bg-gray-900');
    } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark', 'bg-gray-900');
    }
})();

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('#mapOne')) {
        import('./components/map').then((module) => module.initMap());
    }

    if (document.querySelector('#chartOne')) {
        import('./components/chart/chart-1').then((module) => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart/chart-2').then((module) => module.initChartTwo());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart/chart-3').then((module) => module.initChartThree());
    }
    if (document.querySelector('#chartSix')) {
        import('./components/chart/chart-6').then((module) => module.initChartSix());
    }
    if (document.querySelector('#chartEight')) {
        import('./components/chart/chart-8').then((module) => module.initChartEight());
    }
    if (document.querySelector('#chartThirteen')) {
        import('./components/chart/chart-13').then((module) => module.initChartThirteen());
    }

    if (document.querySelector('#calendar')) {
        import('./components/calendar-init').then((module) => module.calendarInit());
    }
});
