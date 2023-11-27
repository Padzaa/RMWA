<script>
import {Line} from 'vue-chartjs';
import {Bar} from 'vue-chartjs';
import Dialog from '../../Shared/Dialog.vue';
import ADCards from "../../Shared/AdminDashboardCards.vue";
import {
    Chart as ChartJS,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'
import dialog from "../../Shared/Dialog.vue";


ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    BarElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)
export default {
    computed: {
        dialog() {
            return dialog
        }
    },
    props: {
        title: String,
        users: Array,
        recipes: Array,
        public_recipes: Array,
        collections: Array,
        ingredients: Array,
        categories: Array,
        charts: {
            type: [Array, Object],
        },
        top_users: Array,
        activities: Array,
        last_user_logins: Array
    },
    components: {
        ADCards,
        Dialog,
        Line, Bar
    },
    data() {
        return {
            cards: [
                {
                    'title': 'Total Users',
                    'value': this.users,
                    'icon': 'mdi-account-group',
                    'fields': {
                        "First Name": "firstname",
                        "Last Name": "lastname",
                        "Email": "email",
                        "Role": "is_admin",

                    }
                },
                {
                    'title': 'Total Recipes',
                    'value': this.recipes,
                    'icon': 'mdi-book',
                    'fields':
                        {
                            "Title": 'title',
                            "Author": "user",
                            "Created At": "created_at",
                        }
                }
                ,
                {
                    'title': 'Public Recipes',
                    'value': this.public_recipes,
                    'icon': 'mdi-earth',
                    'fields':
                        {
                            "Title": 'title',
                            "Author": "user",
                            "Created At": "created_at",
                        }
                }
                ,
                {
                    'title': 'Total Collections',
                    'value': this.collections,
                    'icon': 'mdi-folder',
                    'fields':
                        {
                            "Name": "name",
                            "Author": "user",
                        }
                }
                ,
                {
                    'title': 'Available Ingredients',
                    'value': this.ingredients,
                    'icon': 'mdi-food',
                    'fields':
                        {
                            "Name": "name",
                        }
                }
                ,
                {
                    'title': 'Available Categories',
                    'value': this.categories,
                    'icon': 'mdi-tag',
                    'fields':
                        {
                            "Name": "name",
                        }
                }
            ],
            isActive: false,
            dialogTitle:
                null,
            dialogData:
                null,
            dialogFields:
                [],
            chartData: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [
                    {
                        data: []
                    }
                ],
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: true,
                color: "red",
                backgroundColor: "#0835cb",
                borderColor: "#791212",
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: "",
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 100,
                    }
                }

            },
            barData: {
                labels: [],
                datasets: [
                    {
                        data: [],
                    }
                ]
            },
            barOptions: {
                responsive: true,
                maintainAspectRatio: true,
                backgroundColor: "rgba(14,167,232,0.35)",
                borderColor: "black",
                borderWidth: 1,
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: "Top Users By Recipes Rating",
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            font: {
                                size: 15
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 15
                            }
                        }
                    }
                }
            },
            activitiesFields: {
                "Notification": "message",
                "Created At": "created_at",
                "Read at": "read_at",
            },
            userLoginsFields:{
                "First name": 'firstname',
                "Last name": 'lastname',
                "Email": 'email',
                'Login at': 'last_login',
            }

        }
    },
    methods: {
        /**
         * Sets the values for the bar chart.
         */
        setBarChartValues() {
            let file = this;
            this.top_users.forEach(function (user) {
                file.barData.labels.push(user.firstname);
                file.barData.datasets[0].data.push(user.average_rating);
            });
        },
        /**
         * Change the data that should be displayed in the chart.
         */
        changeChart(data, title) {
            data = Object.values(data);
            this.chartData.datasets = [{data: data}];
            this.chartOptions.plugins.title.text = title;
            this.chartOptions.scales.y.suggestedMax = Math.max(...data) + 10;
        },
        /**
         * Pass data to dialog and opens it
         */
        openDialogAndPassData(title, data, fields) {
            this.isActive = true;
            this.dialogTitle = title;
            this.dialogData = data;
            this.dialogFields = fields;
        },
    },
    beforeMount() {
        this.setBarChartValues();
    },
    watch: {
        dialogData: {
            handler(dialogData) {
                //
            }
        }
    }
}
</script>

<template>
    <Head :title="title"></Head>
    <div class="dashboard">
        <ADCards :cards="cards"/>
        <Dialog ref="childDialog" :dialogData="dialogData" :dialogTitle="dialogTitle" :dialogFields="dialogFields"/>

        <div class="charts">
            <div class="top-users">
                <Bar
                    id="my-chart-id2"
                    :options="barOptions"
                    :data="barData"
                />
            </div>
            <div class="monthly">
                <Line id="my-chart-id"
                      :options="chartOptions"
                      :data="chartData"
                      :key="this.chartOptions.plugins.title.text"
                />
                <div class="changeChartButtons">
                    <v-btn variant="outlined" @click="changeChart(this.charts.monthlyUsers, 'Monthly Users')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Users'">Users
                    </v-btn>
                    <v-btn variant="outlined" @click="changeChart(this.charts.monthlyRecipes, 'Monthly Recipes')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Recipes'">Recipes
                    </v-btn>
                    <v-btn variant="outlined"
                           @click="changeChart(this.charts.monthlyCollections, 'Monthly Collections')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Collections'">Collections
                    </v-btn>
                </div>

            </div>
        </div>
        <div class="additional-buttons">
            <v-btn variant="outlined"
                   @click="openDialogAndPassData('User activities',activities,this.activitiesFields)">New User
                Activities
            </v-btn>

            <v-btn variant="outlined"
                   @click="openDialogAndPassData('User logins',last_user_logins,this.userLoginsFields)">User Logins
            </v-btn>
        </div>
    </div>
</template>

<style scoped>
.dashboard {
    padding: 1em 2em;
    display: grid;
    row-gap: 1.2em;
}


.charts {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.top-users {
    display: grid;

    box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
    padding: 0 1em;
}

.monthly {
    width: 100%;
    box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
    padding: 0 1em;
}


.changeChartButtons {
    padding: 1em 2em;
    display: grid;
    gap: 1em;
    grid-auto-flow: column;
}

.additional-buttons{
    display:flex;
    gap: 10px;
}
</style>
