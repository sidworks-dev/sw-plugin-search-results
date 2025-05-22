import template from './sidworks-search-results-list.html.twig';

const {Criteria} = Shopware.Data;

export default {
    template,

    inject: [
        'acl',
        'repositoryFactory'
    ],

    data() {
        return {
            searchResultsRepository: null,
            searchResultsRepositoryItems: null,
            total: 0
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    methods: {
        getList() {
            const criteria = new Criteria();
            criteria.addSorting(Criteria.sort('timesSearched', 'DESC'));
            criteria.addAssociation('salesChannel');

            this.searchResultsRepository
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.searchResultsRepositoryItems = result;
                    this.total = result.total;
                });
        }
    },

    computed: {
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
        }
    },

    created() {
        this.searchResultsRepository = this.repositoryFactory.create('sidworks_search_results');
        this.getList();
    }
};
