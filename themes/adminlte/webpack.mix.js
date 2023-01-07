const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public/themes/adminlte').mergeManifest();

mix.js(__dirname + '/js/app.js', 'js/adminlte.js')
    .sass( __dirname + '/sass/app.scss', 'css/adminlte.css');

if (mix.inProduction()) {
    mix.version();
}