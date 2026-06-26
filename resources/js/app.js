import Alpine from 'alpinejs'
import Swal from 'sweetalert2'
import { createIcons, icons } from 'lucide'

window.Alpine = Alpine
window.Swal = Swal
window.lucide = { createIcons }

Alpine.start()

createIcons({ icons })