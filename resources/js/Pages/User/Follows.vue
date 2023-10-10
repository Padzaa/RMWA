<script>
export default {
  props: {
    follows: Object,
    my_followers: Object
  }
}
</script>

<template>

  <Head>
    <title>Follows</title>
  </Head>
<div class="cont">
    <h1>Follows</h1>
    <div class="tables">

        <table class="table table-striped">
            <caption>Followings</caption>
            <thead>

            <tr>
                <th>#</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
            </tr>

            </thead>
            <tbody>
            <tr v-for="follow in follows">
                <td>{{ follows.indexOf(follow) + 1 }}</td>
                <td><Link :href="'/user/' + follow.id">{{ follow.firstname }}</Link></td>
                <td>{{ follow.lastname }}</td>
                <td>{{ follow.email }}</td>
            </tr>
            </tbody>
        </table>
        <table class="table table-striped">
            <caption>My Followers</caption>
            <thead>
            <tr>
                <th>#</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
            </tr>

            </thead>
            <tbody>
            <tr v-for="follow in my_followers">
                <td>{{ my_followers.indexOf(follow) + 1 }}</td>
                <td><Link :href="'/user/' + follow.id">{{ follow.firstname }}</Link></td>
                <td>{{ follow.lastname }}</td>
                <td>{{ follow.email }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

</template>

<style scoped>
.tables {
  display:flex;
  justify-content:space-between;
  padding: 2em;
  gap:20px;
  ;
}
tr{
  height: 35px !important;
}
.table{
  border: 1px solid gray;
}
caption{
  caption-side: top;
}
.cont{
  padding: 2em 0;

}
.cont>h1{
    text-align: center;
}
</style>
