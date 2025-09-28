@extends('admin_master.layout')

@section('page-title', 'Edit Exam')

@php
$breadcrumbItems = [
    ['text' => 'Exam Management', 'url' => '#'],
    ['text' => 'All Exams', 'url' => route('exams.index')],
    ['text' => 'Edit Exam']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Edit Exam"></breadcrumb>
        <exam-edit 
            :exam="{{ json_encode($exam) }}" 
            :academic-terms="{{ json_encode($academicTerms) }}">
        </exam-edit>
    </div>
@endsection