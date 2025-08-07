import template from './sidworks-search-results-list.html.twig';

const {Criteria} = Shopware.Data;

export default {
    template,

    inject: [
        'acl',
        'repositoryFactory',
        'filterFactory'
    ],

    data() {
        return {
            searchResultsRepositoryItems: null,
            total: 0,
            page: 1,
            limit: 25,
            storeKey: 'grid.filter.searchResults',
            defaultFilters: [
                'search-result-filter',
                'times-searched-filter',
                'results-count-filter',
                'sales-channel-filter'
            ],
            activeFilterNumber: 0,
            filterCriteria: []
        };
    },

    watch: {
        defaultCriteria: {
            handler() {
                this.getList();
            },
            deep: true,
        },
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    methods: {
        async getList() {
            this.isLoading = true;

            const criteria = await Shopware.Service('filterService')
                .mergeWithStoredFilters(this.storeKey, this.defaultCriteria);

            this.activeFilterNumber = criteria.filters.length;

            try {
                const items = await this.searchResultsRepository.search(criteria);
                this.total = items.total;
                this.searchResultsRepositoryItems = items;
                this.isLoading = false;
                this.selection = {};
            } catch {
                this.isLoading = false;
            }
        },
        updateCriteria(criteria) {
            this.page = 1;
            this.filterCriteria = criteria;
        }
    },

    computed: {
        defaultCriteria() {
            const defaultCriteria = new Criteria(this.page, this.limit);
            defaultCriteria.addSorting(Criteria.sort('timesSearched', 'DESC'));
            defaultCriteria.addAssociation('salesChannel');

            this.filterCriteria.forEach(filter => {
                defaultCriteria.addFilter(filter);
            });

            return defaultCriteria;
        },
        searchResultsRepository() {
            return this.repositoryFactory.create('sidworks_search_results');
        },
        columns() {
            return [
                {
                    property: 'searchTerm',
                    dataIndex: 'searchTerm',
                    label: this.$t('sidworks-search-results.list.searchTerm')
                },
                {
                    property: 'timesSearched',
                    dataIndex: 'timesSearched',
                    label: this.$t('sidworks-search-results.list.timesSearched')
                },
                {
                    property: 'resultsCount',
                    dataIndex: 'resultsCount',
                    label: this.$t('sidworks-search-results.list.resultsCount')
                },
                {
                    property: 'salesChannel.name',
                    dataIndex: 'salesChannel.name',
                    label: this.$t('sidworks-search-results.list.salesChannel')
                }
            ];
        },
        listFilters() {
            return this.filterFactory.create('sidworks_search_results', {
                'search-result-filter': {
                    property: 'searchTerm',
                    type: 'string-filter',
                    criteriaFilterType: 'contains',
                    label: this.$tc('sidworks-search-results.list.searchTerm'),
                    placeholder: this.$tc('sidworks-search-results.list.searchTerm'),
                    valueProperty: 'key',
                    labelProperty: 'key'
                },
                'times-searched-filter': {
                    property: 'timesSearched',
                    type: 'number-filter',
                    label: this.$tc('sidworks-search-results.list.timesSearched'),
                    fromFieldLabel: null,
                    toFieldLabel: null,
                    fromPlaceholder: this.$tc('global.default.from'),
                    toPlaceholder: this.$tc('global.default.to'),
                },
                'results-count-filter': {
                    property: 'resultsCount',
                    type: 'number-filter',
                    label: this.$tc('sidworks-search-results.list.resultsCount'),
                    fromFieldLabel: null,
                    toFieldLabel: null,
                    fromPlaceholder: this.$tc('global.default.from'),
                    toPlaceholder: this.$tc('global.default.to'),
                },
                'sales-channel-filter': {
                    property: 'salesChannel',
                    label: this.$tc('sidworks-search-results.list.salesChannel'),
                    placeholder: this.$tc('sidworks-search-results.list.salesChannel'),
                },
            });
        },
    },

    created() {
        this.getList();
    }
};
