const path = require('path');
const webpack = require('webpack');
const webpack_configurator = require('../../../../tools/utils/scripts/webpack-configurator.js');

const assets_dir_path = path.resolve(__dirname, '../assets');
const manifest_plugin = webpack_configurator.getManifestPlugin();

const path_to_tlp = path.resolve(
    __dirname,
    '../../../../src/www/themes/common/tlp/'
);

const webpack_config_for_trackers = {
    entry: {
        'tracker-report-expert-mode': './report/index.js',
        'tracker-permissions-per-group': './permissions-per-group/src/index.js'
    },
    context: path.resolve(__dirname),
    output: webpack_configurator.configureOutput(assets_dir_path),
    externals: {
        codendi: 'codendi'
    },
    resolve: {
        alias: {
            // TLP is not included in FlamingParrot
            'tlp-fetch': path.join(path_to_tlp, 'src/js/fetch-wrapper.js'),
            'permission-badge': path.resolve(
                __dirname,
                '../../../../src/www/scripts/project/admin/permissions-per-group/'
            )
        }
    },
    module: {
        rules: [
            webpack_configurator.configureBabelRule(
                webpack_configurator.babel_options_karma
            ),
            webpack_configurator.rule_po_files,
            webpack_configurator.rule_vue_loader
        ]
    },
    plugins: [
        manifest_plugin,
        webpack_configurator.getVueLoaderOptionsPlugin(
            webpack_configurator.babel_options_karma
        )
    ]
};

const webpack_config_for_artifact_modal = {
    entry: './angular-artifact-modal/index.js',
    context: path.resolve(__dirname),
    output: webpack_configurator.configureOutput(assets_dir_path),
    externals: {
        tlp: 'tlp'
    },
    resolve: {
        modules: [
            'node_modules',
            // This ensures that dependencies resolve their imported modules in angular-artifact-modal's node_modules
            path.resolve(__dirname, 'node_modules')
        ],
        alias: {
            'angular-tlp': path.join(path_to_tlp, 'angular-tlp/index.js'),
            'tlp-mocks': path.join(path_to_tlp, 'mocks/index.js')
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
    plugins: [webpack_configurator.getMomentLocalePlugin()]
};

const webpack_config_for_burndown_chart = {
    entry: {
        'burndown-chart': './burndown-chart/src/burndown-chart.js'
    },
    context: path.resolve(__dirname),
    output: webpack_configurator.configureOutput(assets_dir_path),
    resolve: {
        modules: [path.resolve(__dirname, 'node_modules')],
        alias: {
            'charts-builders': path.resolve(
                __dirname,
                '../../../../src/www/scripts/charts-builders/'
            )
        }
    },
    module: {
        rules: [
            webpack_configurator.configureBabelRule(
                webpack_configurator.babel_options_karma
            ),
            webpack_configurator.rule_po_files
        ]
    },
    plugins: [manifest_plugin, webpack_configurator.getMomentLocalePlugin()]
};

if (process.env.NODE_ENV === 'watch' || process.env.NODE_ENV === 'test') {
    webpack_config_for_artifact_modal.devtool = 'cheap-module-eval-source-map';
    webpack_config_for_burndown_chart.devtool = 'cheap-module-eval-source-map';
}

if (process.env.NODE_ENV === 'production') {
    const optimized_configs = [
        webpack_config_for_trackers,
        webpack_config_for_burndown_chart
    ];

    optimized_configs.forEach(config => {
        config.plugins.concat([
            new webpack.optimize.ModuleConcatenationPlugin()
        ]);
    });

    webpack_config_for_trackers.plugins = webpack_config_for_trackers.plugins.concat(
        [
            new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: '"production"'
                }
            })
        ]
    );

    module.exports = [
        webpack_config_for_trackers,
        webpack_config_for_burndown_chart
    ];
} else {
    module.exports = [
        webpack_config_for_trackers,
        webpack_config_for_artifact_modal,
        webpack_config_for_burndown_chart
    ];
}
