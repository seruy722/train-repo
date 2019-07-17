<template>
    <div
        data-vue-component-name="DialogAddClient"
    >
        <v-layout row justify-center>
            <v-dialog v-model="open" persistent max-width="500px" min-width="300px">
                <v-card>
                    <v-card-title class="headline grey lighten-2"
                                  primary-title>
                        <span>Добавление клиента</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-layout wrap>
                                <v-flex xs12 sm12 md12>
                                    <v-text-field
                                        v-model="clientData.code"
                                        :error-messages="checkError('code')"
                                        label="Код"
                                        prepend-icon="code"
                                        autofocus
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm12 md12>
                                    <v-text-field
                                        v-model="clientData.phone"
                                        :error-messages="checkError('phone')"
                                        label="Телефон"
                                        mask="+##(###) ###-##-##"
                                        prepend-icon="phone"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm12 md12>
                                    <v-text-field
                                        v-model="clientData.city"
                                        :error-messages="checkError('city')"
                                        label="Город"
                                        prepend-icon="location_city"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field
                                        v-model="clientData.name"
                                        :error-messages="checkError('name')"
                                        label="Имя"
                                        prepend-icon="person"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm12 md12>
                                    <v-select
                                        v-model="clientData.sex"
                                        :items="sex"
                                        :error-messages="checkError('sex')"
                                        label="Пол"
                                        item-text="title"
                                        item-value="id"
                                        hide-selected
                                        prepend-icon="wc"
                                    ></v-select>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat @click="close">Отмена</v-btn>
                        <v-btn color="blue darken-1" flat @click="addClient">Сохранить</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-layout>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import axios from 'axios';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        name: 'DialogAddClient',
        mixins: [checkErrorMixin],
        props: {
            open: {
                type: Boolean,
                default: false,
            },
        },
        data () {
            this.$_data = {
                code: '',
                phone: '',
                name: '',
                city: '',
                sex: '',
            };

            return {
                clientData: {
                    code: '',
                    phone: '',
                    name: '',
                    city: '',
                    sex: '',
                },
            };
        },
        computed: {
            ...mapGetters({
                sex: 'settings/sex',
            }),
        },
        methods: {
            clearData (data, emptyData) {
                _.assign(data, emptyData);
            },
            close () {
                this.$emit('update:open', false);
                this.clearData(this.clientData, this.$_data);
                this.changeErrors({});
            },
            upperCase (obj) {
                _.forEach(obj, (elem, key, obj) => {
                    if (key === 'name' || key === 'city') {
                        const arr = _.split(elem, ' ');
                        obj[key] = _.join(_.map(arr, item => _.upperFirst(item)), ' ');
                    }
                });
            },
            addPlusToPhoneNumber (obj) {
                obj.phone = `+${obj.phone}`;
            },
            async addClient () {
                this.changeErrors({});
                const clientObj = _.clone(this.clientData);
                await this.addPlusToPhoneNumber(clientObj);
                await this.upperCase(clientObj);
                try {
                    const { data } = await axios.post('users/add', clientObj);
                    const { client = null } = data;

                    if (client) {
                        this.$snotify.success(`Клиент: ${client} успешно добавлен`, {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                        this.close();
                    } else {
                        this.$snotify.warning(`Клиент: ${this.clientData.code} уже есть в базе`, {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                    }
                } catch (errors) {
                    this.changeErrors(errors.response.data.errors);
                    console.error(`Ошибка при добавлении клиента - ${errors.response.data.errors}`);
                }
            },
        },
    };
</script>
