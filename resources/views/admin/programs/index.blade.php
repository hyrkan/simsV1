@extends('admin_master.layout')

@section('page-title', 'Programs')

@php
$breadcrumbItems = [
    ['text' => 'Academic Management', 'url' => '#'],
    ['text' => 'Programs']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Programs"></breadcrumb>
        <program-index></program-index>
    </div>
@endsection