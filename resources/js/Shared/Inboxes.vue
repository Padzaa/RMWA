<script>
export default {
    props:{
        items:{
            type:[Array,Object]
        },
        setActiveChat:{
            type:Function,
            required:true
        }
    }
}
</script>

<template>
    <v-card
        class="mx-auto"
        max-width="400"
        width="400"
        title="Inbox"
    >
        <v-list :items="items"
                item-props
                :key="items"
        >
            <template v-for="item in items">
                <v-list-item :ref="item.inbox_id" :key="item.inbox_id" v-if="item.title"
                             :prepend-avatar="item.prependAvatar"
                             :title="item.title"
                             @click="setActiveChat(item)">
                    <v-list-item-content>
                        <div v-html="item.subtitle" class="msg-content"></div>
                    </v-list-item-content>
                </v-list-item>
                <v-divider v-if="item.type"></v-divider>
            </template>
        </v-list>

    </v-card>
</template>

<style scoped>
.msg-content {
    white-space: pre-wrap;
    word-break: break-word;
    text-overflow: ellipsis !important;
}

*:deep(.v-divider) {
    margin: 0 !important;
}

.v-list {
    padding: 0px;
}

.v-list:deep(.v-list-item__content) {
    max-height: 50px;
    white-space: pre-wrap;
    word-break: break-word;
    text-overflow: ellipsis !important;
}

.v-list .v-list-item {
    padding: 1em 0;
    max-height: 100px;
}

</style>
