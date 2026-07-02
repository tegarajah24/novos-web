@extends('layouts.internal')

@section('title', 'Laporan')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Laporan</h1>
@endsection

@section('internal-content')
    @livewire('laporan')
@endsection
