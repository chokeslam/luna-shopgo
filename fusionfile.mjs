/**
 * Part of Windwalker Fusion project.
 *
 * @copyright  Copyright (C) 2021 LYRASOFT.
 * @license    MIT
 */

import fusion, { sass, babel, parallel, wait } from '@windwalker-io/fusion';
import { jsSync, installVendors, findModules } from '@windwalker-io/core';

export async function css() {
  // Watch start
  fusion.watch([
    'resources/assets/scss/**/*.scss',
    'src/Module/**/assets/*.scss',
    ...findModules('**/assets/*.scss')
  ]);
  // Watch end

  return wait(
    // Front
    sass(
      [
        'resources/assets/scss/front/main.scss',
        ...findModules('Front/**/assets/*.scss'),
        'src/Module/Front/**/assets/*.scss'
      ],
      'www/assets/css/front/main.css'
    ),
    // Boostrap
    sass(
      'resources/assets/scss/front/bootstrap.scss',
      'www/assets/css/front/bootstrap.css'
    ),
    // Admin
    sass(
      [
        'resources/assets/scss/admin/main.scss',
        ...findModules('Admin/**/assets/*.scss'),
        'src/Module/Admin/**/assets/*.scss'
      ],
      'www/assets/css/admin/main.css'
    )
  );
}

export async function js() {
  // Watch start
  fusion.watch(['resources/assets/src/**/*.{js,mjs}']);
  // Watch end

  // Compile Start
  return wait(
    babel('resources/assets/src/**/*.{js,mjs}', 'www/assets/js/', { module: 'systemjs' }),
    syncJS()
  );
  // Compile end
}

export async function images() {
  // Watch start
  fusion.watch('resources/assets/images/**/*');
  // Watch end

  // Compile Start
  return wait(
    fusion.copy('resources/assets/images/**/*', 'www/assets/images/')
  );
  // Compile end
}

export async function syncJS() {
  // Watch start
  fusion.watch(['src/Module/**/assets/**/*.{js,mjs}', ...findModules('**/assets/*.{js,mjs}')]);
  // Watch end

  // Compile Start
  const { dest } = await jsSync(
    'src/Module/',
    'www/assets/js/view/'
  );

  return babel(dest.path + '**/*.{mjs,js}', null, { module: 'systemjs' });
  // Compile end
}

export async function install() {
  return installVendors(
    [
      '@fortawesome/fontawesome-free',
      'wowjs',
      'animate.css',
      'jarallax',
    ],
    [
      'lyrasoft/luna'
    ]
  );
}

export default parallel(css, js, images);

/*
 * APIs
 *
 * Compile entry:
 * fusion.js(source, dest, options = {})
 * fusion.babel(source, dest, options = {})
 * fusion.module(source, dest, options = {})
 * fusion.ts(source, dest, options = {})
 * fusion.typeScript(source, dest, options = {})
 * fusion.css(source, dest, options = {})
 * fusion.sass(source, dest, options = {})
 * fusion.copy(source, dest, options = {})
 *
 * Live Reload:
 * fusion.livereload(source, dest, options = {})
 * fusion.reload(file)
 *
 * Gulp proxy:
 * fusion.src(source, options)
 * fusion.dest(path, options)
 * fusion.watch(glob, opt, fn)
 * fusion.symlink(directory, options = {})
 * fusion.lastRun(task, precision)
 * fusion.tree(options = {})
 * fusion.series(...tasks)
 * fusion.parallel(...tasks)
 *
 * Stream Helper:
 * fusion.through(handler) // Same as through2.obj()
 *
 * Config:
 * fusion.disableNotification()
 * fusion.enableNotification()
 */
