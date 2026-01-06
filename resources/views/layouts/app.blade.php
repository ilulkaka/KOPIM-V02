@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle')
        | @yield('subtitle')
    @endif
@stop

{{-- Extend and customize the page content header --}}

{{-- @section('content_top_nav_right')
    <li class="nav-item dropdown">
        <a class="nav-link" href="{{ url('notif') }}">
            <i class="fas fa-bell"></i>
            @if (isset($notifCount) && $notifCount > 0)
                <span class="badge badge-danger navbar-badge">{{ $notifCount }}</span>
            @endif
        </a>
    </li>
@stop --}}



@section('content_header')
    @hasSection('content_header_title')
        <h2 class="text-muted" style="font-size: 1.2rem;">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark" style="font-size: 1rem;">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h2>
    @endif
@stop


{{-- Rename section content to content_body --}}

@section('content')
    @yield('content_body')
    <div id="spinner-overlay" class="spinner-overlay d-none">
        <div class="dots-loader">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="mt-3 text-light">Process...</div>
    </div>
@stop

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '2.0.0') }}
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <strong>Copyright © 2025-{{ date('Y') }} <a href="#">KOPIM.</a></strong>
@stop

@section('css')
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/assets/img/NPMI_Logo.png') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}
@stop
{{-- Add common Javascript/Jquery code --}}

@section('js')
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- @vite(['resources/js/app.js'])
    @stack('scripts') --}}
@stop
