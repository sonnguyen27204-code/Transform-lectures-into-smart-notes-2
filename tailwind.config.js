/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
            },
            colors: {
                stone: {
                    50: '#fafaf9', 100: '#f5f5f4', 200: '#e7e5e3',
                    300: '#d6d3d1', 400: '#a8a29e', 500: '#78716c',
                    600: '#57534e', 700: '#44403c', 800: '#292524', 900: '#1c1917',
                },
            },
            boxShadow: {
                'soft': '0 2px 8px -2px rgba(0,0,0,0.08), 0 4px 16px -4px rgba(0,0,0,0.06)',
                'soft-lg': '0 4px 12px -4px rgba(0,0,0,0.1), 0 8px 24px -8px rgba(0,0,0,0.08)',
                'soft-xl': '0 8px 24px -8px rgba(0,0,0,0.12), 0 16px 48px -16px rgba(0,0,0,0.1)',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
        },
    },
    plugins: [],
};
