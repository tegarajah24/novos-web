@extends('layouts.internal')

@section('title', 'Kelola Kategori')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kategori</h1>
@endsection

@section('internal-content')
    @livewire('kelola-kategori')
@endsection
