@extends('layouts.internal')

@section('title', 'Dashboard')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
@endsection

@section('internal-content')
    {{-- Stats cards and charts will remain below if they exist or are added later --}}
@endsection
