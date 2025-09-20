@extends('admin_master.layout')

@section('page-title', 'Create Staff')

@php
$breadcrumbItems = [
    ['text' => 'Admin', 'url' => '/admin'],
    ['text' => 'Staff Management', 'url' => '/admin/staff'],
    ['text' => 'Create Staff']
];
@endphp

@section('content')
    <div id="app">
        <breadcrumb :items="{{ json_encode($breadcrumbItems) }}"></breadcrumb>
        <create-staff></create-staff>
    </div>
@endsection

@section('scripts')
<script>

</script>
@endsection
