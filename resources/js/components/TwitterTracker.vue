<template>
    <div class="twitter-tracker">
        <h1>Twitter Tracker</h1>
        <div>
            <input
                type="text"
                v-model="query"
                placeholder="Enter Query (without #)"
                @keyup.enter="fetchTweets"
            />
            <button @click="fetchTweets">Fetch Tweets</button>
            {{ tweets.length }}
        </div>
        <div v-if="loading">Loading tweets...</div>
        <div v-if="error" class="error">{{ error }}</div>
        {{tweets.length}}
        <ul v-if="tweets.length > 0">
            <li v-for="tweet in tweets" :key="tweet.id">
                <p>{{ tweet.text }}</p>
                <small>By: {{ tweet.author_id }} on {{ tweet.created_at }}</small>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            query: '',
            tweets: [],
            loading: false,
            error: null,
        };
    },
    methods: {
        async fetchTweets() {
          console.log('starting');
            this.error = null;
            this.tweets = {};
            if (!this.query) {
                this.error = 'Please enter a your query.';
                return;
            }
            this.loading = true;
            try {
                const response = await axios.post('api/twitter-track?q=' + this.query, {});
                // Twitter API returns data under a "data" property
                this.tweets = response.data['tweets'] || [];
                console.log(this.tweets);
            } catch (err) {
                console.error(err);
                this.error = 'Error fetching tweets.';
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.twitter-tracker {
    max-width: 600px;
    margin: 2rem auto;
    font-family: Arial, sans-serif;
}

.twitter-tracker input {
    padding: 0.5rem;
    font-size: 1rem;
    border: solid 1px #ccc;
}

.twitter-tracker button {
    padding: 0.5rem 1rem;
    font-size: 1rem;
    background-color: gray;
}

.twitter-tracker ul {
    list-style: none;
    padding: 0;
}

.twitter-tracker li {
    padding: 1rem;
    color: black;
    border-bottom: 1px solid #ccc;
}

.error {
    color: red;
}
</style>
