import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/js/pages/users/index.js",
                "resources/js/pages/profile/index.js",
                "resources/js/pages/anak-asuh/index.js",
                "resources/js/pages/rumah-harapan/index.js",
                "resources/js/pages/audit-log/index.js",
                "resources/js/pages/settings/index.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            lucide: require.resolve("lucide"),
        },
    },
});
