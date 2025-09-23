@extends('admin_master.layout')

@section('page-title', 'Program Details')

@php
$breadcrumbItems = [
    ['text' => 'Academic Management', 'url' => '#'],
    ['text' => 'Programs', 'url' => route('programs.index')],
    ['text' => 'Program Details']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}" page-title="Program Details"></breadcrumb>
        <program-show :program-id="{{ $program->id }}"></program-show>
    </div>
@endsection