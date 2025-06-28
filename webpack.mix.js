const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// Main application assets
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .sass('resources/sass/marketplace.scss', 'public/css');

// Dason admin assets
mix.copy('public/assets/css/app.css', 'public/css/dason-admin.css')
   .copy('public/assets/css/bootstrap.min.css', 'public/css/dason-bootstrap.css')
   .copy('public/assets/css/icons.min.css', 'public/css/dason-icons.css')
   .copy('public/assets/js/app.min.js', 'public/js/dason-admin.js');

// Third-party libraries
mix.copy('public/assets/libs/jquery/jquery.min.js', 'public/js/jquery.min.js')
   .copy('public/assets/libs/bootstrap/js/bootstrap.bundle.min.js', 'public/js/bootstrap.min.js')
   .copy('public/assets/libs/metismenu/metisMenu.min.js', 'public/js/metismenu.min.js')
   .copy('public/assets/libs/simplebar/simplebar.min.js', 'public/js/simplebar.min.js')
   .copy('public/assets/libs/node-waves/waves.min.js', 'public/js/node-waves.min.js');

// DataTables
mix.copy('public/assets/libs/datatables.net/js/jquery.dataTables.min.js', 'public/js/datatables.min.js')
   .copy('public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js', 'public/js/datatables-bs4.min.js')
   .copy('public/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css', 'public/css/datatables-bs4.min.css');

// Charts
mix.copy('public/assets/libs/apexcharts/apexcharts.min.js', 'public/js/apexcharts.min.js');

// Notifications
mix.copy('public/assets/libs/sweetalert2/sweetalert2.min.js', 'public/js/sweetalert2.min.js')
   .copy('public/assets/libs/sweetalert2/sweetalert2.min.css', 'public/css/sweetalert2.min.css');

// TinyMCE
mix.copyDirectory('public/assets/libs/@ckeditor/ckeditor5-build-classic', 'public/js/tinymce');

// Form libraries
mix.copy('public/assets/libs/select2/js/select2.min.js', 'public/js/select2.min.js')
   .copy('public/assets/libs/select2/css/select2.min.css', 'public/css/select2.min.css')
   .copy('public/assets/libs/choices.js/public/assets/scripts/choices.min.js', 'public/js/choices.min.js')
   .copy('public/assets/libs/choices.js/public/assets/styles/choices.min.css', 'public/css/choices.min.css')
   .copy('public/assets/libs/flatpickr/flatpickr.min.js', 'public/js/flatpickr.min.js')
   .copy('public/assets/libs/flatpickr/flatpickr.min.css', 'public/css/flatpickr.min.css')
   .copy('public/assets/libs/dropzone/min/dropzone.min.js', 'public/js/dropzone.min.js')
   .copy('public/assets/libs/dropzone/min/dropzone.min.css', 'public/css/dropzone.min.css');

// Chart.js
mix.copy('public/assets/libs/chart.js/Chart.bundle.min.js', 'public/js/chart.min.js');

// Feather Icons
mix.copy('public/assets/libs/feather-icons/feather.min.js', 'public/js/feather-icons.min.js');

// Page specific scripts
mix.copy('public/assets/js/pages/dashboard.init.js', 'public/js/pages/dashboard.init.js')
   .copy('public/assets/js/pages/datatables.init.js', 'public/js/pages/datatables.init.js')
   .copy('public/assets/js/pages/form-editor.init.js', 'public/js/pages/form-editor.init.js')
   .copy('public/assets/js/pages/form-advanced.init.js', 'public/js/pages/form-advanced.init.js');

// Marketplace specific assets
mix.js('resources/js/marketplace.js', 'public/js');

// Options
mix.options({
    processCssUrls: false
});

// Versioning for production
if (mix.inProduction()) {
    mix.version();
}
