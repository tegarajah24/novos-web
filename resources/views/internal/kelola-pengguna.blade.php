@extends('layouts.internal')

@section('title', 'Kelola Pengguna')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kelola Pengguna</h1>
@endsection

@section('internal-content')
    @livewire('kelola-pengguna')
@endsection
