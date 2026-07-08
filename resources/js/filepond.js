import * as FilePond from 'filepond'
import 'filepond/dist/filepond.min.css'

import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'

import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'

FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImageExifOrientation
)

FilePond.setOptions({
    allowMultiple: true,
    instantUpload: false,
    allowProcess: false,
    credits: false,
})

window.FilePond = FilePond

window.getFilePondFiles = function (pond, fieldName = 'files[]') {
    if (!pond) return new FormData()

    const fd = new FormData()
    const files = pond.getFiles()
    files.forEach((fileItem, i) => {
        if (fileItem.file instanceof File) {
            const name = fieldName.endsWith('[]')
                ? fieldName
                : fieldName + '[' + i + ']'
            fd.append(name, fileItem.file, fileItem.file.name)
        }
    })
    return fd
}

window.destroyFilePond = function (element) {
    const pond = typeof element === 'string'
        ? FilePond.find(document.querySelector(element))
        : element
    if (pond) pond.destroy()
}

document.addEventListener('DOMContentLoaded', () => {
    FilePond.parse(document.body)
})
