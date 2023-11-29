<script>
import {Inertia} from "@inertiajs/inertia";

export default {
    props: {
        dialogData: {
            type: [Array, Object],
        },
        dialogTitle: String,
        dialogFields: {
            type: [Array, Object],
        },
    },
    data() {
        return {
            checked: [],
            newDialogData: [],
            checkActiveDialog: false,
            checkActiveDialogData: [],
        }
    }
    ,
    methods: {
        /**
         * Decides what to delete
         */
        deleteItem(id, type) {
            switch (type) {
                case 'firstname':
                    this.$utils.deleteUser(id);
                    break;
                case 'title':
                    this.$utils.deleteRecipe(id);
                    break;
                case 'comment':
                    this.$utils.deleteComment(id);
                    break;
                // Add more cases as needed
            }
        },
        /**
         * Set the checked values
         */
        setCheckedValues(dialogData) {
            let checked = []
            dialogData.forEach((data) => {
                if (data['read_at']) {
                    // checked.push({
                    //     "id": data['id'],
                    //     "read_at": data['read_at'],
                    // });

                    checked.push(data['id']);
                }


            });
            return checked;
        },
        /**
         * Update checked values
         */
        updateNewDialogData(id) {
            this.dialogData.find(item => item.id === id).read_at = Date.now();
        },
        /**
         * Set the data for the check dialog when deleting a user
         */
        setCheckActiveDialogData(data) {
            this.checkActiveDialogData = data;
            this.checkActiveDialogData['type'] =
                data['firstname'] ? "firstname" :
                    data['title'] ? "title" : "comment";
        }

        ,
        /**
         * Marks the notifications as read.
         */
        markAsRead(id) {
            let notifications = this.$utils.getNotifications();
            notifications = notifications.filter(obj => obj.id != id);
            this.$parent.$parent.$data.not_key_len = notifications.length;
            sessionStorage.setItem('notifications', JSON.stringify(notifications));
            Inertia.put('/notifications/' + id);
        },
        /**
         * Get field content
         */
        getFieldContent(field, data) {
            switch (field) {
                case "message":
                    return this.$utils.formatString(data['data'][field]);
                case "user":
                    return data[field].firstname + " " + data[field].lastname;
                case "is_admin":
                    return data[field] == 1 ? "Admin" : "User";
                case "created_at":
                    return this.$utils.normalDate(data[field]);
                case "read_at":
                    return this.$utils.normalDate(data[field]);
                case "last_login":
                    return this.$utils.normalDate(data[field]);
                case "user_email":
                    return data['user'].email;
                case "comment":
                    return data[field].substring(0, 50) + "...";
                default:
                    return data[field];
            }
        }

    },
    watch: {
        dialogData: {
            handler(dialogData) {
                if (dialogData) {
                    // Prop is defined, update your data property
                    this.checked = this.setCheckedValues(dialogData);
                    this.newDialogData = dialogData;


                }
            },
            immediate: true, // Trigger the watcher immediately when the component is created
        },
    },
}

</script>

