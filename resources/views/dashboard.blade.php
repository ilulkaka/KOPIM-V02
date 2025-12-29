@extends('layouts.app')
{{-- @extends('adminlte::page') --}}

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')
    <p>Welcome</p>

    {{-- <script>
        @if (session()->has('kopim_token'))
            // Simpan token di localStorage
            localStorage.setItem('kopim_token', "{{ session('kopim_token') }}");
        @endif

        // Bisa dipanggil dari file JS
        var key = localStorage.getItem("kopim_token");
        console.log("Token:", key);
    </script> --}}
@endsection
