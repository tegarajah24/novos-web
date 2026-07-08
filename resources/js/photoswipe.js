import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'

function openPhotoSwipe(files, startIndex = 0) {
    const dataSource = files
        .filter(function (f) {
            if (f.type && f.type.startsWith('image/')) return true
            if (!f.type && (f.url || f.path) && f.name) return true
            return false
        })
        .map(function (f) {
            return {
                src: f.path || f.url,
                width: f.width || 1200,
                height: f.height || 1200,
                alt: f.name || 'Image',
            }
        })

    if (!dataSource.length) return

    const lightbox = new PhotoSwipeLightbox({
        dataSource,
        index: startIndex,
        pswpModule: () => import('photoswipe'),
        bgOpacity: 0.9,
    })

    lightbox.on('uiRegister', function () {
        lightbox.pswp.ui.registerElement({
            name: 'download-button',
            order: 8,
            isButton: true,
            tagName: 'a',
            html: {
                isCustomSVG: true,
                inner: '<path d="M20.5 14.3 17.1 18V10h-2.2v7.9l-3.4-3.6L10 16l6 6.1 6-6.1ZM23 23H9v2h14Z" id="pswp__icn-download"/>',
                outlineID: 'pswp__icn-download'
            },
            title: 'Download',
            onInit: (el, pswp) => {
                el.setAttribute('download', '')
                el.setAttribute('target', '_blank')
                el.setAttribute('rel', 'noopener')
                pswp.on('change', () => {
                    el.href = pswp.currSlide.data.src
                })
            }
        })
    })

    lightbox.init()
    lightbox.loadAndOpen(startIndex)
}

window.openPhotoSwipe = openPhotoSwipe
