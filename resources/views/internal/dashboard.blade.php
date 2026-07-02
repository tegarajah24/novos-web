@extends('layouts.internal')

@section('title', 'Dashboard')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Dashboard</h1>
@endsection

@section('internal-content')
    @livewire('dashboard')
@endsection
