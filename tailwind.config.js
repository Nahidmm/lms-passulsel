/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                // Cinematic Mostar-inspired palette (Dynamic via CSS variables)
                cinema: {
                    bg:      'var(--bg)',
                    surface: 'var(--surface)',
                    card:    'var(--card)',
                    border:  'var(--border)',
                },
                paper: 'var(--paper)',
                ink:   'var(--ink)',
                amber: {
                    DEFAULT:  'var(--amber)',
                    bright:   'var(--amber-bright)',
                    soft:     'var(--accent-soft)',
                },
                primary: {
                    DEFAULT: 'var(--amber)',
                    hover: 'var(--amber-bright)',
                    light: 'var(--accent-soft)',
                },
                prisma: '#DEDBC8',
                accent: {
                    DEFAULT: 'var(--accent)',
                    hover: 'var(--amber-bright)',
                },
                secondary: 'var(--surface)',
                border: 'var(--border)',
                success: 'var(--emerald)',
                warning: 'var(--amber-bright)',
                danger: 'var(--rose)',
            },
            fontFamily: {
                sans:    ["'Plus Jakarta Sans'", 'system-ui', 'sans-serif'],
                display: ["'Fraunces'", "'Ogg Medium'", 'serif'],
                serif:   ["'Fraunces'", "'Ogg Medium'", 'Georgia', 'serif'],
                almarai: ["'Almarai'", 'sans-serif'],
                instrument: ["'Instrument Serif'", 'serif'],
            },
            backgroundImage: {
                'cinema-bg': "radial-gradient(ellipse 1200px 700px at 15% -5%, rgba(139,92,246,0.08) 0%, transparent 55%), radial-gradient(ellipse 800px 600px at 85% 95%, rgba(200,137,26,0.07) 0%, transparent 55%), #0b0e13",
            },
        },
    },
    plugins: [],
};
