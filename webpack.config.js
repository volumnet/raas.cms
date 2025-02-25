const path = require('path');

const config = require('d:/web/home/libs/raas.kernel/webpack.config.inc.js');

config.entry = {
    package: path.resolve('./public/src/package.js'),
};
config.output.publicPath = '/vendor/volumnet/raas.cms/public/';

module.exports = config;