<template>
    <v-dialog :dialogData="dialogData"
              :dialogTitle="dialogTitle"
              :dialogFields="dialogFields"
              min-width="400px"
              width="fit-content"
              v-model="this.$parent.$data.isActive"
    >

        <v-card class="position-relative" :title="dialogTitle">
            <v-card-text>

                <table class="table table-striped" v-if="newDialogData.length != 0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th v-for="field in Object.entries(dialogFields)" :key="field">
                            {{ field[0] }}<!--Returns title-->
                        </th>
                        <th v-if="dialogFields.hasOwnProperty('Notification')">
                            Mark As Read
                        </th>
                        <th v-if="dialogFields.hasOwnProperty('Role') || (dialogFields.hasOwnProperty('Title') && dialogTitle == 'Total Recipes') || dialogTitle=='Users comments'">
                            Actions
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="(data, index) in newDialogData"
                        :style="[data['is_admin'] == 1 ? {'font-weight': 'bold'} : null]" :key="index">
                        <td style="font-weight: bold;">{{ index + 1 }}</td>
                        <td v-for="field in dialogFields" :key="field">
                            <Link
                                v-if="field === 'firstname' || (dialogTitle.includes('Recipes') && field === 'title') || field === 'user'"
                                :href="[field === 'firstname' ? '/user/' + data['id'] : '/recipe/' + data['id']]">
                                {{
                                    this.getFieldContent(field, data)
                                }}
                            </Link>
                            <span v-else>{{ this.getFieldContent(field, data) }}</span>
                        </td>
                        <td style="display: grid;justify-content: center"
                            v-if="dialogFields.hasOwnProperty('Notification')">
                            <v-checkbox-btn v-model="checked" :value="data['id']"
                                            :disabled="checked.some(item => item == data['id'])"
                                            @click="[updateNewDialogData(data['id']),markAsRead(data['id'])]"
                            >

                            </v-checkbox-btn>
                        </td>
                        <td
                            v-if="dialogFields.hasOwnProperty('Role') || (dialogFields.hasOwnProperty('Title') && dialogTitle == 'Total Recipes') || dialogTitle=='Users comments'">
                            <v-btn v-if="(data['id'] != this.$page.props.auth.user.id || data['is_admin'] != 1) "
                                   @click="[setCheckActiveDialogData(data),checkActiveDialog = true]"
                                   color="error">
                                Delete
                            </v-btn>
                            <span v-if="data['is_admin'] != 1">&nbsp;&nbsp;</span>
                            <v-btn
                                v-if="(data['id'] != this.$page.props.auth.user.id || data['is_admin'] != 1) && dialogTitle == 'Total Recipes'"
                                @click="this.$utils.editRecipe(data['id'])"
                                color="blue">
                                Edit
                            </v-btn>
                            <v-btn
                                v-if="(data['id'] != this.$page.props.auth.user.id || data['is_admin'] != 1) && dialogTitle == 'Total Users'"
                                @click="this.$utils.editUser(data['id'])"
                                color="blue">
                                Edit
                            </v-btn>
                        </td>

                    </tr>
                    </tbody>

                </table>
                <p v-else class="text-center mt-2 fst-italic fs-5 text-grey-darken-1">No data</p>
            </v-card-text>
            <v-dialog
                v-if="dialogFields.hasOwnProperty('Role') || (dialogFields.hasOwnProperty('Title') && dialogTitle == 'Total Recipes') || dialogTitle=='Users comments'"
                v-model="checkActiveDialog" class="vd"
                style="display: grid;">
                <div class="bg-white dialog">

                    <div class="dialog-header">

                        <h1 v-if="checkActiveDialogData['title']" class="modal-title fs-4">Confirm recipe deletion</h1>
                        <h1 v-if="checkActiveDialogData['comment']" class="modal-title fs-4">Comment by
                            {{
                                checkActiveDialogData['user']['firstname'] + ' ' + checkActiveDialogData['user']['lastname']
                            }}</h1>
                        <h1 v-if="checkActiveDialogData['firstname']" class="modal-title fs-4">Confirm user
                            deletion</h1>
                        <button type="button" class="btn-close" @click="checkActiveDialog = false"
                                aria-label="Close">
                        </button>
                    </div>
                    <div class="dialog-message">
                        <p v-if="!checkActiveDialogData['comment']">Are you sure you want to delete a user
                            <b>{{
                                    checkActiveDialogData['firstname'] ? checkActiveDialogData['firstname'] + ' ' + checkActiveDialogData['lastname'] :
                                        checkActiveDialogData['title'] ? checkActiveDialogData['title'] : ""
                                }}</b>
                            permanently?</p>
                        <p v-else-if="checkActiveDialogData['firstname']">
                            When you delete a certain user, all of their recipes, comments, reviews and collections will
                            be
                            deleted as well.
                        </p>
                        <p v-else style="max-width: 50ch;word-wrap: break-word">
                            Comment:
                            {{ checkActiveDialogData['comment'] }}
                        </p>

                    </div>
                    <div class="dialog-actions">
                        <v-btn class="btn btn-outline-secondary" @click="checkActiveDialog = false">
                            Cancel
                        </v-btn>
                        <v-btn
                            v-if="checkActiveDialogData['firstname'] || checkActiveDialogData['title'] || checkActiveDialogData['comment']"
                            @click="deleteItem(checkActiveDialogData['id'], checkActiveDialogData['type'])"
                            method="DELETE"
                            color="error" id="delete_recipe"
                        >
                            Delete {{
                                checkActiveDialogData['type'] === 'firstname' ? 'User' :
                                    checkActiveDialogData['type'].charAt(0).toUpperCase() + checkActiveDialogData['type'].slice(1)
                            }}
                        </v-btn>

                    </div>

                </div>
            </v-dialog>
            <v-card-actions style="right:0;" class="position-absolute">
                <v-spacer></v-spacer>
                <v-btn
                    text="Close"
                    append-icon="mdi-close"
                    variant="outlined"
                    @click="this.$parent.$data.isActive = false"
                ></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style scoped>
td {
    vertical-align: middle;
}

.vd {
    display: grid;
    align-items: start;
    justify-content: center;
}

.vd > div {
    align-items: center;
}

.dialog-header {
    display: grid;
    grid-template-columns: 5fr min-content;
}

.dialog-message {
    font-size: 1.1rem;
}

.dialog-actions {
    display: grid;
    gap: 1em;
    grid-auto-flow: column;
}

.dialog {
    display: grid;
    padding: 1em;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    width: fit-content;
    align-self: center;
    gap: 1em;

}
</style>
