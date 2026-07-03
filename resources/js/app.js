import Alpine from 'alpinejs'
import Swal from 'sweetalert2'
import { createIcons, icons } from 'lucide'

window.Swal = Swal
window.lucide = { createIcons, icons }
window.Alpine = Alpine

Alpine.start()
createIcons({ icons })
