<template>
    <div
        data-vue-component-name="DialogEditPrice"
    >
        <template>
            <v-layout row justify-center>
                <v-dialog v-model="open" persistent max-width="600px">
                    <!--<template v-slot:activator="{ on }">-->
                    <!--<v-btn color="primary" dark v-on="on">Open Dialog</v-btn>-->
                    <!--</template>-->
                    <v-card>
                        <v-card-title class="headline grey lighten-2">
                            <span class="headline">Клиент: {{ item.name }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-container grid-list-md>
                                <v-layout wrap>
                                    <v-flex xs12 sm6 md4>
                                        <v-text-field label="Legal first name*" required></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm6 md4>
                                        <v-text-field label="Legal middle name"
                                                      hint="example of helper text only on focus"></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm6 md4>
                                        <v-text-field
                                            label="Legal last name*"
                                            hint="example of persistent helper text"
                                            persistent-hint
                                            required
                                        ></v-text-field>
                                    </v-flex>
                                    <v-flex xs12>
                                        <v-text-field label="Email*" required></v-text-field>
                                    </v-flex>
                                    <v-flex xs12>
                                        <v-text-field label="Password*" type="password" required></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm6>
                                        <v-select
                                            :items="['0-17', '18-29', '30-54', '54+']"
                                            label="Age*"
                                            required
                                        ></v-select>
                                    </v-flex>
                                    <v-flex xs12 sm6>
                                        <v-autocomplete
                                            :items="['Skiing', 'Ice hockey', 'Soccer', 'Basketball', 'Hockey', 'Reading', 'Writing', 'Coding', 'Basejump']"
                                            label="Interests"
                                            multiple
                                        ></v-autocomplete>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                            <small>*indicates required field</small>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" flat @click="open = false">Close</v-btn>
                            <v-btn color="blue darken-1" flat @click="savePriceData">Save</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-layout>
        </template>
    </div>
</template>

<script>
    export default {
        name: 'DialogEditPrice',
        props: {
            open: {
                type: Boolean,
                default: false,
            },
            item: {
                type: Object,
                default: () => ({}),
            },
        },
        data: () => ({
            dialog: false,
        }),
        methods: {
            savePriceData () {

            },
        },
    };
</script>
