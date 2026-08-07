const mix = require('laravel-mix');

mix.js('assets/js/src/healthedia-public.js', 'assets/js/healthedia-public.js')
   .js('dashboard/src/index.js', 'assets/js/healthedia-dashboard.js').react()
   .postCss('assets/css/src/healthedia-core.css', 'assets/css/healthedia-core.css', [
     require('tailwindcss'),
   ])
   .postCss('assets/css/src/healthedia-dashboard.css', 'assets/css/healthedia-dashboard.css', [
     require('tailwindcss'),
   ]);
