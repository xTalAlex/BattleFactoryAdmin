const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors') 

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: { 
                primary: colors.orange,
                secondary : colors.violet,
                gray: colors.stone,
                danger: colors.red,
                success: colors.green,
                warning: colors.amber,
            }, 
        },
    },

    plugins: [
        require('@tailwindcss/forms'), 
        require('@tailwindcss/typography')
    ],
};
