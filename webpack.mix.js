const mix = require('laravel-mix');
const path = require('path');
const tailwindcss = require('tailwindcss');

mix.js('resources/assets/js/app.js', 'public/js')
  .js('resources/assets/js/theme.js', 'public/js')
  .js('resources/assets/js/player.js', 'public/js')
  .js('resources/assets/js/vidstack.js', 'public/js/vidstack.js')
  .js('resources/assets/js/calibration.webgazer.js', 'public/js/calibration.webgazer.js')
  .js('resources/assets/js/eye-tracking.js', 'public/js/eye-tracking.js')
  .sass('resources/assets/sass/app.scss', 'public/css')
  .options({
    processCssUrls: false,
    postCss: [tailwindcss('./tailwind.config.js')],
  })
  .sass('resources/assets/sass/theme.scss', 'public/css')
  .sass('resources/assets/sass/player.scss', 'public/css')
  .webpackConfig({
    output: {
      chunkFilename: 'js/[name].js?id=[chunkhash]',
    },
    resolve: {
      alias: {
        '@': path.resolve('resources/assets/js'),
      },
    },
  })
  .version()
  .sourceMaps();
