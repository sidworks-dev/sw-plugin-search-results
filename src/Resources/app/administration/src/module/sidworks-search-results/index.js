import './acl';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Shopware.Component.register('sidworks-search-results-list', () => import('./page/sidworks-search-results-list'));

Module.register('sidworks-search-results', {
    type: 'plugin',
    name: 'Sidworks Search Results',
    title: 'sidworks-search-results.general.pluginTitle',
    color: '#FFD700',

    snippets: {
        'en-GB': enGB
    },

    routes: {
        index: {
            components: {
                default: 'sidworks-search-results-list',
            },
            path: 'index'
        },
        create: {
            component: 'sidworks-search-results-detail',
            path: 'create',
            meta: {
                parentPath: 'sidworks.search.results.index'
            }
        }
    },

    navigation: [
        {
            id: 'sidworks-search-results-module',
            path: 'sidworks.search.results.index',
            parent: 'sw-marketing',
            label: 'sidworks-search-results.general.pluginTitle',
            position: 10
        }
    ]
});
