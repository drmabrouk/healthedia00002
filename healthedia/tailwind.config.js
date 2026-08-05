/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/views/**/*.php",
    "./dashboard/views/**/*.php",
    "./dashboard/src/**/*.js",
    "./assets/js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        gray: {
          light: '#E0E0E0',
          medium: '#888888',
          dark: '#111111'
        },
        primary: '#111111',
        secondary: '#FFFFFF'
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['"JetBrains Mono"', '"Roboto Mono"', 'monospace']
      }
    }
  },
  plugins: [],
}
