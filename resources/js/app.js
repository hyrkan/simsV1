import { createApp } from 'vue';
import './bootstrap';

const el = document.getElementById('app');

if (el) {
    const app = createApp({});

    // Auto-register all Vue components in resources/js/components
    Object.entries(import.meta.glob('./components/**/*.vue', { eager: true }))
        .forEach(([path, definition]) => {
            const name = path
                .split('/')
                .pop()
                .replace('.vue', '')
                .replace(/([a-z0-9])([A-Z])/g, '$1-$2') // insert dash before capital letters
                .toLowerCase();

            app.component(name, definition.default);
        });

    app.mount(el);
}
