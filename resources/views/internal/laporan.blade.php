@extends('layouts.internal')

@section('topbar-left')
    <h1 class="text-2xl font-bold">Laporan</h1>
    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
@endsection

@section('internal-content')
    
@endsection
