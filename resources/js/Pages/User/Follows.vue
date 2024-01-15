<script>
export default {
    props: {
        follows: {
            type: [Object, Array],
        },
        my_followers: {
            type: [Object, Array],
        },
    },
    data() {
        return {
            tables: [this.follows, this.my_followers]
        }
    },
    mounted() {
        this.follows.caption = "Followings";
        this.my_followers.caption = "My Followers";
    }
}
</script>

<template>

    <Head>
        <title>Follows</title>
    </Head>

    <div class="cont">
        <h1>Follows</h1>
        <p class="text-center fs-4 fw-bold text-red-accent-3 font-italic bigger-screen">In order to see tables, you
            have to switch to the bigger screen.</p>
        <div class="tables">
            <table class="table table-striped" v-for="table in tables">
                <caption>{{ table.caption }}</caption>
                <thead>

                <tr>
                    <th>#</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Email</th>
                    <th>Followed by</th>
                    <th>Follows</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="follow in table">
                    <td>{{ table.indexOf(follow) + 1 }}</td>
                    <td>
                        <Link :href="'/user/' + follow.id">{{ follow.firstname }}</Link>
                    </td>
                    <td>{{ follow.lastname }}</td>
                    <td>{{ follow.email }}</td>
                    <td>{{ follow.my_followers_count }}</td>
                    <td>{{ follow.followed_by_me_count }}</td>
                </tr>
                </tbody>

            </table>
        </div>
    </div>

</template>

<style scoped>
.bigger-screen {
    display: none;
}

.tables {
    display: flex;
    justify-content: space-between;
    padding: 2em;
    gap: 20px;
}

tr {
    height: 35px !important;
}

.table {
    border: 1px solid grey;
    height: fit-content;
}

caption {
    caption-side: top;
}

.cont {
    padding: 2em 0;

}

.cont > h1 {
    text-align: center;
}

@media (max-width: 1200px) {
    .tables {
        flex-direction: column;
    }
}

@media (max-width: 680px) {
    .tables {
        display: none;
    }

    .bigger-screen {
        display: block;
        padding: 0 1em;
    }
}
</style>
