/**
 * This file, coupled with angular-intro.js, restores an existing Angular
 * after ours has been loaded.
 *
 * The loading sequence is configured in the Gruntfile.js.
 *
 * See https://github.com/insideout10/wordlift-plugin/issues/865.
 *
 * @since 3.19.6
 */
if ("undefined" !== typeof window._wlAngularBackup) {
  // Restore any Angular app.
  window._wlAngularBackupApps.attr("ng-app", true);

  // Restore the previous Angular.
  window.angular = window._wlAngularBackup;
  delete window._wlAngularBackup;
}
