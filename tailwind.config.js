import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Baloo 2', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Colores de marca ZSystem + su versión pastel ("suave")
                verde:   { DEFAULT: '#2FAE64', suave: '#E3F5EA' },
                rosa:    { DEFAULT: '#E5426E', suave: '#FCE6EC' },
                teal:    { DEFAULT: '#12958A', suave: '#DDF2EF' },
                naranja: { DEFAULT: '#E8792E', suave: '#FDEDE0' },
                celeste: { DEFAULT: '#3B8ED8', suave: '#E3EFFB' },
                lavanda: { DEFAULT: '#7A68D6', suave: '#EDEAFB' },
                lienzo: '#EAF1EF',   // fondo general (gris verdoso suave)
                borde:  '#CFDDDA',   // borde de cajas y separadores
            },
        },
    },

    plugins: [forms],
};
