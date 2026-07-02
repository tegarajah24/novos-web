@extends('layouts.internal')

@section('title', 'Daftar Pesanan')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Daftar Pesanan</h1>
@endsection

@section('internal-content')
    @livewire('daftar-pesanan')
@endsection
