<script>
export default {
    data() {
        return {
            chatbotInbox: false,
            message: '',
        }
    }
}
</script>

<template>
    <v-btn class="chatbot" icon="mdi-chat" @click="chatbotInbox = !chatbotInbox"></v-btn>

    <v-card v-if="chatbotInbox"
            class="chatbot-inbox"
    >
        <v-card-title
            style="display: grid;justify-content: space-between;grid-auto-flow: column;border-bottom: 1px solid lightgray;">
            Technical Support
            <v-icon @click="chatbotInbox = !chatbotInbox">mdi-close</v-icon>
        </v-card-title>
        <div class="chatbot-inbox-content">
            <span
                class="text-center font-italic text-grey-darken-2 mt-1">Request techical support and someone will join as soon as possible.</span>
            <v-btn color="primary" style="justify-self: center">Request Technical
                Support
            </v-btn>
        </div>
        <v-card-actions>
            <v-text-field label="Message"
                          v-model="message"
                          append-inner-icon="mdi-send"
                          hide-details
                          required
            >

            </v-text-field>
        </v-card-actions>
    </v-card>
</template>

<style scoped>

.chatbot {
    background: #8d158d;
    color: white;
    height: 75px;
    width: 75px;
    font-size: 25px;
    position: fixed;
    bottom: 20px;
    right: 15px;
    z-index: 9999;
    box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;

}

.chatbot-inbox {
    position: absolute;
    width: 300px;
    height: 400px;
    right: 110px;
    bottom: 0;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px;
    display: grid;
    grid-template-rows: min-content 1fr min-content;
}

.chatbot-inbox-content {
    display: grid;
    grid-template-rows: min-content min-content 1fr;
    row-gap: 0.7em;
    overflow-y: auto;
}
</style>
