var masterProducers = new Vue({
    el: '#master_producers',
    data: {
        producers: null,
        url: api.routes.masterProducers,
        loading: false
    },
    ready: function() {
        this.getLatest();
    },
    methods: {
        getLatest: function() {
            this.loading = true;
            this.$http.get(this.url).then(function(response) {
                this.producers = response.body.producers;
                this.loading = false;
            });
        },
        getNextPage: function (event) {
            var self = this
            if(self.loading == false) {
                self.loading = true;
                self.$http.post(self.producers.next_page_url, self.filter).then(function(response) {
                    var producers = self.producers
                    response.body.producers.data.forEach(function(producer) {
                        producers.data.push(producer)
                    });
                    self.producers.next_page_url = response.body.producers.next_page_url
                    self.loading = false;
                });
            }
            event.preventDefault()
        }
    }
});