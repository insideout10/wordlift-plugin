const webpack = require( 'webpack' ); //to access built-in plugins
const path = require( 'path' );

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

const admin = {
	entry: {
		'wordlift-admin': './src/scripts/admin/index.js'
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
			},
			{ test: /\.css$/, use: 'css-loader' }

		]
	},
	plugins: [
		new webpack.DefinePlugin( {
			'process.env': {
				NODE_ENV: JSON.stringify( 'production' )
			}
		} ),
		new webpack.optimize.UglifyJsPlugin()
	]
};

const e2e = {
	entry: {
		'backend': './tests/e2e/scripts/backend/index.js'
	},
	output: {
		path: path.resolve( __dirname, 'tests/e2e/tests' ),
		filename: '[name]/index.specs.js'
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
			},
			{ test: /\.css$/, use: 'css-loader' }

		]
	},
	plugins: [
		new webpack.DefinePlugin( {
			'process.env': {
				NODE_ENV: JSON.stringify( 'production' )
			}
		} )
	]
};

const config = [ admin, e2e ];

module.exports = config;
