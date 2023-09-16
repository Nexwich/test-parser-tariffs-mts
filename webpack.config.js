const path = require('path');

module.exports = {
  entry: './src/js/app.js',
  output: {
    filename: 'js/app.min.js',
    path: path.resolve(__dirname, 'assets'),
  },
  devtool: 'source-map',
  module: {
    rules: [{
      test: /\.js$/,
      include: path.resolve(__dirname, 'src'),
      use: {
        loader: 'babel-loader',
        options: {
          presets: ['@babel/preset-env'],
        },
      },
    }],
  },
};
