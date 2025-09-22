@extends('admin_master.layout')

@section('page-title', 'Academic Terms')

@php
$breadcrumbItems = [
    ['text' => 'Academic Terms']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Academic Terms"></breadcrumb>
        <term-list :initial-terms="{{ json_encode($academicTerms) }}"></term-list>
    </div>
@endsection