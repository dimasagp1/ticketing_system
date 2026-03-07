@extends('layouts.app')

@section('content')
    @if(auth()->user()->isClient())
        @include('dashboard.client')
    @elseif(auth()->user()->isDeveloper())
        @include('dashboard.developer')
    @elseif(auth()->user()->isAdmin())
        @include('dashboard.admin')
    @elseif(auth()->user()->isSuperAdmin())
        @include('dashboard.super-admin')
    @endif
@endsection
