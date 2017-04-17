const AdminConfiguration = require( './.build/webpack/admin' );
const EditorConfiguration = require( './.build/webpack/tinymce' );
const PublicConfiguration = require( './.build/webpack/public' );
const TestsConfiguration = require( './.build/webpack/tests' );

// @todo: add the other files:
// * wordlift, for the WordPress front-end,
// * wordlift-admin, for the WordPress admin,
// * wordlift-editor, for the WordPress TinyMCE editor.
// see https://webpack.js.org/concepts/output/#output-filename.

// @todo: migration, we're migrating all the JavaScript files to WebPack and
// organizing them in three main files (front-end, admin, editor). For some time
// the old files and the new files will cohexist.

const config = [
	AdminConfiguration,
	EditorConfiguration,
	PublicConfiguration,
	TestsConfiguration
];

module.exports = config;
