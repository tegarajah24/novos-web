import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'

function openPhotoSwipe(files, startIndex = 0) {
    const dataSource = files
        .filter(function (f) {
            if (f.type && f.type.startsWith('image/')) return true
            if (f.src || f.url || f.path) return true
            return false
        })
        .map(function (f) {
            return {
                src: f.src || f.path || f.url,
                width: f.width || 1200,
                height: f.height || 1200,
                alt: f.alt || f.name || 'Image',
            }
        })

    if (!dataSource.length) return

    const lightbox = new PhotoSwipeLightbox({
        dataSource,
        index: startIndex,
        pswpModule: () => import('photoswipe'),
        bgOpacity: 0.92,
        wheelToZoom: true,
        maxZoomLevel: 8,
        clickToCloseNonZoomable: false,
        tapAction: 'toggle-zoom',
        initialZoomLevel: (zoomLevelObject) => {
            const panAreaWidth = zoomLevelObject.panAreaSize.x;
            const panAreaHeight = zoomLevelObject.panAreaSize.y;
            const imageWidth = zoomLevelObject.elementSize.x;
            const imageHeight = zoomLevelObject.elementSize.y;
            
            if (imageWidth > 0 && imageHeight > 0) {
                const scaleX = panAreaWidth / imageWidth;
                const scaleY = panAreaHeight / imageHeight;
                return Math.min(scaleX, scaleY);
            }
            return 'fit';
        },
        secondaryZoomLevel: (zoomLevelObject) => {
            const panAreaWidth = zoomLevelObject.panAreaSize.x;
            const panAreaHeight = zoomLevelObject.panAreaSize.y;
            const imageWidth = zoomLevelObject.elementSize.x;
            const imageHeight = zoomLevelObject.elementSize.y;
            
            if (imageWidth > 0 && imageHeight > 0) {
                const scaleX = panAreaWidth / imageWidth;
                const scaleY = panAreaHeight / imageHeight;
                const fitScale = Math.min(scaleX, scaleY);
                return Math.max(fitScale * 2, 2.5);
            }
            return 2.5;
        },
    })

    lightbox.on('uiRegister', function () {
        const pswp = lightbox.pswp

        pswp.ui.registerElement({
            name: 'download-button',
            order: 8,
            isButton: true,
            tagName: 'a',
            html: {
                isCustomSVG: true,
                inner: '<path d="M20.5 14.3 17.1 18V10h-2.2v7.9l-3.4-3.6L10 16l6 6.1 6-6.1ZM23 23H9v2h14Z"/>',
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

        pswp.ui.registerElement({
            name: 'fullscreen-button',
            order: 9,
            isButton: true,
            tagName: 'button',
            html: {
                isCustomSVG: true,
                inner: '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>',
                outlineID: 'pswp__icn-fullscreen'
            },
            title: 'Fullscreen',
            onInit: (el, pswp) => {
                el.addEventListener('click', function () {
                    const target = pswp.element || document.querySelector('.pswp')
                    if (!target) return
                    if (!document.fullscreenElement) {
                        if (target.requestFullscreen) {
                            target.requestFullscreen()
                        } else if (target.webkitRequestFullscreen) {
                            target.webkitRequestFullscreen()
                        }
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen()
                        } else if (document.webkitExitFullscreen) {
                            document.webkitExitFullscreen()
                        }
                    }
                })
            }
        })
    })

    lightbox.init()
    lightbox.loadAndOpen(startIndex)
}

window.openPhotoSwipe = openPhotoSwipe
