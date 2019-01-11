<template>
    <div data-component-name="DateCalendar">
        <v-layout row wrap align-center>

            <v-flex d-flex>
                <v-icon
                    x-large
                    @click="prevDate"
                >
                    arrow_left
                </v-icon>

                <v-combobox
                    v-model="select"
                    :items="items"
                    label="Выбор даты"
                    @change="changeDate"
                ></v-combobox>

                <v-icon
                    x-large
                    @click="nextDate"
                >
                    arrow_right
                </v-icon>

            </v-flex>

        </v-layout>

        <template>
            <v-layout row justify-center>
                <!--Диалог выбора дат для выборки данных-->
                <v-dialog
                    v-model="dialog"
                    max-width="290"
                >
                    <v-card>
                        <v-card-title class="title grey lighten-2">{{ viewDialogTitle }}:</v-card-title>

                        <v-card-text>
                            <v-layout>
                                <v-flex>
                                    <!--Выбор периода-->
                                    <div v-show="dialogChangeInside === 'Выбрать период'">
                                        ОТ:
                                        <DatePicker :value.sync="datePickerValue.startDate"></DatePicker>
                                        ДО:
                                        <DatePicker :value.sync="datePickerValue.endDate"></DatePicker>
                                    </div>
                                    <!--Выбор дня-->
                                    <div v-show="dialogChangeInside === 'Выбрать день'">
                                        Дата:
                                        <DatePicker :value.sync="datePickerValue.day"></DatePicker>
                                    </div>
                                    <!--Выбор года-->
                                    <div v-show="dialogChangeInside === 'Выбрать год'">
                                        <YearPicker :value.sync="datePickerValue.year"></YearPicker>
                                    </div>
                                    <!--Выбор месяца-->
                                    <div v-show="dialogChangeInside === 'Выбрать месяц'">
                                        <MonthPicker :value.sync="datePickerValue.month"></MonthPicker>
                                    </div>
                                    <!--Выбор недели-->
                                    <div v-show="dialogChangeInside === 'Выбрать неделю'">
                                        День недели:
                                        <DatePicker :value.sync="datePickerValue.week"></DatePicker>
                                        <div><span>Понедельник:</span> {{ datePickerValue.startDate }}</div>
                                        <div><span>Воскресенье:</span> {{ datePickerValue.endDate }}</div>
                                    </div>
                                </v-flex>
                            </v-layout>
                        </v-card-text>

                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn
                                color="green darken-1"
                                flat="flat"
                                @click="onClickOk"
                            >
                                ок
                            </v-btn>

                            <v-btn
                                color="red darken-1"
                                flat="flat"
                                @click="clickCancel"
                            >
                                Отмена
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-layout>
        </template>
    </div>
</template>

