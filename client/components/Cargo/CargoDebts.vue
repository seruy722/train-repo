<template>
    <v-card>
        <!--<v-card-title-->
            <!--class="grey lighten-4 py-4 title"-->
        <!--&gt;-->
            <!--<span class="headline">{{ dialogTitle }}</span>-->
        <!--</v-card-title>-->
        <v-card-actions>
            <v-btn class="white--text main_color-bg" @click="addEl">
                <span>Долг</span>
                <v-icon>add</v-icon>
            </v-btn>
        </v-card-actions>

        <v-container grid-list-sm class="pa-4">
            <v-layout row wrap class="borderForEntry" v-for="(item, index) in getMass" :key="index">

                <!--ДАТА-->
                <v-flex xs12 sm2 md2>
                    <DatePicker :value.sync="dd"></DatePicker>
                </v-flex>

                <v-flex xs12 sm3 md3>
                    <v-text-field
                        v-model="item.type"
                        :error-messages="checkError('type')"
                        prepend-icon="person"
                        autofocus
                        placeholder="Тип"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm3 md3>
                    <v-text-field
                        v-model="item.client"
                        :error-messages="checkError('client')"
                        prepend-icon="person"
                        placeholder="Клиент"
                        autofocus
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm2 md2>
                    <v-text-field
                        v-model="item.sum"
                        :error-messages="checkError('sum')"
                        prepend-icon="monetization_on"
                        placeholder="Сумма"
                        autofocus
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm2 md2>
                    <v-text-field
                        v-model="item.place"
                        :error-messages="checkError('place')"
                        prepend-icon="phone"
                        placeholder="Мест"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm2 md2>
                    <v-text-field
                        v-model="item.kg"
                        :error-messages="checkError('kg')"
                        prepend-icon="notes"
                        placeholder="Вес"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm4 md4>
                    <v-text-field
                        v-model="item.fax"
                        :error-messages="checkError('fax')"
                        prepend-icon="notes"
                        placeholder="Факс"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm5 md5>
                    <v-text-field
                        v-model="item.notation"
                        :error-messages="checkError('notation')"
                        prepend-icon="notes"
                        placeholder="Примечание"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm1 md1>
                    <v-btn
                        v-if="index > 0"
                        fab
                        small
                        dark
                        color="error"
                        @click="deleteObj(item)"
                    >
                        <v-icon dark>delete</v-icon>
                    </v-btn>
                </v-flex>

            </v-layout>
        </v-container>

        <v-card-actions>

            <v-spacer></v-spacer>

            <v-btn
                v-if="mass.length"
                fab
                small
                dark
                color="primary"
                @click=""
            >
                <v-icon dark>save</v-icon>
            </v-btn>

            <v-btn
                fab
                small
                dark
                color="error"
                @click="clearAndCloseComponent"
            >
                <v-icon dark>clear</v-icon>
            </v-btn>

            <v-spacer></v-spacer>

        </v-card-actions>
    </v-card>
</template>
<script>
    import axios from 'axios';
    import DatePicker from '~/components/Cargo/Control/DatePicker/DatePicker';
    import { formatDate } from '~/utils';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        components: {
            DatePicker
        },
        name: 'CreateCargoEntry',
        middleware: 'auth',
        mixins: [checkErrorMixin],
        head () {
            return {title: 'Карго'};
        },
        props:['showComponent'],
        data () {
            return {
                search: '',
                loadOnBtn: false, // Оверлей для кнопки
                selected: [],
                dd: '',
                mass: [
                    {
                        type: null,
                        date: '',
                        sum: null,
                        sale: null,
                        client: null,
                        place: null,
                        kg: null,
                        fax: null,
                        notation: null,
                    }
                ],
                headers: [
                    {
                        text: 'Дата',
                        align: 'center',
                        value: 'created_at'
                    },
                    { text: 'Тип', align: 'center', value: 'type' },
                    { text: 'Сумма', align: 'center', value: 'sum' },
                    { text: 'Клиент', align: 'center', value: 'client' },
                    { text: 'Мест', align: 'center', value: 'place' },
                    { text: 'Вес', align: 'center', value: 'kg' },
                    { text: 'Факс', align: 'center', value: 'fax' },
                    { text: 'Примечания', align: 'center', value: 'notation' },
                ],
                defaultItem: {
                    type: null,
                    date:'',
                    sum: null,
                    client: null,
                    place: null,
                    kg: null,
                    fax:null,
                    notation: null,
                },
            }
        },
        computed: {
            getMass(){
                return this.mass;
            },
            dialogTitle () {
                return 'Добавление записей';
            },
        },
        methods: {
            clearAndCloseComponent(){
                this.$store.commit('controlPanel/SET_OPENEDCOMPONENT', false);
                this.mass = [];
                this.addEl();
            },
            deleteObj(elem){
               const elemIndex = _.indexOf(this.mass, elem);
               this.mass.splice(elemIndex, 1);
            },
            addEl(){
                this.mass.push(_.assign({}, this.defaultItem));
            },
            changeLoadBtn () {
                this.loadOnBtn = !this.loadOnBtn;
            },
            async save () {
                this.changeLoadBtn();
                this.changeErrors({});
                // Если удалено название изображения значит не отправляем изображение на сервер
                if (!this.imageName) {
                    this.editedItem.file = null;
                }
                // Если editedIndex > -1 значит это обновление записи
                if (this.editedIndex === -1) {
                    // СОХРАНЕНИЕ ДАННЫХ
                    this.editedItem.type = 'save';
                } else {
                    // ОБОВЛЕНИЕ ДАННЫХ
                    this.editedItem.type = 'update';
                }
                // Очищаем обьект от ложных данных
                const cleanedEditItem = _.pickBy(this.editedItem, _.identity);
                // Для отправки файлов помещаем свойства обьекта editedItem в FormData
                const data = new FormData();

                _.forEach(cleanedEditItem, (value, key) => {
                    if (!value) {
                        data.append(key, '');
                    } else {
                        data.append(key, value);
                    }
                });

                await this.saveItemToServer(data).then((response) => {
                    const {status} = response.data;
                    const {type} = response.data;
                    let {item} = response.data;
                    // Форматируем дату
                    item = formatDate(item, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

                    if (status) {
                        this.close();
                        this.saveLog(item, type);

                        if (type === 'update') {
                            const data = {item, index: this.editedIndex};
                            this.updateItemInStorage(data);
                            this.$snotify.success('Запись успешно обновлена', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
                            });
                        } else if (type === 'save') {
                            this.addItemToStorage(item);
                            this.$snotify.success('Запись успешно добавлена!', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
                            });
                        }
                    }
                }).catch((errors) => {
                    this.changeLoadBtn();
                    this.changeErrors(errors.response.data.errors);
                });
            },
            async saveItemToServer (item) {
                return await axios.post('blacklist/saveUpdate', item);
            },
            async deleteItemFromServer (id) {
                return await axios.post('blacklist/delete', {id});
            },
        }
    }
</script>

<style scoped>
    .borderForEntry {
        border-bottom: 1px solid #0080FF;
    }
</style>
