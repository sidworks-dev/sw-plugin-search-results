import template from './sidworks-search-results-list.html.twig';

const { Criteria } = Shopware.Data;

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
            criteria.addSorting(Criteria.sort('timesSearched', 'DESC')); // <-- sorting added here

            this.searchResultsRepository
                .search(criteria, Shopware.Context.api)
                .then((result) => {
                    this.searchResultsRepositoryItems = result;
                    this.total = result.total;
                });
        },

        updateTotal({ total }) {
            this.total = total;
        },

        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.getList();
        }
    },

    computed: {
        dateFilter() {
            return Shopware.Filter.getByName('date');
        },
        columns() {
            return [
                {
                    property: 'searchTerm',
                    dataIndex: 'searchTerm',
                    label: this.$t('sidworks-search-results.list.searchTerm'),
                },
                {
                    property: 'timesSearched',
                    dataIndex: 'timesSearched',
                    label: this.$t('sidworks-search-results.list.timesSearched')
                }
            ];
        }
    },

    created() {
        this.searchResultsRepository = this.repositoryFactory.create('sidworks_search_results');
        this.getList();
    }
};