<script>
    import DatePicker from '~/components/Cargo/Control/DatePicker/DatePicker';
    import YearPicker from '~/components/Cargo/Control/DatePicker/YearPicker';
    import MonthPicker from '~/components/Cargo/Control/DatePicker/MonthPicker';
    import { DateClass } from '~/utils'; // Класс для работы с датами
    import { mapGetters } from 'vuex';

    const date = new DateClass();

    export default {
        name: 'DateCalendar',
        components: {
            MonthPicker,
            DatePicker,
            YearPicker,
        },
        data: () => ({
            datePickerValue: {
                title: null,
                day: null,
                startDate: null,
                endDate: null,
                year: null,
                month: null,
                week: null,
            },
            dialog: false,
            dialogChangeInside: false,
            dialogTitle: null,
            select: 'Все даты',
            items: [
                'Все даты',
                'Сегодня',
                'Выбрать период',
                'Выбрать день',
                'Текущий год',
                'Выбрать год',
                'Текущий месяц',
                'Выбрать месяц',
                'Текущая неделя',
                'Выбрать неделю',
            ],
        }),
        computed: {
            ...mapGetters({
                sensor: 'cargo/getSensor',
            }),
            viewDialogTitle () {
                return this.dialogTitle;
            },
        },
        watch: {
            // Слежение за значением при выборе в селекте (select title)
            select () {
                setTimeout(() => {
                    this.select = this.sensor;
                }, 50);
            },
            // Слежение за значением возвращемого со store (select title)
            sensor () {
                setTimeout(() => {
                    this.select = this.sensor;
                }, 50);
            },
            // Слежение за выбором дня недели
            'datePickerValue.week': function () {
                console.log('this.datePickerValue.startDate', this.datePickerValue.startDate);
                const obj = date.choiceWeek(this.datePickerValue.week);

                this.datePickerValue.startDate = obj.monday;
                this.datePickerValue.endDate = obj.sunday;
            },
        },
        methods: {
            async changeDate (title) {
                this.dialogChangeInside = false;

                switch (title) {
                    case 'Все даты':
                        this.$store.commit('cargo/SET_CURRENTDATE', {
                            title,
                        });

                        await this.$store.dispatch('cargo/changeList');

                        break;

                    case 'Сегодня':
                        this.$store.commit('cargo/SET_CURRENTDATE', {
                            title,
                            day: date.today(),
                        });

                        await this.$store.dispatch('cargo/changeList');

                        break;

                    case 'Текущий месяц':
                        this.$store.commit('cargo/SET_CURRENTDATE', _.assign(this.datePickerValue, date.currentMonth(), {title}));

                        await this.$store.dispatch('cargo/changeList');
                        break;

                    case 'Текущий год':
                        this.$store.commit('cargo/SET_CURRENTDATE', _.assign(this.datePickerValue, date.currentYear(), {title}));

                        await this.$store.dispatch('cargo/changeList');
                        break;

                    case 'Текущая неделя':
                        this.$store.commit('cargo/SET_CURRENTDATE', _.assign(this.datePickerValue, date.currentWeek(), {title}));

                        await this.$store.dispatch('cargo/changeList');
                        break;

                    case 'Выбрать период':
                    case 'Выбрать день':
                    case 'Выбрать год':
                    case 'Выбрать месяц':
                    case 'Выбрать неделю':
                        this.dialogChangeInside = title;
                        this.setDialogTitle(title);
                        this.setDateTitle(title);
                        this.dialog = true;
                        break;

                    default:
                        console.error('default title in DateCalendar ', title);
                        this.select = title;
                }
            },

            async nextDate () {
                this.$store.commit('cargo/SET_CURRENTDATE', {
                    title: 'Выбрать день',
                    day: date.next(this.select),
                });

                await this.$store.dispatch('cargo/changeList');
                this.dialog = false;
            },

            async prevDate () {
                this.$store.commit('cargo/SET_CURRENTDATE', {
                    title: 'Выбрать день',
                    day: date.prev(this.select),
                });

                await this.$store.dispatch('cargo/changeList');
                this.dialog = false;
            },

            async onClickOk () {
                const isEmptyDatePickerValue = await this.checkDatePickerValueOnEmpty(_.cloneDeep(this.datePickerValue));
                if (isEmptyDatePickerValue) {
                    return;
                }

                this.dialog = false;
                this.$store.commit('cargo/SET_CURRENTDATE', this.datePickerValue);

                await this.$store.dispatch('cargo/changeList');
                this.resetDatePickerValue();
            },

            clickCancel () {
                this.dialog = false;
            },

            setDialogTitle (title) {
                this.dialogTitle = title;
            },

            setDateTitle (val) {
                this.datePickerValue.title = val;
            },

            resetDatePickerValue () {
                _.forIn(this.datePickerValue, (value, key, obj) => {
                    obj[key] = null;
                });
            },

            checkDatePickerValueOnEmpty (obj) {
                _.unset(obj, 'title', 'week');
                const result = _.pickBy(obj, _.isString);

                return _.isEmpty(result);
            },
        },
    };
</script>
