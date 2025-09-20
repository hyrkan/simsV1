@extends('admin_master.layout')

@section('page-title', 'Dashboard')

@php
$breadcrumbItems = [];
@endphp

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Welcome to Admin Dashboard</h5>
            </div>
            <div class="card-body">
                <p>This is the main dashboard page with dynamic breadcrumb navigation.</p>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h4>150</h4>
                                <p>Total Students</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h4>25</h4>
                                <p>Total Staff</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h4>12</h4>
                                <p>Total Classes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h4>8</h4>
                                <p>Total Subjects</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection