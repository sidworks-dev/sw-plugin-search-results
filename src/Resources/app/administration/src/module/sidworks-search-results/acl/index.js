Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: null,
    key: 'sidworks_search_results',
    roles: {
        viewer: {
            privileges: ['sidworks_search_results:read'],
            dependencies: []
        },
        editor: {
            privileges: [
                'sidworks_search_results:update',
                'sidworks_search_results_sales_channel:create',
                'sidworks_search_results_sales_channel:delete'
            ],
            dependencies: []
        },
        creator: {
            privileges: [
                'sidworks_search_results:create',
                'sidworks_search_results_sales_channel:create',
                'sidworks_search_results_sales_channel:delete'
            ],
            dependencies: []
        },
        deleter: {
            privileges: ['sidworks_search_results:delete'],
            dependencies: []
        }
    }
});
