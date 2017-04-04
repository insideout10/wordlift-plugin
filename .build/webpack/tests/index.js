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
	devtool: 'cheap-module-eval-source-map',
	node: { process: false }
};

module.exports = config;
