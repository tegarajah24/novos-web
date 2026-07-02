@extends('layouts.internal')
@section('title', 'Summary')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Summary</h1>
@endsection

@section('internal-content')
    @livewire('summary')
@endsection
