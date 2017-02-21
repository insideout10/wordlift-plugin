const webpack = require( 'webpack' );
const path = require( 'path' );

const config = {
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
			{ test: /\.(js|jsx)$/, use: 'babel-loader' }
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
	devtool: 'cheap-module-eval-source-map'
};

module.exports = config;
