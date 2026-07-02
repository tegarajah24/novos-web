@extends('layouts.customer')
@section('title', 'Tracking Pesanan — Novos')
@section('content')
    @livewire('tracking', ['orderData' => $orderData, 'shared' => $shared ?? false])
@endsection
