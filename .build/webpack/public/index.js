const webpack = require( 'webpack' );
const path = require( 'path' );

const config = {
	entry: {
		'wordlift-navigator': './src/scripts/navigator/index.js'
	},
	output: {
		path: './src/public/js',
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
	devtool: 'cheap-module-eval-source-map'
};

module.exports = config;
