/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/css/**/*.css',
    ],
    theme: {
        extend: {
            colors: {
                'primary': '#3B82F6',
                'secondary': '#F59E0B',
            },
        },
        fontFamily: {
            outfit: ['Outfit', 'sans-serif'],
        },
    },
    plugins: [],
}
