@extends('layouts.customer')
@section('title', 'Katalog — Novos')
@section('content')
    @livewire('katalog', ['products' => $products, 'categories' => $categories])
@endsection
