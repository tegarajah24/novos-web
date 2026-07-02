@extends('layouts.internal')

@section('title', 'Daily Mental Check')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Daily Mental Check</h1>
@endsection

@section('internal-content')
    @livewire('daily-mental-check')
@endsection
