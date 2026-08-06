const mix = require('laravel-mix');

mix.js('assets/js/src/healthedia-public.js', 'assets/js/healthedia-public.js')
   .js('assets/js/src/member-settings.js', 'assets/js/member-settings.js')
   .js('assets/js/src/archive-search.js', 'assets/js/archive-search.js')
   .js('assets/js/src/member-requests.js', 'assets/js/member-requests.js')
   .js('dashboard/src/index.js', 'assets/js/healthedia-dashboard.js').react()
   .postCss('assets/css/src/healthedia-core.css', 'assets/css/healthedia-core.css', [
     require('tailwindcss'),
   ])
   .postCss('assets/css/src/healthedia-dashboard.css', 'assets/css/healthedia-dashboard.css', [
     require('tailwindcss'),
   ]);
