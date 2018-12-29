<template>
    <div data-component-name="">
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
                                    <div v-if="dialogChangeInside === 'period'">
                                        ОТ:
                                        <date-picker :value.sync="pickersValues.period1"></date-picker>
                                        ДО:
                                        <date-picker :value.sync="pickersValues.period2"></date-picker>
                                    </div>
                                    <!--Выбор дня-->
                                    <div v-if="dialogChangeInside === 'day'">
                                        Дата:
                                        <date-picker :value.sync="day"></date-picker>
                                    </div>
                                    <!--Выбор года-->
                                    <div v-if="dialogChangeInside === 'year'">
                                        Дата:
                                        <year-picker :value.sync="pickersValues.year"></year-picker>
                                    </div>
                                    <!--Выбор месяца-->
                                    <div v-if="dialogChangeInside === 'month'">
                                        Месяц:
                                        <month-picker :value.sync="pickersValues.month"></month-picker>
                                    </div>
                                    <!--Выбор недели-->
                                    <div v-if="dialogChangeInside === 'week'">
                                        День недели:
                                        <date-picker :value.sync="pickersValues.week"></date-picker>
                                        <div><span>Понедельник:</span> {{ monday }}</div>
                                        <div><span>Воскресенье:</span> {{ sunday }}</div>

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
    import { mapGetters } from 'vuex';
    import { Date } from '~/utils';

    const date = new Date();

    export default {
        components: {
            MonthPicker,
            DatePicker,
            YearPicker
        },
        data: () => ({
            pickersValues: {
                period1: null,
                period2: null,
                day: null,
                year: null,
                month: null,
                monday: null,
                sunday: null,
                week: null
            },
            day: null,
            value: null,
            dialog: false,
            dialogChangeInside: null,
            dialogTitle: null,
            select: 'Все даты',
            savedPrevSelect: null,
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
            ]
        }),
        computed: {
            ...mapGetters({
                // select: 'controlPanel/getSelect',
                // items: 'controlPanel/getItems',
            }),
            viewDialogTitle () {
                return this.dialogTitle;
            },
            monday(){

                if(this.pickersValues.week){
                    const obj = date.choiceWeek(this.pickersValues.week);
                    this.pickersValues.monday = obj.monday;
                    return this.pickersValues.monday;
                }
            },

            sunday(){

                if(this.pickersValues.week){
                    const obj = date.choiceWeek(this.pickersValues.week);
                    this.pickersValues.sunday = obj.sunday;
                    return this.pickersValues.sunday;
                }
            },
        },
        watch: {
            pickersValues (){
                console.log('FFFF');
                const obj = date.choiceWeek(this.week);
                this.pickersValues.monday = obj.monday;
                this.pickersValues.sunday = obj.sunday;

            },
            select (val) {
                this.$store.commit('cargo/SET_CURRENTDATE', val);
            },
            day (val) {
                console.log('DAY', val);
                this.$store.commit('cargo/SET_CURRENTDATE', {
                    item: 'day',
                    value: val
                });
            }
        },
        methods: {

            changeDate (title) {
                this.dialogChangeInside = null;

                switch (title) {
                    case 'Все даты':
                        this.savePrevSelect(title);
                        this.$store.commit('cargo/SET_CURRENTDATE', {
                            item: title,
                            value : null
                        });
                        break;

                    case 'Сегодня':
                        this.$store.commit('cargo/SET_CURRENTDATE', {
                            item: title,
                            value : [date.today()]
                        });

                        this.setSelectValue(date.today());
                        this.$store.dispatch('cargo/changeList');
                        break;

                    case 'Текущий месяц':
                        this.savePrevSelect(title);
                        this.select = date.currentMonth();
                        break;

                    case 'Текущий год':
                        this.savePrevSelect(title);
                        this.select = date.currentYear();
                        break;
                    case 'Текущая неделя':
                        this.select = date.currentWeek();
                        break;

                    case 'Выбрать период':
                        this.dialogChangeInside = 'period';
                        this.setDialogTitle(title);
                        this.dialog = true;
                        break;

                    case 'Выбрать день':
                        this.dialogChangeInside = 'day';
                        this.setDialogTitle(title);
                        this.dialog = true;
                        break;

                    case 'Выбрать год':
                        this.dialogChangeInside = 'year';
                        this.setDialogTitle(title);
                        this.dialog = true;
                        break;

                    case 'Выбрать месяц':
                        this.dialogChangeInside = 'month';
                        this.setDialogTitle(title);
                        this.dialog = true;
                        break;

                    case 'Выбрать неделю':
                        this.dialogChangeInside = 'week';
                        this.setDialogTitle(title);
                        this.dialog = true;
                        break;

                    default:
                        this.select = title;
                }
            },

            nextDate () {
                const selectDate = this.select;
                if (date.isValid(selectDate)) {
                    this.select = date.next(this.select);
                }
            },

            prevDate () {
                const selectDate = this.select;
                if (date.isValid(selectDate)) {
                    this.select = date.prev(this.select);
                }
            },
            onClickOk () {
                this.dialog = false;
            },
            clickCancel () {
                this.dialog = false;
                this.select = this.savedPrevSelect;
            },
            savePrevSelect (title) {
                if (!this.select) {
                    this.select = 'Все даты';
                    return;
                }
                this.savedPrevSelect = title;
            },
            setDialogTitle (title) {
                this.dialogTitle = title;
            },
            setSelectValue (value) {
                setTimeout(() => {
                    this.select = value;
                });
            }
        }
    }
</script>

