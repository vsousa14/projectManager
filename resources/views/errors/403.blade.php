@extends('layouts.app')

@section('content')
@section('title', '403 Forbidden')
    <div class="container">
        <h1 class="text-danger">403 Forbidden</h1>
        <p>You do not have permission to access this page.</p>
        <a href="{{ url('/home') }}">Go back to home</a>
    </div>
    @endsection
