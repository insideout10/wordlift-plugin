const AdminConfiguration = require( './.build/webpack/admin' );
const PublicConfiguration = require( './.build/webpack/public' );
const TestsConfiguration = require( './.build/webpack/tests' );

// @todo: consider splitting vendor, see:
// * https://robertknight.github.io/posts/webpack-dll-plugins/
// * https://webpack.js.org/concepts/entry-points/

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
	PublicConfiguration,
	TestsConfiguration
];

module.exports = config;
