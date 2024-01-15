<script>
export default {
    props: {
        items: {
            type: [Array, Object]
        },
        setActiveChat: {
            type: Function,
            required: true
        }
    },
    data() {
        return {
            inboxCollapsed: false
        }
    },
    methods: {
        /**
         * Collapse the inbox or expand
         */
        collapseOrExpand() {
            const chats = document.getElementById('chats');
            const inbox = document.querySelector('.inbox');
            const inboxes = document.getElementById('inbox');
            const inboxTitle = document.getElementById('inbox-title');
            inboxes.animate([
                {
                    width: 'min-content'
                },
                {
                    width: '50%'
                },
                {
                    width: '75%'
                },
                {
                    width: '100%'
                }
            ], {
                duration: 500,
                easing: 'ease-out',
                fill: 'forwards',
                direction: this.inboxCollapsed ? 'normal' : 'reverse'
            });
            this.$nextTick(() => {
                // chats.style.transform = this.inboxCollapsed ? 'translateX(0)' : 'translateX(-100%)';
                inboxTitle.style.borderBottom = '1px solid lightgray';
                this.inboxCollapsed = !this.inboxCollapsed;

            });

        }
    }
}
</script>

<template>
    <v-card
        ref="inbox"
        id="inbox"
    >
        <v-card-title
            style="height: 52px;padding: 0;display: grid;grid-template-columns: 1fr min-content;"
            id="inbox-title">
            <div style="padding: 0.5em 1em">Inbox</div>
            <div class="collapse-btn">
                <v-btn class="collapse-left"
                       :append-icon="inboxCollapsed ? 'mdi-arrow-collapse-right' : 'mdi-arrow-collapse-left'"
                       @click="collapseOrExpand()"></v-btn>
            </div>
        </v-card-title>
        <v-list :items="items"
                item-props
                :key="items"
                id="chats"
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

.min-content-layout .inbox {
    grid-template-columns: min-content 3fr;
}

#inbox {
    min-width: min-content;
    max-width: 400px;
}

#chats {
    height: calc(100% - 52px);
    overflow-y: auto;
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

.v-card-title:deep(.v-btn) {
    width: fit-content;
    height: 52px;
    font-size: 1.25em;
    box-shadow: none;
    color: black;
    border-radius: 0;
}

#chats, #inbox {
    transition: all 0.5s ease-in-out;
}

@media (max-width: 768px) {
    .collapse-btn {
        display: none;
    }
}
</style>
