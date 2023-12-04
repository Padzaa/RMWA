<script>
import {Inertia} from "@inertiajs/inertia";
import {capitalize} from "vue";

export default {
    props: {
        dialogData: {
            type: [Array, Object],
        },
        dialogTitle: String,
        dialogFields: {
            type: [Array, Object],
        },
        cardType: String,
    },
    data() {
        return {
            checked: [],
            newDialogData: [],
            checkActiveDialog: false,
            checkActiveDialogData: [],
            formData: '',
        }
    }
    ,
    methods: {
        /**
         * Returns dialog heading text
         */
        getDialogTitleText(checkActiveDialogData = null) {
            if (checkActiveDialogData['action'] == 'delete') {
                return checkActiveDialogData['title'] ? 'Confirm recipe deletion' :
                    checkActiveDialogData['comment'] ? 'Confirm comment deletion' :
                        checkActiveDialogData['firstname'] ? 'Confirm user deletion' :
                            checkActiveDialogData['type'] == 'category' ? 'Confirm category deletion' :
                                checkActiveDialogData['type'] == 'ingredient' ? 'Confirm ingredient deletion' :
                                    checkActiveDialogData['type'] == 'collection' ? 'Confirm collection deletion' : ''
            }
            if (checkActiveDialogData['action'] == 'edit') {
                return 'Edit' + ' ' + checkActiveDialogData['name'];
            }
            if (checkActiveDialogData['action'] == 'add') {
                return 'Add' + ' ' + capitalize(checkActiveDialogData['type']);
            }

        },
        /**
         * Get dialog text
         */
        getDialogText(checkActiveDialogData = null) {
            if (checkActiveDialogData['action'] == 'delete') {
                return checkActiveDialogData['title'] ? checkActiveDialogData['title'] :
                    checkActiveDialogData['firstname'] ? checkActiveDialogData['firstname'] + ' ' + checkActiveDialogData['lastname'] :
                        checkActiveDialogData['type'] == 'category' ? 'Delete category' + ' ' + checkActiveDialogData['name'] :
                            checkActiveDialogData['type'] == 'ingredient' ? 'Delete ingredient' + ' ' + checkActiveDialogData['name'] :
                                checkActiveDialogData['type'] == 'collection' ? 'Delete collection' + ' ' + checkActiveDialogData['name'] : ''
            }
        },
        /**
         * Set the checked values
         */
        setCheckedValues(dialogData) {
            let checked = []
            dialogData.forEach((data) => {
                if (data['read_at']) {
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
        setCheckActiveDialogData(data = null, type = 'delete') {
            this.checkActiveDialogData = data;
            let title = this.dialogTitle;
            this.checkActiveDialogData['action'] = type;
            this.checkActiveDialogData['type'] =
                data['firstname'] ? "user" :
                    data['title'] ? "recipe" :
                        data['comment'] ? "comment" :
                            title.includes('Categories') ? "category" :
                                title.includes('Ingredients') ? "ingredient" :
                                    title.includes('Collections') ? "collection" : null;
            this.formData = type === 'edit' ? data['name'] : '';
        },
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
        },
        /**
         * Get button action
         */
        handleAction(action, id, type, formData) {
            switch (action) {
                case 'delete':
                    this.$utils.deleteItem(id, type);
                    break;
                case 'edit':
                    this.$utils.updateItem(id, type, formData);
                    break;
                case 'add':
                    this.$utils.addItem(type, formData);
                    break;
                // Add more cases if needed
                default:
                    break;
            }
        },
        /**
         * Get button label
         */
        getButtonLabel(checkActiveDialogData) {
            let label = checkActiveDialogData['type'] === 'user'
                ? 'user'
                : checkActiveDialogData['type'].charAt(0).toUpperCase() + checkActiveDialogData['type'].slice(1);
            return checkActiveDialogData['action'] == 'edit' ? 'Update' + ' ' + label : checkActiveDialogData['action'] + ' ' + label;

        },
        /**
         * Get button color
         */
        getButtonColor(action) {
            switch (action) {
                case 'delete':
                    return 'error';
                case 'edit':
                    return 'white';
                case 'add':
                    return 'blue'
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
    <v-dialog min-width="400px"
              width="fit-content"
              v-model="this.$parent.$data.isActive"
    >

        <v-card class="position-relative" :title="dialogTitle">
            <v-card-text>
                <v-btn style="position:absolute;right: 10px"
                       v-if="dialogTitle.includes('Categories') ||
                                    dialogTitle.includes('Ingredients')"
                       @click="[setCheckActiveDialogData([], 'add'),checkActiveDialog = true]"
                       color="blue">
                    Add
                </v-btn>
                <table class="table mt-2 table-striped" v-if="newDialogData.length != 0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th v-for="field in Object.entries(dialogFields)" :key="field">
                            {{ field[0] }}<!--Returns title-->
                        </th>
                        <th v-if="dialogFields.hasOwnProperty('Notification')">
                            Mark As Read
                        </th>
                        <th v-if="dialogTitle != 'Public Recipes' && !dialogTitle.includes('activities') && !dialogTitle.includes('logins') ">
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
                                v-if="field === 'firstname' || (dialogTitle.includes('Recipes') && field === 'title') || field === 'user' || dialogTitle.includes('Collections')"
                                :href="[
                                    field === 'firstname' ? '/user/' + data['id'] :
                                    field === 'user' ? '/user/' + data['user']['id'] :
                                    field === 'title' ? '/recipe/' + data['id'] :
                                    dialogTitle.includes('Collections') ? '/collection/' + data['id'] : ''
                                ]">
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
                            />
                        </td>
                        <td
                            v-if="dialogTitle != 'Public Recipes' && !dialogTitle.includes('activities') && !dialogTitle.includes('logins')">
                            <v-btn v-if="(data['id'] != this.$page.props.auth.user.id || data['is_admin'] != 1) "
                                   @click="[setCheckActiveDialogData(data),checkActiveDialog = true]"
                                   color="error">
                                Delete
                            </v-btn>
                            <span v-if="data['is_admin'] != 1">&nbsp;&nbsp;</span>
                            <v-btn
                                v-if="(data['id'] != this.$page.props.auth.user.id || data['is_admin'] != 1) && (
                                    dialogTitle == 'Total Recipes' ||
                                    dialogTitle == 'Total Users' ||
                                    dialogTitle.includes('Categories') ||
                                    dialogTitle.includes('Ingredients') ||
                                    dialogTitle.includes('Collections'))"
                                @click="dialogTitle.includes('Categories') ||
                                    dialogTitle.includes('Ingredients') ? [setCheckActiveDialogData(data,'edit'),checkActiveDialog = true] : this.$utils.editItem(data,cardType)"
                                color="white">
                                Edit
                            </v-btn>

                        </td>

                    </tr>
                    </tbody>

                </table>
                <p v-else class="text-center mt-2 fst-italic fs-5 text-grey-darken-1">No data</p>
            </v-card-text>
            <v-dialog
                v-if="dialogTitle != 'Public Recipes'"
                v-model="checkActiveDialog" class="vd"
                style="display: grid;">
                <div class="bg-white dialog">

                    <div class="dialog-header">
                        <h1 v-text="this.getDialogTitleText(checkActiveDialogData)" class="modal-title fs-4"></h1>
                        <button type="button" class="btn-close" @click="checkActiveDialog = false"
                                aria-label="Close">
                        </button>
                    </div>
                    <div class="dialog-message">
                        <p v-if="!checkActiveDialogData['comment']" v-text="this.getDialogText(checkActiveDialogData)">
                        </p>
                        <p v-if="checkActiveDialogData['comment']" style="max-width: 50ch;word-wrap: break-word">
                            Comment:
                            {{ checkActiveDialogData['comment'] }}
                        </p>

                    </div>
                    <div class="dialog-message"
                         v-if="checkActiveDialogData['action'] == 'edit' || checkActiveDialogData['action'] == 'add'"
                    >
                        <v-text-field v-model="formData" name="name" label="Name">

                        </v-text-field>
                    </div>

                    <div class="dialog-actions">
                        <v-btn class="btn btn-outline-secondary" @click="checkActiveDialog = false">
                            Cancel
                        </v-btn>
                        <v-btn
                            @click="handleAction(checkActiveDialogData['action'], checkActiveDialogData['id'], checkActiveDialogData['type'], formData)"
                            :color="getButtonColor(checkActiveDialogData['action'])"
                            :method="checkActiveDialogData['action']"
                            id="delete_recipe"
                        >
                            {{ getButtonLabel(checkActiveDialogData) }}
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
