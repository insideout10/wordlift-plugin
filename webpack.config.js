const webpack = require( 'webpack' );
const path = require( 'path' );
const ExtractTextPlugin = require( "extract-text-webpack-plugin" );
const tests = require( './.build/webpack/tests' );

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
					use: ExtractTextPlugin.extract(
						{
							fallback: 'style-loader',
							//resolve-url-loader may be
							// chained before
							// sass-loader if necessary
							use: [
								'css-loader',
								'sass-loader'
							]
						} )
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
