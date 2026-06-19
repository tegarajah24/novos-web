@extends('layouts.internal')

@section('title', 'Notifikasi')

@section('topbar-left')
    <h1 class="text-xl font-bold text-black">Notifications</h1>
@endsection

@section('internal-content')
<div class="glass-card rounded-2xl overflow-hidden flex flex-col" style="height: calc(100vh - 10rem);">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-6">
            <button class="text-sm font-semibold text-gray-900 border-b-2 border-gray-900 pb-0.5">All</button>
            <button class="text-sm font-semibold text-gray-500 hover:text-gray-700 pb-0.5">Inbox <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold text-white bg-red-500 rounded-full min-w-[18px]">2</span></button>
            <button class="text-sm font-semibold text-gray-500 hover:text-gray-700 pb-0.5">Following</button>
            <button class="text-sm font-semibold text-gray-500 hover:text-gray-700 pb-0.5">Archived</button>
        </div>
        <button class="text-sm font-medium text-gray-500 hover:text-gray-700">Mark all as read</button>
    </div>

    <div class="flex-1 overflow-y-auto">
        <template x-for="notif in notifications" :key="notif.id">
            <div class="px-6 py-4 border-b border-gray-50 hover:bg-white/40 transition-colors cursor-pointer">
                <div class="flex items-start gap-4">
                    <div class="shrink-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-sm">
                            <span x-text="notif.initials"></span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm text-gray-900">
                                <span class="font-semibold" x-text="notif.sender"></span>
                                <span x-text="notif.action"></span>
                            </p>
                            <span class="text-xs text-gray-400 whitespace-nowrap" x-text="notif.time"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-show="notif.target" x-text="notif.target"></span>
                        </p>

                        <div x-show="notif.type === 'approval'" class="flex items-center gap-2 mt-3">
                            <button class="px-4 py-1.5 text-xs font-medium border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">Deny</button>
                            <button class="px-4 py-1.5 text-xs font-medium bg-[#1a237e] text-white rounded-lg hover:bg-[#1a237e]/90 transition-colors">Approve</button>
                        </div>

                        <div x-show="notif.type === 'attachment'" class="mt-3 inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white/60">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.583 6.583a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <span class="text-xs font-medium text-gray-700" x-text="notif.fileName"></span>
                            <span class="text-xs text-gray-400" x-text="notif.fileSize"></span>
                        </div>

                        <div x-show="notif.type === 'comment'" class="mt-3 p-3 rounded-lg border border-gray-100 bg-white/60">
                            <p class="text-xs text-gray-600 italic" x-text="notif.comment"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div x-show="notifications.length === 0" class="px-6 py-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
            <p class="text-sm text-gray-500 font-medium">No notifications</p>
            <p class="text-xs text-gray-400 mt-1">You're all caught up</p>
        </div>
    </div>

    <div class="px-6 py-3 border-t border-gray-100 flex items-center justify-between bg-white/40">
        <div class="flex items-center gap-2">
            <button class="text-xs text-gray-400 hover:text-gray-600 font-medium">Use</button>
            <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
            <button class="text-xs text-gray-400 hover:text-gray-600 font-medium">to navigate</button>
        </div>
        <button class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 font-medium">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Manage Notifications
        </button>
    </div>
</div>

<script>
function internalChatApp() {
    return {
        activeNotif: null,
        notifications: []
    }
}
</script>
@endsection
