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
import {Inertia} from "@inertiajs/inertia";


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
        users: {
            type: [Array, Object],
        },
        recipes: {
            type: [Array, Object],
        },
        public_recipes: {
            type: [Array, Object],
        },
        collections: {
            type: [Array, Object],
        },
        ingredients: {
            type: [Array, Object],
        },
        categories: {
            type: [Array, Object],
        },
        charts: {
            type: [Array, Object],
        },
        top_users: {
            type: [Array, Object],
        },
        activities: {
            type: [Array, Object],
        },
        last_user_logins: {
            type: [Array, Object],
        },
        users_comments: {
            type: [Array, Object],
        },
        available_years: {
            type: [Array, Object],
        },
        chosen_year: Number,
        chosen_number_of_users: Number
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
                    'type': 'user',
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
                    'type': 'recipe',
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
                    'type': 'public-recipe',
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
                    'type': 'collection',
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
                    'type': 'ingredient',
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
                    'type': 'category',
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
            cardType: null,
            requestedChartYear: this.chosen_year ?? 2023,
            available_number_of_users: [2, 4, 5, 10, 15],
            requestedTopUsers: this.chosen_number_of_users ?? 5,
            chartData: {
                labels: [],
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
            usersLoginsFields: {
                "First name": 'firstname',
                "Last name": 'lastname',
                "Email": 'email',
                'Login at': 'last_login',
            },
            usersCommentsFields: {
                "User": 'user',
                "Email": 'user_email',
                'Comment': 'comment',
            },

        }
    },
    methods: {
        /**
         * Sets the values for the bar chart.
         */
        setBarChartValues() {
            let file = this;
            console.log("UPDATING");
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
            this.chartData.labels = data.map(item => item.label);
            this.chartData.datasets = [{data: data.map(item => item.Count)}];
            this.chartOptions.plugins.title.text = title;
            this.chartOptions.scales.y.suggestedMax = Math.max(...data.map(item => item.Count)) + 10;
        },
        /**
         * Reset chart data
         */
        resetChart() {
            this.chartData.labels = [];
            this.chartData.datasets = [];
            this.chartOptions.plugins.title.text = "";
            this.chartOptions.scales.y.suggestedMax = 100;
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
        /**
         * Request chart data
         */
        requestChartYear() {
            Inertia.reload({
                only: ['charts', 'chosen_year'],
                data: {
                    year: this.requestedChartYear
                },
            });
            this.resetChart();
        }
        ,
        /**
         * Request chart data
         */
        requestTopUsersCharts() {
            Inertia.visit(this.$page.url, {
                only: ['top_users', 'chosen_number_of_users'],
                data: {
                    users: this.requestedTopUsers
                },
            });
        },

    },
    beforeMount() {
        this.setBarChartValues();
    },
}
</script>

<template>
    <Head :title="title"></Head>
    <div class="dashboard">
        <ADCards :cards="cards"/>
        <Dialog ref="childDialog" :dialogData="dialogData" :dialogTitle="dialogTitle" :dialogFields="dialogFields"
                :cardType="cardType"/>
        <div class="additional-buttons">
            <v-btn class="ch-btn" variant="outlined"
                   @click="openDialogAndPassData('User activities',activities,this.activitiesFields)">New User
                Activities
            </v-btn>

            <v-btn class="ch-btn" variant="outlined"
                   @click="openDialogAndPassData('Users last logins',last_user_logins,this.usersLoginsFields)">Users
                Last Logins
            </v-btn>

            <v-btn class="ch-btn" variant="outlined"
                   @click="openDialogAndPassData('Users comments',users_comments,this.usersCommentsFields)">Users
                Comments
            </v-btn>
        </div>
        <div class="charts">
            <div class="top-users">
                <Bar
                    id="my-chart-id2"
                    :options="barOptions"
                    :data="barData"
                />
                <v-select variant="outlined"
                          label="Select number of users"
                          :items="this.available_number_of_users"
                          v-model="requestedTopUsers"
                          @update:modelValue="requestTopUsersCharts"
                >
                </v-select>
            </div>
            <div class="monthly">
                <Line id="my-chart-id"
                      :options="chartOptions"
                      :data="chartData"
                      :key="this.chartOptions.plugins.title.text"
                />
                <div class="changeChartButtons">
                    <v-btn class="ch-btn" variant="outlined"
                           @click="changeChart(this.charts.monthlyUsers, 'Monthly Users')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Users'">Users
                    </v-btn>
                    <v-btn class="ch-btn" variant="outlined"
                           @click="changeChart(this.charts.monthlyRecipes, 'Monthly Recipes')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Recipes'">Recipes
                    </v-btn>
                    <v-btn class="ch-btn" variant="outlined"
                           @click="changeChart(this.charts.monthlyCollections, 'Monthly Collections')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Collections'">Collections
                    </v-btn>
                    <v-select variant="outlined"
                              label="Select year"
                              name="requestedChartYear"
                              :items="this.available_years"
                              v-model="requestedChartYear"
                              @update:modelValue="requestChartYear()"
                    >
                    </v-select>

                </div>

            </div>
        </div>

    </div>
</template>

<style scoped>
.dashboard {
    padding: 1em 2em;
    display: grid;
    row-gap: 1.2em;
}

.ch-btn {
    height: max-content !important;
    padding: 1.3em 1em;
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

.additional-buttons {
    display: flex;
    gap: 10px;
}
</style>
