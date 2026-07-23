@if(request()->ajax())
{{-- AJAX response: only content sections --}}
<div id="ajax-topbar-title">@yield('topbar-left')</div>
<div id="ajax-page-title">@yield('title', 'Admin Panel') - {{ config('app.name', 'Novos') }}</div>
<div id="ajax-content">@yield('internal-content')</div>
@stack('scripts')
@else
{{-- Layout Internal --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Novos') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/filepond.js', 'resources/js/photoswipe.js'])
    @notifyCss
    @stack('styles')
    
    <style>
        :root {
            --color-primary: #1a237e;
            --color-primary-rgb: 26, 35, 126;
            --color-secondary: #3949ab;
            --font-size-base: 15px;
            --radius-base: 12px;
            --font-family-base: 'Poppins';

        }

        * { font-family: var(--font-family-base), sans-serif; font-size: var(--font-size-base); }

        @media (max-width: 767px) {
            :root { --font-size-base: 14px !important; }
        }
        [x-cloak] { display: none !important; }

        body.internal-body {
            background: linear-gradient(135deg, #f4f6f9 0%, #eef2f7 100%) !important;
            transition: background 0.4s ease, color 0.3s ease;
            color: #212121;
        }

        /* ── Background decorative SVG layers ── */
        .bg-svg {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            object-fit: cover;
            object-position: top center;
        }
        .bg-svg-dark { display: none; }
        /* Keep content above the background layers */
        body.internal-body > .flex-1,
        body.internal-body > .contents,
        body.internal-body > aside {
            position: relative;
            z-index: 1;
        }

        /* ── Dynamic Brand Color Mappings ── */
        .bg-\[\#1a237e\] { background-color: var(--color-primary) !important; }
        .bg-\[\#1a237e\]\/5 { background-color: rgba(var(--color-primary-rgb), 0.05) !important; }
        .bg-\[\#1a237e\]\/10 { background-color: rgba(var(--color-primary-rgb), 0.1) !important; }
        .bg-\[\#1a237e\]\/15 { background-color: rgba(var(--color-primary-rgb), 0.15) !important; }
        .bg-\[\#1a237e\]\/20 { background-color: rgba(var(--color-primary-rgb), 0.2) !important; }
        .bg-\[\#1a237e\]\/30 { background-color: rgba(var(--color-primary-rgb), 0.3) !important; }
        .bg-\[\#1a237e\]\/80 { background-color: rgba(var(--color-primary-rgb), 0.8) !important; }
        .bg-\[\#1a237e\]\/90 { background-color: rgba(var(--color-primary-rgb), 0.9) !important; }
        .hover\:bg-\[\#1a237e\]\/90:hover { background-color: rgba(var(--color-primary-rgb), 0.9) !important; }
        .hover\:bg-\[\#283593\]:hover { background-color: var(--color-secondary) !important; }
        .hover\:bg-\[\#1a237e\]:hover { background-color: var(--color-primary) !important; }
        .hover\:bg-\[\#1a237e\]\/5:hover { background-color: rgba(var(--color-primary-rgb), 0.05) !important; }
        .hover\:bg-\[\#1a237e\]\/10:hover { background-color: rgba(var(--color-primary-rgb), 0.1) !important; }
        .hover\:bg-\[\#1a237e\]\/20:hover { background-color: rgba(var(--color-primary-rgb), 0.2) !important; }

        .text-\[\#1a237e\] { color: var(--color-primary) !important; }
        .text-\[\#1a237e\]\/50 { color: rgba(var(--color-primary-rgb), 0.5) !important; }
        .text-\[\#1a237e\]\/70 { color: rgba(var(--color-primary-rgb), 0.7) !important; }
        .hover\:text-\[\#1a237e\]:hover { color: var(--color-primary) !important; }
        .hover\:text-\[\#283593\]:hover { color: rgba(var(--color-primary-rgb), 0.7) !important; }
        .group:hover .group-hover\:text-\[\#1a237e\] { color: var(--color-primary) !important; }

        .border-\[\#1a237e\] { border-color: var(--color-primary) !important; }
        .border-\[\#1a237e\]\/15 { border-color: rgba(var(--color-primary-rgb), 0.15) !important; }
        .border-\[\#1a237e\]\/20 { border-color: rgba(var(--color-primary-rgb), 0.2) !important; }
        .border-\[\#1a237e\]\/30 { border-color: rgba(var(--color-primary-rgb), 0.3) !important; }
        .hover\:border-\[\#1a237e\]:hover { border-color: var(--color-primary) !important; }
        .hover\:border-\[\#1a237e\]\/30:hover { border-color: rgba(var(--color-primary-rgb), 0.3) !important; }
        .hover\:border-\[\#1a237e\]\/40:hover { border-color: rgba(var(--color-primary-rgb), 0.4) !important; }

        .ring-\[\#1a237e\] { --tw-ring-color: rgba(var(--color-primary-rgb), 1) !important; }
        .ring-\[\#1a237e\]\/20 { --tw-ring-color: rgba(var(--color-primary-rgb), 0.2) !important; }
        .focus\:ring-\[\#1a237e\]:focus,
        .focus\:ring-\[\#1a237e\/20\]:focus,
        .focus\:ring-\[\#1a237e\/30\]:focus {
            --tw-ring-color: rgba(var(--color-primary-rgb), var(--tw-ring-opacity, 1)) !important;
        }
        .focus\:border-\[\#1a237e\]:focus,
        .focus\:border-\[\#1a237e\/50\]:focus {
            border-color: var(--color-primary) !important;
        }

        .accent-\[\#1a237e\] { accent-color: var(--color-primary) !important; }

        .shadow-\[\#1a237e\]\/5 { --tw-shadow-color: rgba(var(--color-primary-rgb), 0.05) !important; }
        .shadow-\[\#1a237e\]\/20 { --tw-shadow-color: rgba(var(--color-primary-rgb), 0.2) !important; }
        .shadow-\[\#1a237e\]\/25 { --tw-shadow-color: rgba(var(--color-primary-rgb), 0.25) !important; }

        /* ── Sidebar icon safety ── */
        aside nav a i.text-\[\#1a237e\],
        aside nav a svg.text-\[\#1a237e\] {
            color: var(--color-primary) !important;
        }

        /* ── Button Styles ── */
        /* 3D Button Style */
        html[data-btn-style='3d'] button.bg-\[\#1a237e\],
        html[data-btn-style='3d'] button[type='submit'],
        html[data-btn-style='3d'] .btn-primary,
        html[data-btn-style='3d'] [class*="bg-[#1a237e]"],
        html[data-btn-style='3d'] .btn-primary-dynamic {
            border-bottom: none !important;
            box-shadow: 0 4px 0 rgba(0,0,0,0.3) !important;
            transform: translateY(0);
            transition: transform 0.1s, box-shadow 0.1s;
        }
        html[data-btn-style='3d'] button.bg-\[\#1a237e\]:active,
        html[data-btn-style='3d'] button[type='submit']:active,
        html[data-btn-style='3d'] .btn-primary:active,
        html[data-btn-style='3d'] [class*="bg-[#1a237e]"]:active,
        html[data-btn-style='3d'] .btn-primary-dynamic:active {
            box-shadow: 0 1px 0 rgba(0,0,0,0.3) !important;
            transform: translateY(3px) !important;
        }

        /* Outline Button Style */
        html[data-btn-style='outline'] button.bg-\[\#1a237e\],
        html[data-btn-style='outline'] button[type='submit'],
        html[data-btn-style='outline'] .btn-primary,
        html[data-btn-style='outline'] [class*="bg-[#1a237e]"],
        html[data-btn-style='outline'] .btn-primary-dynamic {
            background-color: transparent !important;
            background-image: none !important;
            border: 2px solid var(--color-primary) !important;
            color: var(--color-primary) !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }
        html[data-btn-style='outline'] button.bg-\[\#1a237e\]:hover,
        html[data-btn-style='outline'] button[type='submit']:hover,
        html[data-btn-style='outline'] .btn-primary:hover,
        html[data-btn-style='outline'] [class*="bg-[#1a237e]"]:hover,
        html[data-btn-style='outline'] .btn-primary-dynamic:hover {
            background-color: var(--color-primary) !important;
            color: white !important;
        }

        /* ── Component Rounding (Rectangular to Capsule) ── */
        .rounded-xl,
        .rounded-lg,
        .rounded-md,
        .rounded-sm,
        .rounded,
        .input,
        .select,
        .textarea,
        .btn,
        button {
            border-radius: var(--radius-base) !important;
        }

        /* Large Container Capped Rounding */
        .rounded-2xl,
        .rounded-3xl {
            border-radius: min(var(--radius-base), 24px) !important;
        }

        /* ── Layout Density Overrides ── */
        html[data-density='compact'] .p-5,
        html[data-density='compact'] .p-6,
        html[data-density='compact'] .p-7,
        html[data-density='compact'] .p-8 { padding: 0.85rem !important; }
        html[data-density='compact'] th,
        html[data-density='compact'] td { padding: 0.35rem 0.5rem !important; }
        html[data-density='compact'] .py-3 { padding-top: 0.4rem !important; padding-bottom: 0.4rem !important; }
        html[data-density='compact'] .space-y-6 > * + * { margin-top: 0.75rem !important; }

        html[data-density='spacious'] .p-5,
        html[data-density='spacious'] .p-6,
        html[data-density='spacious'] .p-7,
        html[data-density='spacious'] .p-8 { padding: 2.25rem !important; }
        html[data-density='spacious'] th,
        html[data-density='spacious'] td { padding: 1.25rem 1.5rem !important; }
        html[data-density='spacious'] .py-3 { padding-top: 1.1rem !important; padding-bottom: 1.1rem !important; }
        html[data-density='spacious'] .space-y-6 > * + * { margin-top: 2rem !important; }

        
        /* ── Entrance Animation ── */
        @keyframes slideUpEntrance {
            0%   { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .animate-entrance {
            animation: slideUpEntrance 0.5s ease-out forwards;
        }

        /* ── Modal Animations ── */
        .modal-wrapper {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.25s, opacity 0.25s;
        }
        .modal-wrapper.active {
            visibility: visible;
            opacity: 1;
        }
        .modal-wrapper .modal-backdrop {
            opacity: 0;
            transition: opacity 0.25s ease-out;
        }
        .modal-wrapper.active .modal-backdrop {
            opacity: 1;
        }
        .modal-wrapper .modal-card {
            transform: scale(0.92) translateY(15px);
            opacity: 0;
            transition: transform 0.25s ease-out, opacity 0.25s ease-out;
        }
        .modal-wrapper.active .modal-card {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* ── Dark Mode Theme Styles ── */
        body.theme-dark.internal-body {
            background: linear-gradient(135deg, #0e111a 0%, #121422 50%, #0e121d 100%) !important;
            color: #cbd5e1 !important;
        }

        /* Swap to dark SVG background */
        body.theme-dark .bg-svg { display: none; }
        body.theme-dark .bg-svg-dark { display: block; }
        body.theme-dark input,
        body.theme-dark textarea,
        body.theme-dark select {
            background-color: rgba(13, 17, 28, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
        }
        body.theme-dark input::placeholder,
        body.theme-dark textarea::placeholder {
            color: #475569 !important;
        }
        body.theme-dark h1,
        body.theme-dark h2,
        body.theme-dark h3,
        body.theme-dark h4,
        body.theme-dark h5,
        body.theme-dark h6,
        body.theme-dark label,
        body.theme-dark p,
        body.theme-dark span,
        body.theme-dark th,
        body.theme-dark td,
        body.theme-dark tr,
        body.theme-dark select option {
            color: #cbd5e1 !important;
        }
        body.theme-dark .text-gray-900,
        body.theme-dark .text-gray-800,
        body.theme-dark .text-gray-700,
        body.theme-dark .text-gray-600,
        body.theme-dark .text-gray-500 {
            color: #cbd5e1 !important;
        }
        body.theme-dark .text-gray-400 {
            color: #94a3b8 !important;
        }
        body.theme-dark .text-\[\#1a237e\] {
            color: var(--color-primary) !important;
        }
        body.theme-dark .bg-gray-25,
        body.theme-dark .bg-gray-50,
        body.theme-dark .bg-gray-50\/50,
        body.theme-dark .bg-gray-100,
        body.theme-dark .bg-gray-200 {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #cbd5e1 !important;
        }
        body.theme-dark main table thead tr {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        body.theme-dark main tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        body.theme-dark .border-gray-100,
        body.theme-dark .border-gray-200,
        body.theme-dark .border-gray-300 {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        body.theme-dark .divide-gray-50 > * + * {
            border-color: rgba(255, 255, 255, 0.06) !important;
        }

        /* ── Adjust light pastel backgrounds & colors in dark mode ── */
        body.theme-dark .bg-purple-50 {
            background-color: rgba(168, 85, 247, 0.15) !important;
        }
        body.theme-dark .bg-blue-50 {
            background-color: rgba(59, 130, 246, 0.15) !important;
        }
        body.theme-dark .bg-orange-50 {
            background-color: rgba(249, 115, 22, 0.15) !important;
        }
        body.theme-dark .bg-green-50,
        body.theme-dark .bg-emerald-50 {
            background-color: rgba(16, 185, 129, 0.15) !important;
        }
        body.theme-dark .bg-red-50 {
            background-color: rgba(239, 68, 68, 0.15) !important;
        }
        body.theme-dark .text-purple-600 {
            color: #c084fc !important;
        }
        body.theme-dark .text-blue-600 {
            color: #60a5fa !important;
        }
        body.theme-dark .text-orange-600 {
            color: #fb923c !important;
        }
        body.theme-dark .text-green-600,
        body.theme-dark .text-emerald-500,
        body.theme-dark .text-emerald-600 {
            color: #34d399 !important;
        }
        body.theme-dark .text-red-500,
        body.theme-dark .text-red-600 {
            color: #f87171 !important;
        }

        /* ── Preserve theme preview boxes in dark mode ── */
        body.theme-dark .theme-preview-box.bg-white {
            background-color: #ffffff !important;
        }
        body.theme-dark .theme-preview-box.bg-gray-900 {
            background-color: #111827 !important;
        }

        /* ── Card & Container Dark Mode ── */
        body.theme-dark .bg-white {
            background-color: #1a1d2e !important;
        }
        body.theme-dark .bg-white\/60,
        body.theme-dark .bg-white\/70,
        body.theme-dark .bg-white\/80 {
            background-color: rgba(26, 29, 46, 0.9) !important;
        }

        /* ── Sidebar Dark Mode ── */
        body.theme-dark aside a:not([class*="bg-\[#1a237e\]"]):hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: #cbd5e1 !important;
        }

        /* ── Hover State Dark Mode ── */
        body.theme-dark .hover\:bg-gray-50:hover,
        body.theme-dark .hover\:bg-gray-100:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
        }

        /* ── Colored Background Mappings ── */
        body.theme-dark .bg-blue-50 { background-color: rgba(59, 130, 246, 0.12) !important; }
        body.theme-dark .bg-purple-50 { background-color: rgba(168, 85, 247, 0.12) !important; }
        body.theme-dark .bg-green-50 { background-color: rgba(34, 197, 94, 0.12) !important; }
        body.theme-dark .bg-orange-50 { background-color: rgba(249, 115, 22, 0.12) !important; }
        body.theme-dark .bg-red-50 { background-color: rgba(239, 68, 68, 0.12) !important; }
        body.theme-dark .bg-yellow-50 { background-color: rgba(234, 179, 8, 0.12) !important; }
        body.theme-dark .bg-pink-50 { background-color: rgba(236, 72, 153, 0.12) !important; }
        body.theme-dark .bg-teal-50 { background-color: rgba(20, 184, 166, 0.12) !important; }
        body.theme-dark .bg-indigo-50 { background-color: rgba(99, 102, 241, 0.12) !important; }
        body.theme-dark .bg-cyan-50 { background-color: rgba(6, 182, 212, 0.12) !important; }

        /* ── Notification Specific Dark Mode ── */
        body.theme-dark .bg-blue-50\/50 {
            background-color: rgba(59, 130, 246, 0.08) !important;
        }
        body.theme-dark .bg-gray-50\/50 {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        /* ── Panduan Pengguna Dark Mode ── */
        .panduan-header-bg {
            background: linear-gradient(135deg, rgba(26, 35, 126, 0.06) 0%, rgba(227, 242, 253, 0.8) 100%);
        }
        body.theme-dark .panduan-header-bg {
            background: linear-gradient(135deg, rgba(26, 35, 126, 0.35) 0%, rgba(2, 119, 189, 0.15) 100%) !important;
        }
        .panduan-section-card {
            background-color: rgba(249, 250, 251, 0.7);
        }
        body.theme-dark .panduan-section-card {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        body.theme-dark .bg-gray-50\/70 {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        /* ── Shadow Dark Mode ── */
        body.theme-dark .shadow-sm,
        body.theme-dark .shadow {
            box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important;
        }
        body.theme-dark .shadow-md {
            box-shadow: 0 4px 6px rgba(0,0,0,0.35) !important;
        }
        body.theme-dark .shadow-lg {
            box-shadow: 0 8px 15px rgba(0,0,0,0.4) !important;
        }
        body.theme-dark .shadow-xl {
            box-shadow: 0 12px 25px rgba(0,0,0,0.45) !important;
        }
        body.theme-dark .shadow-2xl {
            box-shadow: 0 20px 40px rgba(0,0,0,0.5) !important;
        }

        /* ── Light mode readability: darken overly light text ── */
        body.internal-body .text-gray-300 {
            color: #9ca3af !important;
        }

        /* ── Dark mode: text, icons, badges & borders ── */
        body.theme-dark .text-red-500,
        body.theme-dark .text-red-600,
        body.theme-dark .text-red-700,
        body.theme-dark .text-red-800,
        body.theme-dark .text-red-900 {
            color: #fca5a5 !important;
        }
        body.theme-dark .text-blue-500,
        body.theme-dark .text-blue-600,
        body.theme-dark .text-blue-700,
        body.theme-dark .text-blue-900 {
            color: #93c5fd !important;
        }
        body.theme-dark .text-green-400,
        body.theme-dark .text-green-500,
        body.theme-dark .text-green-600,
        body.theme-dark .text-green-700 {
            color: #86efac !important;
        }
        body.theme-dark .text-yellow-500,
        body.theme-dark .text-yellow-600,
        body.theme-dark .text-yellow-700 {
            color: #fde68a !important;
        }
        body.theme-dark .text-orange-500,
        body.theme-dark .text-orange-600,
        body.theme-dark .text-orange-700 {
            color: #fdba74 !important;
        }
        body.theme-dark .text-amber-600,
        body.theme-dark .text-amber-700,
        body.theme-dark .text-amber-800 {
            color: #fcd34d !important;
        }
        body.theme-dark .text-purple-500,
        body.theme-dark .text-purple-600,
        body.theme-dark .text-purple-700 {
            color: #d8b4fe !important;
        }
        body.theme-dark .text-pink-500 {
            color: #f9a8d4 !important;
        }
        body.theme-dark .text-teal-600 {
            color: #5eead4 !important;
        }
        body.theme-dark .text-cyan-600 {
            color: #67e8f9 !important;
        }
        body.theme-dark .text-indigo-500,
        body.theme-dark .text-indigo-600 {
            color: #a5b4fc !important;
        }
        body.theme-dark .text-emerald-500,
        body.theme-dark .text-emerald-600,
        body.theme-dark .text-emerald-700,
        body.theme-dark .text-emerald-800 {
            color: #6ee7b7 !important;
        }

        /* ── Dark mode: badge backgrounds ── */
        body.theme-dark .bg-red-100 { background-color: rgba(239, 68, 68, 0.15) !important; }
        body.theme-dark .bg-yellow-100 { background-color: rgba(234, 179, 8, 0.15) !important; }
        body.theme-dark .bg-orange-100 { background-color: rgba(249, 115, 22, 0.15) !important; }
        body.theme-dark .bg-blue-100 { background-color: rgba(59, 130, 246, 0.15) !important; }
        body.theme-dark .bg-green-100 { background-color: rgba(34, 197, 94, 0.15) !important; }
        body.theme-dark .bg-purple-100 { background-color: rgba(168, 85, 247, 0.15) !important; }
        body.theme-dark .bg-amber-100 { background-color: rgba(245, 158, 11, 0.15) !important; }
        body.theme-dark .bg-emerald-100 { background-color: rgba(16, 185, 129, 0.15) !important; }

        /* ── Dark mode: colored borders ── */
        body.theme-dark .border-red-100,
        body.theme-dark .border-red-200 { border-color: rgba(239, 68, 68, 0.3) !important; }
        body.theme-dark .border-yellow-200 { border-color: rgba(234, 179, 8, 0.3) !important; }
        body.theme-dark .border-orange-200,
        body.theme-dark .border-orange-200\/60 { border-color: rgba(249, 115, 22, 0.3) !important; }
        body.theme-dark .border-blue-100,
        body.theme-dark .border-blue-200 { border-color: rgba(59, 130, 246, 0.3) !important; }
        body.theme-dark .border-green-200 { border-color: rgba(34, 197, 94, 0.3) !important; }
        body.theme-dark .border-purple-100,
        body.theme-dark .border-purple-200 { border-color: rgba(168, 85, 247, 0.3) !important; }
        body.theme-dark .border-amber-200,
        body.theme-dark .border-amber-200\/60 { border-color: rgba(245, 158, 11, 0.3) !important; }
        body.theme-dark .border-emerald-200 { border-color: rgba(16, 185, 129, 0.3) !important; }
        body.theme-dark .border-indigo-200\/60 { border-color: rgba(99, 102, 241, 0.3) !important; }
        body.theme-dark .hover\:border-red-300\/50:hover,
        body.theme-dark .hover\:border-red-300:hover,
        body.theme-dark .hover\:border-red-200:hover { border-color: rgba(239, 68, 68, 0.5) !important; }
        body.theme-dark .hover\:border-blue-300\/50:hover,
        body.theme-dark .hover\:border-blue-200:hover { border-color: rgba(59, 130, 246, 0.5) !important; }
        body.theme-dark .hover\:border-green-300\/50:hover,
        body.theme-dark .hover\:border-green-200:hover { border-color: rgba(34, 197, 94, 0.5) !important; }
        body.theme-dark .hover\:border-orange-300\/50:hover { border-color: rgba(249, 115, 22, 0.5) !important; }
        body.theme-dark .hover\:border-purple-300\/50:hover { border-color: rgba(168, 85, 247, 0.5) !important; }
        body.theme-dark .hover\:border-emerald-200:hover { border-color: rgba(16, 185, 129, 0.5) !important; }
        body.theme-dark .hover\:border-amber-200:hover { border-color: rgba(245, 158, 11, 0.5) !important; }

        /* ── Dark mode: profile dropdown & notification panel ── */
        body.theme-dark header .bg-white {
            background-color: #1a1d2e !important;
        }
        body.theme-dark header .border-gray-200 {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        body.theme-dark header .hover\:bg-gray-50:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
        }
        body.theme-dark header .text-gray-700 {
            color: #cbd5e1 !important;
        }

        /* ── Dark mode: gray-300 override (was darkened in light mode) ── */
        body.theme-dark .text-gray-300 {
            color: #94a3b8 !important;
        }

        /* ── Dark mode flash prevention ── */
        html.theme-dark-pending body.internal-body {
            background: linear-gradient(135deg, #0e111a 0%, #121422 50%, #0e121d 100%) !important;
        }
        /* ── Notification dropdown mobile fix ── */
        @media (max-width: 640px) {
            .notif-dropdown-panel {
                position: fixed !important;
                top: 60px !important;
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%) !important;
                width: calc(100vw - 1.5rem) !important;
                max-width: 360px !important;
            }
        }
    </style>

    {{-- Apply saved appearance BEFORE first paint to avoid flash --}}
    <script>
    (function(){
        try {
            var s = localStorage.getItem('novos_appearance');
            var a = s ? JSON.parse(s) : {};
            var root = document.documentElement;
            if (a.primary) {
                root.style.setProperty('--color-primary', a.primary);
                var hex = a.primary.replace('#', '');
                if (hex.length === 3) {
                    hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                }
                var r = parseInt(hex.substring(0,2), 16);
                var g = parseInt(hex.substring(2,4), 16);
                var b = parseInt(hex.substring(4,6), 16);
                if (!isNaN(r) && !isNaN(g) && !isNaN(b)) {
                    root.style.setProperty('--color-primary-rgb', r + ',' + g + ',' + b);
                }
            }
            if (a.secondary) root.style.setProperty('--color-secondary', a.secondary);
            var fsMap = {sm:'13px', md:'15px', lg:'17px', xl:'19px'};
            if (a.fontSize)  root.style.setProperty('--font-size-base', fsMap[a.fontSize]||'15px');
            var rrMap = {none:'0px', sm:'6px', xl:'12px', full:'9999px'};
            if (a.rounded)   root.style.setProperty('--radius-base', rrMap[a.rounded]||'12px');
            if (a.buttonStyle) root.setAttribute('data-btn-style', a.buttonStyle);
            if (a.density) root.setAttribute('data-density', a.density);
            var fontMap = { poppins: "'Poppins'", inter: "'Inter'", outfit: "'Outfit'" };
            if (a.fontFamily) root.style.setProperty('--font-family-base', fontMap[a.fontFamily] || "'Poppins'");
            var dark = a.theme==='dark' || (a.theme==='auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (dark) document.documentElement.classList.add('theme-dark-pending');
            
            // Set defaults or saved colors
            var primary = a.primary;
            var secondary = a.secondary;
            
            if (dark) {
                if (!primary || primary === '#1a237e') {
                    primary = '#0277bd';
                }
                if (!secondary || secondary === '#3949ab') {
                    secondary = '#0288d1';
                }
            } else {
                if (!primary) primary = '#1a237e';
                if (!secondary) secondary = '#3949ab';
            }
            
            root.style.setProperty('--color-primary', primary);
            var hex = primary.replace('#', '');
            if (hex.length === 3) {
                hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
            }
            var r = parseInt(hex.substring(0,2), 16);
            var g = parseInt(hex.substring(2,4), 16);
            var b = parseInt(hex.substring(4,6), 16);
            if (!isNaN(r) && !isNaN(g) && !isNaN(b)) {
                root.style.setProperty('--color-primary-rgb', r + ',' + g + ',' + b);
            }
            
            root.style.setProperty('--color-secondary', secondary);
            
            var fsMap = {sm:'13px', md:'15px', lg:'17px', xl:'19px'};
            var fontSize = a.fontSize || 'md';
            root.style.setProperty('--font-size-base', fsMap[fontSize] || '15px');
            
            var rrMap = {none:'0px', sm:'6px', xl:'12px', full:'9999px'};
            var rounded = a.rounded || 'xl';
            root.style.setProperty('--radius-base', rrMap[rounded] || '12px');
            
            var buttonStyle = a.buttonStyle || 'flat';
            root.setAttribute('data-btn-style', buttonStyle);
            
            var density = a.density || 'comfortable';
            root.setAttribute('data-density', density);
            
            var fontMap = { poppins: "'Poppins'", inter: "'Inter'", outfit: "'Outfit'" };
            var fontFamily = a.fontFamily || 'poppins';
            root.style.setProperty('--font-family-base', fontMap[fontFamily] || "'Poppins'");
            
            var transition = a.transition || 'fade';
            root.setAttribute('data-transition', transition);
        } catch(e){}
    })();
    </script>
    {{-- Migrate old cookie name (sidebar.open) → sidebar_open to fix PHP dot-conversion bug --}}
    <script>
        (function () {
            var oldVal = document.cookie.match(/(?:^|;\s*)sidebar\.open=([^;]*)/);
            if (oldVal) {
                var val = oldVal[1];
                // Delete the old cookie
                document.cookie = 'sidebar.open=; path=/; max-age=0';
                // Write new cookie only if sidebar_open not already set
                if (!document.cookie.match(/(?:^|;\s*)sidebar_open=/)) {
                    document.cookie = 'sidebar_open=' + val + '; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 365);
                }
            }
        })();
    </script>
</head>
<body class="internal-body text-[#212121] antialiased flex h-screen overflow-hidden" x-data>
<script>
(function(){
    if (document.documentElement.classList.contains('theme-dark-pending')) {
        document.body.classList.add('theme-dark');
        document.documentElement.classList.remove('theme-dark-pending');
    }
})();
</script>

    {{-- Background decorative SVG — light & dark variants --}}
    <img class="bg-svg" src="{{ asset('images/bg-internal.svg') }}" alt="" aria-hidden="true">
    <img class="bg-svg bg-svg-dark" src="{{ asset('images/bg-internal-dark.svg') }}" alt="" aria-hidden="true">

    <!-- Sidebar -->
    @include('components.sidebar')

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Topbar -->
        <header class="py-4 px-8 flex items-center justify-between shrink-0 relative z-30">
            <div class="flex items-center gap-3">
                <button @click="$dispatch('sidebar-toggle')" class="text-gray-500 hover:text-[#1a237e]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 xl:hidden">
                        <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                    </svg>
                    <i data-lucide="menu" class="w-6 h-6 hidden xl:inline"></i>
                </button>
                <div id="topbar-title">
                    @yield('topbar-left')
                </div>
            </div>

            <!-- Right Section: Chat, Notifikasi, Profil -->
                <div class="flex items-center gap-5">
                <!-- Chat & Notifikasi -->
                <div class="flex items-center gap-4">
                    <div x-data="staffChatBadge()" x-init="init()" class="relative hidden">
                        <div class="bg-white rounded-full shadow-sm">
                            <a href="{{ route('staf.chat') }}" class="relative p-2 text-gray-500 hover:text-[#1a237e] flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 xl:hidden">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                                <i data-lucide="message-circle" class="w-5 h-5 hidden xl:inline"></i>
                                <span x-show="unreadCount > 0" x-cloak
                                      x-text="unreadCount > 9 ? '9+' : unreadCount"
                                      class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-[#1a237e] rounded-full min-w-[18px] h-[18px]">
                                </span>
                            </a>
                        </div>
                    </div>
                    {{-- Notifikasi - Mobile (link) --}}
                    <div class="xl:hidden" x-data="notifBadge()" x-init="init()">
                        <div class="bg-white rounded-full shadow-sm">
                            <a href="{{ route('staf.notifikasi') }}" class="relative p-2 text-gray-500 hover:text-[#1a237e] transition-colors flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                                </svg>
                                <span x-show="unreadCount > 0" x-cloak
                                      x-text="unreadCount > 9 ? '9+' : unreadCount"
                                      class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-[#1a237e] rounded-full min-w-[18px] h-[18px]">
                                </span>
                            </a>
                        </div>
                    </div>

                    {{-- Notifikasi - Desktop (hover dropdown) --}}
                    <div class="hidden xl:block" x-data="notifDropdown()" x-init="init()">
                        <div @mouseenter="open = true" @mouseleave="open = false" @click.away="open = false" class="relative">
                            <div class="bg-white rounded-full shadow-sm">
                                <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-[#1a237e] transition-colors flex items-center justify-center">
                                    <i data-lucide="bell" class="w-5 h-5"></i>
                                    <span x-show="unreadCount > 0" x-cloak
                                          x-text="unreadCount > 9 ? '9+' : unreadCount"
                                          class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-[#1a237e] rounded-full min-w-[18px] h-[18px]">
                                    </span>
                                </button>
                            </div>

                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                 class="absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden">

                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-gray-900">Notifikasi</span>
                                        <span x-show="unreadCount > 0" x-text="unreadCount"
                                              class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold text-white bg-[#1a237e] rounded-full min-w-[18px]">
                                        </span>
                                    </div>
                                    <button @click="markAllRead()" class="text-xs font-medium text-[#1a237e] hover:underline">Tandai semua dibaca</button>
                                </div>

                                <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                    <template x-if="loading">
                                        <div class="px-4 py-8 text-center">
                                            <svg class="w-6 h-6 mx-auto text-gray-300 mb-2 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                            </svg>
                                            <p class="text-xs text-gray-400">Memuat notifikasi...</p>
                                        </div>
                                    </template>
                                    <template x-if="!loading">
                                        <div>
                                            <template x-for="notif in previewNotifs" :key="notif.id">
                                                <div @click="markRead(notif.id)"
                                                     :class="notif.read ? 'bg-white' : 'bg-blue-50/50'"
                                                     class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors">
                                                    <div class="relative shrink-0">
                                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                                             :style="'background:' + notif.color"
                                                             x-text="notif.initials">
                                                        </div>
                                                        <span x-show="!notif.read"
                                                              class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full">
                                                        </span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs text-gray-800 leading-relaxed" x-html="notif.message"></p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span class="text-[10px] text-gray-400" x-text="notif.time"></span>
                                                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full"
                                                                  :class="notif.badgeClass" x-text="notif.badge"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="!loading && previewNotifs.length === 0" class="px-4 py-8 text-center">
                                                <p class="text-xs text-gray-400">Tidak ada notifikasi baru</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="border-t border-gray-100 px-4 py-2.5 bg-gray-50/50">
                                    <a :href="'{{ route('staf.notifikasi') }}'" class="block text-center text-xs font-semibold text-[#1a237e] hover:underline">
                                        Lihat semua notifikasi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <div x-data="{ open: false, profileOpen: false }" class="relative ml-5">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover shrink-0">
                        @else
                            <div class="w-8 h-8 bg-[#1a237e] rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs">{{ collect(explode(' ', auth()->user()->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('') }}</span>
                            </div>
                        @endif
                        <span class="text-gray-700 font-medium text-sm hidden xl:inline">{{ auth()->user()->role->name ?? auth()->user()->name }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 hidden xl:inline"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg z-50">
                        <div class="py-1">
                            <button @click="profileOpen = true; open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil Saya</button>
                            <a href="{{ route('beranda') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Beranda</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                            </form>
                        </div>
                    </div>

                    {{-- Modal Profil Saya (read-only) --}}
                    <template x-teleport="body">
                        <div x-show="profileOpen" class="fixed inset-0 z-[100] flex items-center justify-center" x-cloak>
                            <div x-show="profileOpen" x-transition.opacity class="fixed inset-0 bg-black/40" @click="profileOpen = false"></div>
                            <div x-show="profileOpen" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 mx-4">
                                <div class="flex items-center justify-between mb-5">
                                    <h3 class="text-lg font-bold text-gray-900">Profil Saya</h3>
                                    <button @click="profileOpen = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>

                                <div class="flex flex-col items-center gap-3 mb-6">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-2 border-gray-100">
                                    @else
                                        <div class="w-20 h-20 bg-[#1a237e] rounded-full flex items-center justify-center border-2 border-gray-100">
                                            <span class="text-white font-bold text-xl">{{ collect(explode(' ', auth()->user()->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('') }}</span>
                                        </div>
                                    @endif
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#1a237e]/10 text-[#1a237e]">{{ auth()->user()->role->name ?? '-' }}</span>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-0.5">Nama</p>
                                        <p class="text-sm font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ auth()->user()->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-0.5">Email</p>
                                        <p class="text-sm font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>

                                <button @click="profileOpen = false"
                                    class="w-full mt-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 animate-entrance">
            @include('components.alert')
            @yield('internal-content')
        </main>
        
    </div>

    {{-- Micro-Break Reminder --}}
    @php
        $rawReminders = json_decode(App\Models\Setting::get('reminder_times', '[
            {"time":"10:00","message":"Saatnya Micro-Break! \u2014 Minum air putih, peregangan ringan, dan tarik napas 3 kali."},
            {"time":"13:00","message":"Istirahat Sejenak \u2014 Lakukan teknik STOP: Stop, Take a breath, Observe, Proceed."},
            {"time":"15:00","message":"Sudahkah Anda Beristirahat? \u2014 Berdiri, tarik napas, dan kembali bekerja dengan lebih segar."}
        ]'), true);
        if ($rawReminders && is_array($rawReminders) && isset($rawReminders[0]) && is_string($rawReminders[0])) {
            $rawReminders = array_map(fn($t) => ['time' => $t, 'message' => 'Waktunya Micro-Break!'], $rawReminders);
        }
    @endphp
    <div x-data="microBreakReminder({{ json_encode($rawReminders) }})" x-init="init()">
        <template x-teleport="body">
            <div x-show="activeReminder" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 translate-x-4"
                x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
                x-transition:leave-end="opacity-0 translate-y-4 translate-x-4"
                class="fixed bottom-6 right-6 z-[9999] w-80">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-2xl p-5 h-44 flex flex-col">
                    <p class="font-semibold text-gray-900" x-text="activeReminder?.title"></p>
                    <div class="text-gray-600 text-sm leading-relaxed flex-1 flex items-center" x-html="activeReminder?.body"></div>
                    <button @click="dismissReminder()"
                        class="w-full py-2 bg-[#1a237e]/5 hover:bg-[#1a237e]/10 text-[#1a237e] font-medium text-sm rounded-xl transition-colors">
                        Baik, Saya Akan Istirahat
                    </button>
                </div>
            </div>
        </template>
    </div>

    <script>
        function microBreakReminder(config = []) {
            const slots = (config || []).map((item, i) => {
                const [h, m] = item.time.split(':').map(Number);
                return {
                    key: String(i),
                    hour: h,
                    min: m,
                    time: item.time,
                    timeLabel: item.time,
                    title: 'Micro-Break! ☕',
                    body: item.message || 'Waktunya Micro-Break!'
                };
            });

            return {
                shownSlots: JSON.parse(localStorage.getItem('microbreak_shown') || '[]'),
                today: localStorage.getItem('microbreak_date') || '',
                activeReminder: null,
                reminders: slots,
                _timer: null,

                init() {
                    const d = new Date().toDateString();
                    if (this.today !== d) {
                        this.shownSlots = [];
                        this.today = d;
                        try {
                            localStorage.setItem('microbreak_shown', '[]');
                            localStorage.setItem('microbreak_date', d);
                        } catch (e) {}
                    }
                    this._checkTime();
                    this._catchUp();
                    this._timer = setInterval(() => this._checkTime(), 15000);
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) { this._checkTime(); this._catchUp(); }
                    });
                },

                _catchUp() {
                    const now = new Date();
                    const currentMinutes = now.getHours() * 60 + now.getMinutes();
                    for (const r of this.reminders) {
                        const target = r.hour * 60 + r.min;
                        if (target > currentMinutes || currentMinutes - target > 30) continue;
                        if (!this.shownSlots.includes(r.key)) {
                            this.shownSlots.push(r.key);
                            this.activeReminder = r;
                            try {
                                localStorage.setItem('microbreak_shown', JSON.stringify(this.shownSlots));
                            } catch (e) {}
                        }
                    }
                },

                _checkTime() {
                    const now = new Date();
                    const currentMinutes = now.getHours() * 60 + now.getMinutes();
                    for (const r of this.reminders) {
                        const target = r.hour * 60 + r.min;
                        if (Math.abs(currentMinutes - target) <= 1 && !this.shownSlots.includes(r.key)) {
                            this.activeReminder = r;
                            this.shownSlots.push(r.key);
                            try {
                                localStorage.setItem('microbreak_shown', JSON.stringify(this.shownSlots));
                            } catch (e) {}
                            break;
                        }
                    }
                },

                dismissReminder() {
                    this.activeReminder = null;
                }
            }
        }
    </script>

    @include('notify::components.notify')

    <script>
    window.Notify = {
        _icons: {
            success: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 shrink-0 text-green-500"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
            error: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 shrink-0 text-red-500"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            warning: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 shrink-0 text-yellow-500"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 shrink-0 text-blue-500"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
            close: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
        },
        _borders: {
            success: 'border-green-500', error: 'border-red-500',
            warning: 'border-yellow-500', info: 'border-blue-500'
        },
        _show(type, message, title, duration) {
            let container = document.getElementById('notify-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notify-container';
                container.className = 'fixed top-20 right-4 z-[9999] flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }
            let el = document.createElement('div');
            el.className = 'pointer-events-auto notify-item border-l-4 ' + (this._borders[type] || 'border-blue-500') + ' bg-white rounded-lg shadow-lg p-4 transition-all duration-300 translate-x-4 opacity-0 max-w-sm';
            el.innerHTML = '<div class="flex items-start gap-3">' + (this._icons[type] || this._icons.info) + '<div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900">' + title + '</p><p class="text-sm text-gray-600 mt-1">' + message + '</p></div><button onclick="this.closest(\'.notify-item\').remove()" class="text-gray-400 hover:text-gray-600 shrink-0">' + this._icons.close + '</button></div>';
            container.appendChild(el);
            requestAnimationFrame(function() { el.classList.remove('translate-x-4', 'opacity-0'); });
            setTimeout(function() {
                el.classList.add('translate-x-4', 'opacity-0');
                setTimeout(function() { el.remove(); }, 300);
            }, duration);
        },
        success: function(message, title) { this._show('success', message, title || 'Berhasil', 3000); },
        error: function(message, title) { this._show('error', message, title || 'Gagal', 5000); },
        warning: function(message, title) { this._show('warning', message, title || 'Peringatan', 4000); },
        info: function(message, title) { this._show('info', message, title || 'Informasi', 4000); }
    };
    </script>

    {{-- Tutorial Overlay --}}
    <div x-data="pageTutorial()" x-init="init()">
        <template x-teleport="body">
            <div x-show="active" x-cloak class="fixed inset-0 z-[99999]">
                <div class="absolute inset-0 bg-black/60" @click="skip()"></div>
                <div class="relative z-10 h-full flex items-center justify-center">
                    <template x-if="currentStep">
                        <div class="max-w-lg w-full mx-4">
                            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                                <div class="px-6 pt-6 pb-4">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-bold shrink-0"
                                            :style="'background:'+currentStep.color">
                                            <i :data-lucide="currentStep.icon" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900" x-text="currentStep.label"></h3>
                                            <p class="text-xs text-gray-400" x-text="'Langkah ' + (stepIndex + 1) + ' dari ' + steps.length"></p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed" x-text="currentStep.tip"></p>
                                </div>
                                <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t border-gray-100">
                                    <button @click="skip()"
                                        class="text-xs font-semibold text-gray-500 hover:text-gray-700">
                                        Lewati Tutorial
                                    </button>
                                    <div class="flex items-center gap-2">
                                        <button @click="prev()" x-show="stepIndex > 0"
                                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-white rounded-xl transition-colors">
                                            Sebelumnya
                                        </button>
                                        <button @click="next()"
                                            class="px-5 py-2 text-xs font-bold text-white rounded-xl transition-all active:scale-95 shadow-md"
                                            :style="'background:'+currentStep.color">
                                            <span x-text="stepIndex === steps.length - 1 ? 'Selesai' : 'Selanjutnya'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <script>
    function pageTutorial() {
        return {
            active: false,
            stepIndex: 0,
            steps: [
                { target: 'a[href*="dashboard"]', label: 'Dashboard', icon: 'layout-dashboard', color: '#1a237e', tip: 'Dashboard adalah halaman utama yang menampilkan ringkasan statistik bisnis seperti jumlah pesanan baru, pendapatan hari ini, dan grafik tren. Cocok untuk memantau kondisi toko secara cepat.' },
                { target: 'a[href*="summary"]', label: 'Summary', icon: 'pie-chart', color: '#0288d1', tip: 'Summary menyajikan analisis data lebih mendalam dengan diagram lingkaran, tren pendapatan, dan performa tim. Gunakan filter periode untuk melihat data spesifik.' },
                { target: 'a[href*="daftar-pesanan"]', label: 'Daftar Pesanan', icon: 'shopping-bag', color: '#e65100', tip: 'Kelola semua pesanan pelanggan di sini. Anda bisa melihat daftar, mencari, memfilter berdasarkan status, dan mengklik nomor pesanan untuk melihat detail lengkap.' },
                { target: 'a[href*="design"]', label: 'Design', icon: 'pen-tool', color: '#2e7d32', tip: 'Halaman untuk tim Design: upload hasil desain jersey, update status pengerjaan, dan berkomunikasi dengan tim terkait revisi desain.' },
                { target: 'a[href*="produksi"]', label: 'Produksi', icon: 'scissors', color: '#bf360c', tip: 'Halaman untuk tim Produksi: kelola proses produksi jersey, update progress, dan tambahkan catatan produksi.' },
                { target: 'a[href*="daily-mental-check"]', label: 'Daily Mental Check', icon: 'heart', color: '#e91e63', tip: 'Isi cek kesehatan mental harian di sini. Pilih mood, energi, dan tingkat stres Anda. Data bersifat rahasia.' },
                { target: 'a[href*="laporan"]', label: 'Laporan', icon: 'file-text', color: '#37474f', tip: 'Buat dan ekspor laporan bisnis dalam format CSV, Excel, atau PDF. Filter berdasarkan periode dan jenis laporan.' },
                { target: 'a[href*="kelola-produk"]', label: 'Kelola Produk', icon: 'package', color: '#00695c', tip: 'Atur katalog produk jersey: tambah produk baru, edit harga, upload foto, atur status featured.' },
                { target: 'a[href*="kategori"]', label: 'Kategori', icon: 'folder-tree', color: '#f57c00', tip: 'Kelola kategori produk untuk mengelompokkan jenis jersey. Kategori akan tampil di katalog publik.' },
                { target: 'a[href*="kelola-pengguna"]', label: 'Kelola Pengguna', icon: 'users', color: '#4a148c', tip: 'Manajemen akun staf internal: tambah pengguna baru, atur role, edit atau nonaktifkan akun. Hanya untuk Super Admin.' },
                { target: 'a[href*="pengaturan"]', label: 'Pengaturan', icon: 'settings', color: '#607d8b', tip: 'Konfigurasi toko (nama, alamat, kontak) dan tampilan panel (tema, warna, font, glassmorphism, transisi). Lihat panduan lengkap di tab Panduan.' },
            ],
            get currentStep() {
                return this.steps[this.stepIndex] || null;
            },
            init() {
                var params = new URLSearchParams(window.location.search);
                if (params.get('tutorial') === '1') {
                    this.active = true;
                    this.$nextTick(() => { try { lucide.createIcons({ icons: window.lucide.icons }); } catch(e) {} });
                }
            },
            next() {
                if (this.stepIndex < this.steps.length - 1) {
                    this.stepIndex++;
                    this.$nextTick(() => { try { lucide.createIcons({ icons: window.lucide.icons }); } catch(e) {} });
                } else {
                    this.skip();
                }
            },
            prev() {
                if (this.stepIndex > 0) {
                    this.stepIndex--;
                    this.$nextTick(() => { try { lucide.createIcons({ icons: window.lucide.icons }); } catch(e) {} });
                }
            },
            skip() {
                this.active = false;
                this.stepIndex = 0;
                var url = new URL(window.location);
                url.searchParams.delete('tutorial');
                window.history.replaceState({}, '', url);
            }
        };
    }
    </script>
    @stack('scripts')
</body>

<script>
function staffChatBadge() {
    return {
        unreadCount: 0,
        init() {
            this.fetchUnread();
            setInterval(() => this.fetchUnread(), 60000);
        },
        async fetchUnread() {
            try {
                const res = await fetch('{{ route("staf.chat.unread-count") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.unreadCount = data.count || 0;
            } catch (e) {}
        }
    }
}

function notifBadge() {
    return {
        unreadCount: 0,
        init() {
            this.fetchCount();
            setInterval(() => this.fetchCount(), 60000);
        },
        async fetchCount() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.notifikasi.preview") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                });
                const data = await res.json();
                this.unreadCount = (data.notifications || []).filter(n => !n.read).length;
            } catch (e) {}
        }
    }
}

function notifDropdown() {
    return {
        open: false,
        loading: true,
        notifications: [],
        get unreadCount() {
            return this.notifications.filter(n => !n.read).length;
        },
        get previewNotifs() {
            return this.notifications.slice(0, 5);
        },
        init() {
            this.loadNotifications();
            setInterval(() => this.loadNotifications(), 60000);
        },
        async loadNotifications() {
            this.loading = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.notifikasi.preview") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                });
                const data = await res.json();
                this.notifications = data.notifications;
            } catch (e) {} finally {
                this.loading = false;
            }
        },
        async markRead(id) {
            const n = this.notifications.find(n => n.id === id);
            if (!n || n.read) return;
            n.read = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                await fetch('{{ url("staf/notifikasi") }}/' + id + '/read', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
            } catch (e) {}
        },
        async markAllRead() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.notifikasi.read-all") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.notifications.forEach(n => n.read = true);
                }
            } catch (e) {}
        }
    }
}
</script>

{{-- Global Lucide init for pages without explicit createIcons() call --}}
<script>
document.addEventListener('alpine:init', function () {
    if (window.Alpine && window.lucide && typeof window.lucide.createIcons === 'function') {
        window.Alpine.nextTick(function () {
            window.lucide.createIcons({ icons: window.lucide.icons });
        });
    }
});
</script>
</html>
@endif
