const path = require('path');
const webpack = require('webpack');
const webpack_configurator = require('../../../../tools/utils/scripts/webpack-configurator.js');

const assets_dir_path = path.resolve(__dirname, '../assets');

const webpack_config = {
    entry: {
        'tuleap-pullrequest': './src/app/app.js',
        'move-button-back': './move-button-back.js'
    },
    context: path.resolve(__dirname),
    output: webpack_configurator.configureOutput(assets_dir_path),
    resolve: {
        modules: ['node_modules', 'bower_components'],
        alias: {
            'tuleap-pullrequest-module': path.resolve(
                __dirname,
                './src/app/app.js'
            ),
            'angular-ui-bootstrap-templates':
                'angular-ui-bootstrap-bower/ui-bootstrap-tpls.js',
            'angular-ui-select': 'ui-select/dist/select.js'
        }
    },
    module: {
        rules: [
            webpack_configurator.configureBabelRule(
                webpack_configurator.babel_options_karma
            ),
            webpack_configurator.rule_ng_cache_loader,
            webpack_configurator.rule_angular_gettext_loader
        ]
    },
    plugins: [
        webpack_configurator.getManifestPlugin(),
        webpack_configurator.getMomentLocalePlugin()
    ]
};

if (process.env.NODE_ENV === 'production') {
    webpack_config.plugins.push(
        new webpack.optimize.ModuleConcatenationPlugin()
    );
} else if (process.env.NODE_ENV === 'watch') {
    webpack_config.devtool = 'eval';
    webpack_config.module.rules.push(
        webpack_configurator.rule_angular_gettext_extract_loader
    );
} else if (process.env.NODE_ENV === 'test') {
    webpack_config.devtool = 'eval';
}

module.exports = webpack_config;
