const webpack = require( 'webpack' );
const path = require( 'path' );
const tests = require( './.build/webpack/tests' );

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
	tests, {
		entry: {
			'wordlift-admin': './src/admin/js/wordlift-admin.js',
			'wordlift-admin-edit-page': './src/scripts/admin-edit-page/index.js',
			'wordlift-admin-settings-page': './src/scripts/admin-settings-page/index.js',
			'wordlift-admin-tinymce': './src/scripts/admin-tinymce/index.js'
		},
		output: {
			path: path.resolve( __dirname, 'src/admin/js' ),
			filename: '[name].bundle.js'
		},
		module: {
			rules: [
				// `eslint`.
				{
					test: /\.js$/,
					exclude: /node_modules/,
					// `eslint` runs before any other loader.
					enforce: 'pre',
					use: 'eslint-loader',
				},
				// `babel`.
				{
					test: /\.(js|jsx)$/,
					use: 'babel-loader'
				},
				// Stylesheets.
				//
				// Do not enable `css-loader?modules` or global styles define in
				// `src/scripts/admin-settings-page/index.scss` will fail.
				{
					test: /\.scss$/,
					use: [
						'style-loader',
						'css-loader',
						'sass-loader'
					]
				},
				{
					test: /\.png$/,
					use: { loader: 'url-loader', options: { limit: 2000 } },
				},
				{
					test: /\.jpg$/,
					use: 'file-loader'
				}
			]
		},
		plugins: [
			new webpack.DefinePlugin( {
				'process.env': {
					NODE_ENV: JSON.stringify( 'production' )
				}
			} ),
			new webpack.optimize.UglifyJsPlugin()
		],
		devtool: 'cheap-module-source-map'
	}
];

module.exports = config;
