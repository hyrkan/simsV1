@extends('admin_master.layout')

@section('page-title', 'Curricula')

@php
$breadcrumbItems = [
    ['text' => 'Academic Management', 'url' => '#'],
    ['text' => 'Curricula']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Curricula"></breadcrumb>
        <curriculum-index></curriculum-index>
    </div>
@endsection