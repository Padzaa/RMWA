<script>
  export default {
    props:{
      user:Object,
      is_following:Boolean
    }
  }
</script>

<template>
  <Header></Header>
  <Head>
    <title>{{user.firstname + " " + user.lastname}}</title>
  </Head>
  <div class="container rounded bg-white mt-5 mb-5">


      <div class="row justify-content-center">
        <h2 class="text-center">Profile Settings</h2>

        <div class="col-md-3 border-right">
          <div class="d-flex flex-column align-items-center text-center p-3 py-5">

            <label for="fileToUpload">
              <div class="profile-pic">
                <img :src="user.picture ? user.picture : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png'" alt="">
              </div>
            </label>

            <span class="font-weight-bold">{{ user.firstname }}</span>
            <span class="text-black-50">{{ user.email }}</span>
            <span> </span>
          </div>
        </div>
        <div class="col-md-5 border-right d-flex">
          <div class="p-3 py-5  align-self-center">

            <div class="row mt-2">
              <div class="col-md-6"><label class="labels">Firstname</label><input type="text"
                                                                                  name="firstname"
                                                                                  class="form-control"
                                                                                  placeholder="Firstname"
                                                                                  :value="user.firstname"
              disabled>

              </div>
              <div class="col-md-6"><label class="labels">Lastname</label><input type="text"
                                                                                 name="lastname"                                                                                           class="form-control"
                                                                                 :value="user.lastname"
                                                                                 disabled
                                                                                 ></div>

            </div>
            <div class="row mt-3">

              <div class="col-md-12"><label class="labels">Email</label><input type="email"
                                                                               name="email"
                                                                               class="form-control"
                                                                               disabled
                                                                               :value="user.email"></div>

            </div>
            <div class="mt-5 text-center">

            </div>
          </div>
        </div>
        <div class="follow">
          <Link :href="'/user/' + user.id + '/follow'" method="PUT" :class="is_following ? 'btn-danger' : 'btn-primary'" class="btn">{{is_following ? 'Unfollow' : 'Follow'}}</Link>
        </div>
      </div>

  </div>
</template>

<style scoped>

.profile-pic>img{
  height: 150px;
  width:150px;
  border-radius: 50%;
  border:1px solid black;
}
div.follow{
  display: grid;
  justify-content: center;
}
div.follow > a {
  padding: 0.25em 5em
}
</style>
