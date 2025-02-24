const TerserJSPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts");
const { VueLoaderPlugin } = require('vue-loader')
const webpack = require('webpack');
const path = require('path');

const isProduction = process.argv[process.argv.indexOf('--mode') + 1] === 'production';

module.exports = {
    mode: 'production',
    entry: {
        package: './public/src/package.js',
    },
    resolve: {
        modules: ['node_modules'],
        alias: {
            kernel: path.resolve(__dirname, 'd://web/home/libs/raas.kernel/public/src'),
            app: path.resolve(__dirname, 'public/src/'),
            jquery: path.resolve(__dirname, 'node_modules/jquery/dist/jquery'),
            cms: path.resolve(__dirname, 'd:/web/home/libs/raas.cms/resources/js.vue3'),
            'fa-mixin': path.resolve(__dirname, 'd:/web/home/libs/raas.cms/resources/js.vue3/_shared/mixins/fa6.scss'),
            "./dependencyLibs/inputmask.dependencyLib": "./dependencyLibs/inputmask.dependencyLib.jquery"
        },
        extensions: [
            '.scss',
            '.js',
            '.vue',
        ]
    },
    output: {
        filename: '[name].js',
        path: __dirname+'/public',
        publicPath: '/vendor/volumnet/raas.cms/public/',
    },
    optimization: {
        minimizer: [
            new TerserJSPlugin({ 
                extractComments: false,
                terserOptions: { format: { comments: false, }}
            }),
        ],
    },
    externals: {
        knockout: 'knockout',
        jquery: 'jQuery',
        $: 'jquery',
        'window.jQuery': 'jquery',
        vue: 'Vue', // Иначе при рендеринге компоненты будут тянуть за собой копию Vue
    },
    devtool: (isProduction ? false : 'inline-source-map'),
    module: {
        rules: [
            {
                test: /\.js$/,
                use: 'babel-loader',
                exclude: /node_modules/
            },
            {
                test: /\.scss$/,
                use: [
                    { loader: MiniCssExtractPlugin.loader },
                    { loader: "css-loader", options: {url: false}, },
                    {
                        loader: 'postcss-loader', // Run postcss actions
                        options: {
                            postcssOptions: {
                                plugins: [
                                    ['postcss-utilities', { centerMethod: 'flexbox' }], 
                                    'autoprefixer',
                                    'rucksack-css',
                                    'postcss-short',
                                    'postcss-combine-duplicated-selectors',
                                    'postcss-pseudo-elements-content',
                                ],
                            },
                        },
                    },
                    {
                        loader: "sass-loader",
                        options: {
                            additionalData: "@use 'kernel/_shared/init.scss' as *;\n",
                        },
                    },
                ]
            },
            {
                test: /\.css$/,
                use: [
                    { loader: MiniCssExtractPlugin.loader },
                    { loader: "css-loader", options: {url: false}, },
                ]
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.(png|svg|jpg|jpeg|gif)$/,
                loader: 'file-loader',
                options: { 
                    outputPath: './img', 
                    name: '[name].[ext]', 
                }
            },
            {
                test: /(\.(woff|woff2|eot|ttf|otf))|(font.*\.svg)$/,
                loader: 'file-loader',
                options: { 
                    outputPath: './fonts', 
                    name: '[name].[ext]',
                }
            },
            {
                test: /\.json$/,
                loader: 'json-loader'
            },
        ],
    },
    plugins: [
        new VueLoaderPlugin(),
        new webpack.ProvidePlugin({
            knockout: 'knockout',
        }),
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin({ filename: './[name].css' }),
    ]
}