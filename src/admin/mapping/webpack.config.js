const path = require( 'path' );
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
	entry: {
		mapping: './assets/src/js/mapping.js',
	},
	output: {
		path: path.resolve( __dirname, 'assets/dist/js' ),
		filename: '[name].js',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader'
			},
			{
				test: /\.s[ac]ss$/i,
				use: [
					{
						loader: MiniCssExtractPlugin.loader
					},
					{
						loader: 'css-loader'
					},
					{
						loader: 'sass-loader'
					},
				],
			}
		]
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: '[name].css',
		}),
	],
};
