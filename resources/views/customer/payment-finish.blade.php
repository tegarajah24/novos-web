@extends('layouts.customer')
@section('title', 'Pembayaran Selesai — Novos')
@section('content')
    @livewire('payment-finish', ['orderNumber' => $orderNumber])
@endsection
