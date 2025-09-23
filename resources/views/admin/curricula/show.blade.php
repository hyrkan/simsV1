@extends('admin_master.layout')

@section('page-title', 'Curriculum Details')

@php
$breadcrumbItems = [
    ['text' => 'Academic Management', 'url' => '#'],
    ['text' => 'Curricula', 'url' => route('curricula.index')],
    ['text' => 'Curriculum Details']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Curriculum Details"></breadcrumb>
        <curriculum-show></curriculum-show>
    </div>
@endsection