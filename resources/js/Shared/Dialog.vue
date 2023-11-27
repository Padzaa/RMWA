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
        }
    }
    ,
    methods: {
        /**
         * Accepts string and removes leading and trailing quotes and trims the string
         * @param str
         * @returns {*}
         */
        formatString(str) {
            return str.replace(/^["']+|["']+$/g, '').trim();
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
         * Marks the notifications as read.
         */
        markAsRead(id) {
            Inertia.put('/notifications/' + id);
        },
        /**
         * Deletes a user
         */
        deleteUser(id) {
            Inertia.delete('/user/' + id);
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
                <table class="table table-striped">
                    <thead>

                    <tr>
                        <th>#</th>
                        <th v-for="field in Object.entries(dialogFields)" :key="field">
                            {{ field[0] }}<!--Returns title-->
                        </th>
                        <th v-if="dialogFields.hasOwnProperty('Notification')">
                            Mark As Read
                        </th>
                        <th v-if="dialogFields.hasOwnProperty('Role')">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(data, index) in newDialogData"
                        :style="[data['is_admin'] == 1 ? {'font-weight': 'bold'} : null]" :key="index">
                        <td style="font-weight: bold;">{{ index + 1 }}</td>
                        <td v-for="field in dialogFields" :key="field">
                            {{
                                field === "message" ? formatString(data['data'][field]) :
                                    field === "user" ? data[field].firstname + " " + data[field].lastname :
                                        field === "is_admin" ? data[field] == 1 ? "Admin" : "User" :
                                            field === "created_at" ? this.$utils.normalDate(data[field]) :
                                                field === "read_at" ? this.$utils.normalDate(data['read_at']) :
                                                    field === 'last_login' ? this.$utils.normalDate(data[field]) : data[field]

                            }}
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
                            v-if="dialogFields.hasOwnProperty('Role')">
                            <v-btn v-if="data['id'] != this.$page.props.auth.user.id || data['is_admin'] != 1" color="error"
                                   @click="deleteUser(data['id'])"
                                   method="delete"
                            >
                                Delete
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>

                </table>
            </v-card-text>

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
</style>
