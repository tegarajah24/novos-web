@extends('layouts.internal')

@section('title', 'Detail Pesanan')

@section('topbar-left')
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-[#1a237e]">{{ $order->order_number }}</h1>
        </div>
    </div>
@endsection

@section('internal-content')
    @livewire('detail-pesanan', ['orderNumber' => $order->order_number])
@endsection
