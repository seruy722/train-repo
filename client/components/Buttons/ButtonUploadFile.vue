<template>

    <div
        data-vue-component-name="ButtonUploadFile"
    >
        <input
            ref="inputUploadFile"
            type="file"
            class="display-none_input"
            @change="uploadedFile"
        >

        <v-btn
            :loading="loading3"
            :disabled="loading3"
            :color="buttonUploadFileSettings.color"
            :class="buttonUploadFileSettings.class"
            @click="onSelectFile"
        >
            Добавить файл
            <v-icon right dark>{{ buttonUploadFileSettings.icon }}</v-icon>
        </v-btn>
    </div>

</template>

<script>
    export default {
        name: 'ButtonUploadFile',
        props: {
            file: {
                type: FormData,
                default: () => new FormData(),
            },
            buttonUploadFileSettings: {
                type: Object,
                default: () => null,
            },
        },
        data: () => ({
            loading3: false,
        }),
        methods: {
            uploadedFile (event) {
                if (event.target.files && event.target.files.length) {
                    const file = event.target.files[0];
                    const { name, size } = event.target.files[0];
                    const dataFile = new FormData();

                    dataFile.append('uploadedFile', file);
                    dataFile.append('fileName', name);
                    dataFile.append('fileSize', size);

                    this.$emit('update:file', dataFile);

                    // Очистка input
                    this.$refs.inputUploadFile.value = null;
                }
            },
            onSelectFile () {
                this.$refs.inputUploadFile.click();
            },
        },
    };
</script>
