const webpack = require( 'webpack' );
const path = require( 'path' );
const ExtractTextPlugin = require( "extract-text-webpack-plugin" );
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
			'wordlift-admin-settings-page': './src/scripts/wordlift-admin-settings-page/index.js'
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
				{ test: /\.(js|jsx)$/, use: 'babel-loader' },
				// Stylesheets.
				{
					test: /\.scss$/,
					use: [
						'style-loader',
						'css-loader',
						'sass-loader'
					]
				}
			]
		},
		plugins: [
			new webpack.DefinePlugin( {
				'process.env': {
					NODE_ENV: JSON.stringify( 'production' )
				}
			} ),
			new webpack.optimize.UglifyJsPlugin(),
			new ExtractTextPlugin( '../css/[name].min.css' )
		],
		devtool: 'cheap-module-eval-source-map'
	}
];

module.exports = config;
