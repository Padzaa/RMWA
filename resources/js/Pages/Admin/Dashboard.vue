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
            available_number_of_users: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
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
                            stepSize: 1,
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
            this.chartData.datasets = [{data: data.map(item => item.count)}];
            this.chartOptions.plugins.title.text = title;
            this.chartOptions.scales.y.suggestedMax = Math.max(...data.map(item => item.count)) + 10;
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
            <v-btn class="ch-btn"
                   @click="openDialogAndPassData('User activities',activities,this.activitiesFields)">New User
                Activities
            </v-btn>

            <v-btn class="ch-btn"
                   @click="openDialogAndPassData('Users last logins',last_user_logins,this.usersLoginsFields)">User
                Last Logins
            </v-btn>

            <v-btn class="ch-btn"
                   @click="openDialogAndPassData('Users comments',users_comments,this.usersCommentsFields)">User
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
                          class="selectNOU"
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
                    <v-btn class="ch-btn"
                           @click="changeChart(this.charts.monthlyUsers, 'Monthly Users')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Users'">Users
                    </v-btn>
                    <v-btn class="ch-btn"
                           @click="changeChart(this.charts.monthlyRecipes, 'Monthly Recipes')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Recipes'">Recipes
                    </v-btn>
                    <v-btn class="ch-btn"
                           @click="changeChart(this.charts.monthlyCollections, 'Monthly Collections')"
                           :disabled="this.chartOptions.plugins.title.text === 'Monthly Collections'">Collections
                    </v-btn>
                    <v-select variant="outlined"
                              label="Select year"
                              name="requestedChartYear"
                              :hide-details="true"
                              :items="this.available_years"
                              v-model="requestedChartYear"
                              @update:modelValue="requestChartYear()"
                    >
                    </v-select>

                </div>

            </div>
            <p class="text-center fs-4 fw-bold text-red-accent-3 font-italic bigger-screen">In order to see charts, you
                have to switch to the bigger screen.</p>
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
    height: 100% !important;
    padding: 1.3em 1em;
    box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;

}

.additional-buttons:deep(.v-btn) {
    padding: 1.3em 2em;
}

.charts {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;

}

.top-users {
    box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
    padding: 0 1em;
    border-radius: 0.375rem;
}

.monthly {
    box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
    padding: 0 1em;
    border-radius: 0.375rem;

}

.top-users > canvas,
.monthly > canvas {
    max-width: 100%;
}

.changeChartButtons {
    padding: 1em 2em;
    display: grid;
    gap: 20px;
    grid-auto-flow: column;
}

.additional-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    column-gap: 20px;
    row-gap: 15px;

}

.bigger-screen {
    display: none;
}

@media (max-width: 1440px) {
    .charts {
        grid-template-columns: unset;
        grid-auto-flow: row;
    }
}

@media (max-width: 640px) {
    .bigger-screen {
        margin-top: 2em;
        display: block;
    }

    .top-users, .monthly {
        display: none;
    }
}
</style>
