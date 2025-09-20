@extends('admin_master.layout')

@section('page-title', 'Roles and Permissions')

@php
$breadcrumbItems = [
    ['text' => 'Admin', 'url' => '/admin'],
    ['text' => 'Settings', 'url' => '#'],
    ['text' => 'Roles and Permissions']
];
@endphp

@section('content')
  <div id="app">
    <roles-and-permission></roles-and-permission>
  </div>
@endsection