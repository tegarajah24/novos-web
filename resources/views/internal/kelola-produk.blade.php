@extends('layouts.internal')

@section('title', 'Kelola Produk')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kelola Produk</h1>
@endsection

@section('internal-content')
    @livewire('kelola-produk')
@endsection
