<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Поиск по ФИАС</span>
                </div>
            </div>
            <div class="card-body">
                <v-select label="name" :filterable="false" :options="options" @search="onSearch">
                    <template slot="no-options">
                        Введите адрес...
                    </template>
                    <template slot="option" slot-scope="option">
                        <div class="d-center">
                            {{ option.value }}
                        </div>
                    </template>
                    <template slot="selected-option" slot-scope="option">
                        <div class="selected d-center">
                            {{ option.value }}
                        </div>
                    </template>
                </v-select>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name:    "Api",
        data:    () => (
            {
                query:   '',
                options: []
            }
        ),
        ready() {
            this.prepareComponent();
        },
        mounted() {
            this.prepareComponent();
        },
        methods: {
            prepareComponent() {
            },
            onSearch(search, loading) {
                loading(true);
                this.search(loading, search, this);
            },
            search: _.debounce((loading, search, vm) => {
                vm.query = search;
                vm.getResults(loading, vm);
            }, 1000),
            getResults(loading) {
                const body = {
                    query: this.query
                };
                const options = {
                    headers: {
                        'Accept':        'application/json',
                        'Content-Type':  'application/json',
                        'Cache-Control': 'no-cache',
                    }
                };
                axios.post('/api/web', body, options)
                    .then(res => {
                        if (typeof res.data.error === 'undefined') {
                            this.options = res.data;
                        }
                        else {
                            this.options = [{value: res.data.error}];
                        }

                        loading(false);
                    })
                    .catch(function (error) {
                        this.options.push(error);
                    });
            }
        }
    }
</script>

<style scoped>

</style>
