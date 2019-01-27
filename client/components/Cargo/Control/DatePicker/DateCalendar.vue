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
                    item-text="title"
                    item-value="id"
                    return-object
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
                                    <div v-show="dialogChangeInside === 6">
                                        ОТ:
                                        <DatePicker :value.sync="datePickerValue.startDate"></DatePicker>
                                        ДО:
                                        <DatePicker :value.sync="datePickerValue.endDate"></DatePicker>
                                    </div>
                                    <!--Выбор дня-->
                                    <div v-show="dialogChangeInside === 7">
                                        Дата:
                                        <DatePicker :value.sync="datePickerValue.day"></DatePicker>
                                    </div>
                                    <!--Выбор года-->
                                    <div v-show="dialogChangeInside === 8">
                                        <YearPicker :value.sync="datePickerValue.year"></YearPicker>
                                    </div>
                                    <!--Выбор месяца-->
                                    <div v-show="dialogChangeInside === 9">
                                        <MonthPicker :value.sync="datePickerValue.month"></MonthPicker>
                                    </div>
                                    <!--Выбор недели-->
                                    <div v-show="dialogChangeInside === 10">
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
    // import DatePicker from '~/components/Cargo/Control/DatePicker/DatePicker';
    // import YearPicker from '~/components/Cargo/Control/DatePicker/YearPicker';
    // import MonthPicker from '~/components/Cargo/Control/DatePicker/MonthPicker';
    import { today, choiceWeek, currentMonth, currentYear, currentWeek, nextDate, prevDate } from '~/utils/dates';
    import { mapGetters } from 'vuex';

    export default {
        name: 'DateCalendar',
        components: {
            MonthPicker: () => import('~/components/Cargo/Control/DatePicker/MonthPicker'),
            DatePicker: () => import('~/components/Cargo/Control/DatePicker/DatePicker'),
            YearPicker: () => import('~/components/Cargo/Control/DatePicker/YearPicker'),
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
            select: {
                id: 1,
                title: 'Все даты',
            },
            items: [
                {
                    id: 1,
                    title: 'Все даты',
                },
                {
                    id: 2,
                    title: 'Сегодня',
                },
                {
                    id: 3,
                    title: 'Текущий месяц',
                },
                {
                    id: 4,
                    title: 'Текущий год',
                },
                {
                    id: 5,
                    title: 'Текущая неделя',
                },
                {
                    id: 6,
                    title: 'Выбрать период',
                },
                {
                    id: 7,
                    title: 'Выбрать день',
                },
                {
                    id: 8,
                    title: 'Выбрать год',
                },
                {
                    id: 9,
                    title: 'Выбрать месяц',
                },
                {
                    id: 10,
                    title: 'Выбрать неделю',
                },
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
                    this.select = this.sensor.title;
                }, 50);
            },
            // Слежение за значением возвращемого со store (select title)
            'sensor.title': function () {
                setTimeout(() => {
                    this.select = this.sensor.title;
                }, 50);
            },
            // Слежение за выбором дня недели
            'datePickerValue.week': function () {
                const startEndDatesOfWeek = choiceWeek(this.datePickerValue.week);

                _.assign(this.datePickerValue, startEndDatesOfWeek);
            },
        },
        methods: {
            async changeDate (objChangeDate) {
                console.log('objChangeDate258', objChangeDate);
                this.dialogChangeInside = false;

                switch (objChangeDate.id) {
                    // 'Все даты'
                    case 1:
                        this.$store.commit('cargo/SET_CURRENTDATE', objChangeDate);

                        await this.$store.dispatch('cargo/changeList');

                        break;
                    // 'Сегодня'
                    case 2:
                        objChangeDate.day = today();
                        this.$store.commit('cargo/SET_CURRENTDATE', objChangeDate);

                        await this.$store.dispatch('cargo/changeList');

                        break;
                    // Текущий месяц
                    case 3:
                        this.$store.commit('cargo/SET_CURRENTDATE', _.assign(this.datePickerValue, currentMonth(), objChangeDate));

                        await this.$store.dispatch('cargo/changeList');
                        break;
                    // Текущий год
                    case 4:
                        this.$store.commit('cargo/SET_CURRENTDATE', _.assign(this.datePickerValue, currentYear(), objChangeDate));

                        await this.$store.dispatch('cargo/changeList');
                        break;
                    // Текущая неделя
                    case 5:
                        this.$store.commit('cargo/SET_CURRENTDATE', _.assign(this.datePickerValue, currentWeek(), objChangeDate));

                        await this.$store.dispatch('cargo/changeList');
                        break;

                    case 6: // Выбрать период
                    case 7: // Выбрать день
                    case 8: // Выбрать год
                    case 9: // Выбрать месяц
                    case 10: // Выбрать неделю
                        this.dialogChangeInside = objChangeDate.id;
                        this.setDialogTitle(objChangeDate.title);
                        this.setDateTitle(objChangeDate.title);
                        this.dialog = true;
                        break;

                    default:
                        console.error('default title in DateCalendar ', objChangeDate);
                        this.select = objChangeDate;
                }
            },

            async nextDate () {
                const isDateNext = nextDate(this.select.title);
                if (isDateNext) {
                    this.$store.commit('cargo/SET_CURRENTDATE', {
                        title: 'Выбрать день',
                        day: isDateNext,
                    });

                    await this.$store.dispatch('cargo/changeList');
                }
            },

            async prevDate () {
                console.log('SELECT', this.select);
                const isDatePrev = prevDate(this.select);
                if (isDatePrev) {
                    const newObj = _.assign({}, { day: isDatePrev, id: 2 });
                    this.$store.commit('cargo/SET_CURRENTDATE', newObj);
                    await this.$store.dispatch('cargo/changeList');
                }
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
