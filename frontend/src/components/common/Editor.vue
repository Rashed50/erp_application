<template>
    <ckeditor :editor="editor" :model-value="modelValue" @update:modelValue="updateValue" :config="editorConfig"
        placeholder="Content..." />
</template>

<script>
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
import CKEditor from '@ckeditor/ckeditor5-vue'

export default {
    name: 'Editor',

    components: {
        ckeditor: CKEditor.component
    },

    props: {
        modelValue: {
            type: String,
            default: ''
        },
    },

    emits: ['update:modelValue'],

    data() {
        return {
            editor: ClassicEditor,
            editorConfig: {
                // toolbar: [
                //     'undo', 'redo',
                //     '|',
                //     'bold', 'italic',
                //     '|',
                //     'bulletedList', 'numberedList',
                //     '|',
                //     'link'
                // ]
            }
        }
    },

    methods: {
        updateValue(value) {
            this.$emit('update:modelValue', value)
        }
    }
}
</script>

<style>
.ck-editor__editable {
    min-height: 150px;
    resize: vertical;
    overflow: auto;
}
</style>
