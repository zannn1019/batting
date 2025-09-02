import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Define the formatGameTime function
function formatGameTime(gameTime) {
    if (!gameTime) return "Date/Time N/A";
    const date = new Date(gameTime);
    // Check if the date is valid after parsing
    if (isNaN(date.getTime())) {
        return "Invalid Date";
    }

    const options = {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
    };
    return date.toLocaleString(undefined, options);
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        app.config.globalProperties.$formatGameTime = formatGameTime;

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
