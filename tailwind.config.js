/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#3b82f6',
                    hover: '#2563eb',
                    light: '#dbeafe',
                },
                accent: {
                    DEFAULT: '#C5A02E',
                    hover: '#A8871F',
                },
                secondary: '#F5F7FA',
                border: '#E2E6EC',
                text: {
                    primary: '#1A1A1A',
                    secondary: '#5C6470',
                },
                success: '#1E8E5A',
                warning: '#D98E04',
                danger: '#C0392B',
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                display: ['Poppins', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
