const webpack = require( 'webpack' );
const path = require( 'path' );

const config = {
	entry: {
		'backend': './tests/e2e/scripts/backend/index.js',
	},
	output: {
		path: './tests/e2e/tests',
		filename: '[name]/indexSpec.js'
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
			{ test: /\.js$/, use: 'babel-loader' }
		]
	},
	plugins: [
		new webpack.DefinePlugin( {
			'process.env': {
				NODE_ENV: JSON.stringify( 'production' )
			}
		} )
	],
	devtool: 'cheap-module-eval-source-map'
};

module.exports = config;
