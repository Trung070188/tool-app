<template>
    <div>
        <QFileManagerInput :hideFileList="true" v-model="files" @update:modelValue="value => onSelected(value)"/>
        <textarea ref="textarea" :id="id">{{modelValue}}</textarea>
    </div>
</template>

<script>

import QFileManagerInput from "./QFileManagerInput";

export default {
    name: "RichtextEditor",
    data() {
        return {
            watchIgnored: true,
            files: true,
            hasEmoji: false,
            style: {position: 'absolute', zIndex: 99},
        }
    },
    methods: {

        addEmoji(e) {
            if (this.editor) {
                this.editor.insertHtml(e.native)
            }
        },
        onSelected(files) {
            if (files && files[0]) {
                if (this.editor) {
                    const fileUrl = files[0].url;
                    const html = `<p class="image"><img src="${fileUrl}" alt=""  /></p>`;
                    this.editor.insertHtml(html)
                }
            }

        }
    },
    components: {
        QFileManagerInput,
    },
    watch: {
        modelValue: function (newValue) {
            if (this.watchIgnored) {
                this.watchIgnored = false;
                return;
            }

            this.editor.setData(newValue);
        }
    },
    props: ['id', 'modelValue', 'upload', 'height', 'default', 'simple'],
    mounted: function () {
        //  console.log('aa',{a:this.upload})
        const self = this;

        CKEDITOR.config.height = this.height || 300;
        this.editor = CKEDITOR.replace(this.$refs.textarea, {
            language: 'vi'
        });

        this.editor.on('change', function (e) {

            self.hasEmoji = false;
            const data = e.editor.getData();
            self.watchIgnored = true;
            self.$emit('update:modelValue', data);
        });
    },
}
</script>

<style scoped>

</style>
