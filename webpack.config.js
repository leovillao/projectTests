const path = require('path');

module.exports = {
  mode: 'development',
  entry: './App.js',
  output: {
    path: path.resolve(__dirname),
    filename: 'js/react/main.js',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-react'],
          },
        },
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      }
    ],
  },
};