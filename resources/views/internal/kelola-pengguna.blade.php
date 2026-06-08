@extends('layouts.internal')

@section('title', 'Kelola Pengguna')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kelola Pengguna</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
@endsection

@section('internal-content')
@endsection
