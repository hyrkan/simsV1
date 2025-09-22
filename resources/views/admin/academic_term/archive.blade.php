@extends('admin_master.layout')

@section('page-title', 'Academic Terms Archive')

@php
$breadcrumbItems = [
    ['text' => 'Academic Terms', 'url' => '/admin/academic-terms'],
    ['text' => 'Archive']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Academic Terms Archive"></breadcrumb>
        <archive-term-list :initial-terms="{{ json_encode($archivedTerms) }}"></archive-term-list>
    </div>
@endsection