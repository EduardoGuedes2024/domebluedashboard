@extends('layouts.app')

@section('title', 'Home - DomeBlue')

@section('content')

<div class="flex items-center justify-center min-h-screen">

    <div class="text-center">

        <img src="{{ asset('imagens/logodomeblueazul.png') }}"
             class="mx-auto h-24 mb-4">

        <h1 class="text-5xl font-black text-red-700">
            DomeBlue
        </h1>

        <p class="text-gray-500 mt-3">
            Sistema de Gestão
        </p>

    </div>

</div>

@endsection
