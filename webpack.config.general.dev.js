'use strict'
const webpack = require('webpack')
const { VueLoaderPlugin } = require('vue-loader')
const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin'); 
const ManifestPlugin = require('webpack-manifest-plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const CopyWebpackPlugin = require('copy-webpack-plugin')

let cleanOptions = {
  verbose:  true,
  dry:      false,
  cleanOnceBeforeBuildPatterns: ['*bundle.js'],
}

module.exports = {
  mode: process.env.NODE_ENV,
  entry: {
    main: ['./resources/js/babelpolyfill.js','./resources/js/backend.js'],
    front: ['./resources/js/babelpolyfill.js','./resources/js/front.js'],
  },
  output: {
    filename: '[name].[hash].bundle.js',
    path: path.resolve(__dirname, 'dist'),
    chunkFilename: '[name].[hash].bundle.js',
  },
  watchOptions: {
    poll: true,
    aggregateTimeout: 2000,
    ignored: ['resources/Standalone/*.js', 'node_modules']
  },
/*   devServer: {
    hot: true,
    
  }, */
  resolve: {
    extensions: ['.vue', '.js'],
    enforceExtension: false,
    mainFiles: ['index']
  },

  module: {
    rules: [
      {
        test: /\.vue$/,
        use: [
          {
            loader: 'vue-loader',
            options: {
              loaders: {
                  'scss': 'vue-style-loader!css-loader!sass-loader',
                  'sass': 'vue-style-loader!css-loader!sass-loader?indentedSyntax'
              }
            }
          }
        ],
      },
      {
        test: /\.css$/,
        use: [
          'vue-style-loader',
          {
            loader: 'css-loader',
          }
        ],
      },
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: [
          'babel-loader'
        ]
      },
      {
        test: /\.scss$/,
        use: [
          'css-loader',
          'sass-loader'
        ]
      },
      {
        test: /\.(png|jpg|gif)$/,
        use: [
          {
            loader: 'file-loader',
            options: {},
          },
        ],
      },
    ]
  },
  plugins: [
    new webpack.IgnorePlugin({
        resourceRegExp: /^\.\/locale$/,
        contextRegExp: /moment$/
    }),
    new CleanWebpackPlugin(cleanOptions),
    new WebpackAssetsManifest({
      // Options go here
    }),
    new VueLoaderPlugin(),
    new webpack.HashedModuleIdsPlugin(),
    /*new BundleAnalyzerPlugin({
      analyzerHost: '192.168.101.10'
    })*/
  ], 
}