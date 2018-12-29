/**
 * Заруск и остановка прогресс бара в таблице
 */
export default {
    data: () => ({
        progressLoading: true
    }),
    methods: {
        runProgressbarOnTable () {
            this.progressLoading = true;
        },
        stopProgressbarOnTable () {
            this.progressLoading = false;
        },
    }
}
