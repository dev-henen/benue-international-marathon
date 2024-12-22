/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors');

module.exports = {
  content: [
    './app/templates/**/*.twig',
  ],
  theme: {
    extend: {
      fontFamily: {
        bitter: ["Arial", "Helvetica"],
        worksans: ["Arial", "sans-serif"],
      },
      typography: {
        DEFAULT: {
          css: {
            color: '#000',
            a: {
              color: '##0E55B6',
              '&:hover': {
                color: '#D8302F',
              },
            },
          },
        },
      }
    },
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      'gray': colors.gray,
      'black': colors.black,
      'white': colors.white,
      'light': '#EEFEF8',
      'lighter': '#EEFEF8',
      'dark': '#121212',
      'primary': '#0E55B6',
    },
  },
  plugins: [],
};
