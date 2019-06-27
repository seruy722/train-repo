<template>
    <div
        data-vue-component-name="Mailing"
    >
        <h3>PRIVET</h3>
        <DialogUploadFile
            :click-save-in-edit-dialog="clickSaveInEditDialog"
            :event="'sendFileToServer'"
            @sendFileToServer="sendFileToServer"
        />
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: 'Mailing',
        components: {
            DialogUploadFile: () => import('~/components/Dialogs/DialogUploadFile'),
        },
        data () {
            return {
                clickSaveInEditDialog: false,
            };
        },
        methods: {
            async sendFileToServer () {
                // Загрузка данных файла на сервер СЕРВЕРНАЯ ЧАСТЬ
                // $ImportedFaxArray = Excel::toArray(new FaxesImport, $file);
                // $newArray = [];
                // foreach ($ImportedFaxArray as $item) {
                //     foreach ($item as $key => $elem) {
                //         if (stripos($elem[1], '06') !== false || stripos($elem[1], '07') !== false) {
                //             if (stripos($elem[4], '+7') === false) {
                //                 $arr = ['name' => $elem[1], 'phone' => $elem[4]];
                //                 array_push($newArray, $arr);
                //             }
                //         }
                //     }
                // }
                try {
                    const { data } = await axios.post('faxes/storeFax', this.fileForUpload);
                    const { status, fax = [], groupedData = [], elem = [] } = data;
                    // console.log('ELEM', elem);
                    axios({
                        url: 'fax/download',
                        method: 'POST',
                        responseType: 'blob', // important
                        data: {
                            faxData: elem,
                        },
                    }).then((response) => {
                        if (!window.navigator.msSaveOrOpenBlob) {
                            // BLOB NAVIGATOR
                            const url = window.URL.createObjectURL(new Blob([response.data]));
                            const link = document.createElement('a');
                            link.href = url;
                            link.setAttribute('download', 'phones.xlsx');
                            document.body.appendChild(link);
                            link.click();
                        } else {
                            // BLOB FOR EXPLORER 11
                            window.navigator.msSaveOrOpenBlob(new Blob([response.data]), 'phones.xlsx');
                        }
                    });
                    if (status) {
                        // console.log('groupedData', groupedData);

                        this.$store.dispatch('fax/addFax', fax);
                        this.$store.dispatch('fax/setGroupedData', groupedData);

                        this.openCloseAddFaxDialog(false);
                        this.clearData();

                        this.$snotify.success('Факс успешно добавлен.', {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                    }
                } catch (e) {
                    console.error(`Произошла ошибка при загрузки файла на сервер - ${e}`);
                } finally {
                    console.log('Completed request to upload file on server.');
                }
            },
        },
    };
</script>
