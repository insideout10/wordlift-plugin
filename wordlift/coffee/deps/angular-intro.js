/**
 * This file, coupled with angular-outro.js, backups an existing Angular
 * in order to load ours.
 *
 * The loading sequence is configured in the Gruntfile.js.
 *
 * See https://github.com/insideout10/wordlift-plugin/issues/865.
 *
 * @since 3.19.6
 */
if ("undefined" !== typeof window.angular) {
  // Backup the existing angular.
  window._wlAngularBackup = window.angular;
  delete window.angular;

  // Remove any `ng-app` from the dom, to avoid Angular trying to automatically
  // bootstrap them.
  window._wlAngularBackupApps = jQuery("[ng-app]").removeAttr("ng-app");
}
