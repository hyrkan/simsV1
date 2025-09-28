@extends('admin_master.layout')

@section('page-title', 'Exams')

@php
$breadcrumbItems = [
    ['text' => 'Exam Management', 'url' => '#'],
    ['text' => 'All Exams']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Exams"></breadcrumb>
        <exam-index></exam-index>
    </div>
@endsection