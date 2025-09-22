@extends('admin_master.layout')

@section('page-title', 'Departments')

@php
$breadcrumbItems = [
    ['text' => 'Academic Management', 'url' => '#'],
    ['text' => 'Departments']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Departments"></breadcrumb>
        <department-index></department-index>
    </div>
@endsection