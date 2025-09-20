@extends('admin_master.layout')

@section('page-title', 'Staff List')

@php
$breadcrumbItems = [
    ['text' => 'Admin', 'url' => '/admin'],
    ['text' => 'Staff Management', 'url' => '/admin/staff'],
    ['text' => 'Staff List']
];
@endphp

@section('content')
  <div id="app">
    <staff-table 
      :staffs="{{ json_encode($staffs->items()) }}"
      :pagination="{{ json_encode($staffs->toArray()) }}"
    ></staff-table>
  </div>
@endsection
