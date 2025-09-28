@extends('admin_master.layout')

@section('page-title', 'Create Exam')

@php
$breadcrumbItems = [
    ['text' => 'Exam Management', 'url' => '#'],
    ['text' => 'All Exams', 'url' => route('exams.index')],
    ['text' => 'Create Exam']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Create Exam"></breadcrumb>
        <exam-create :academic-terms="{{ json_encode($academicTerms) }}"></exam-create>
    </div>
@endsection