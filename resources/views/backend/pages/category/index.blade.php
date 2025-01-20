@extends('backend.layouts.main')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Kateqoriyalar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Kateqoriyalar</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="ibox-tools mb-3">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="fullscreen-link">
                                <i class="fa fa-expand"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5>Kateqoriyalar</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('categories.create') }}" class="btn btn-w-m btn-primary">Yeni</a>
                            </div>
                        </div>


                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover dataTables-base" data-order="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Ad</th>
                                <th>Parent</th>
                                <th>Yaradılıb</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $data)
                                <tr class="gradeX">

                                    <td>{{ $data->id }}</td>
                                    <td>{{ $data->name['az_name'] }}</td>
                                    <td>{{ $data->parent?->name['az_name'] ?? '' }}</td>
                                    <td>{{ $data->created_at }}</td>

                                    <td class="text-right">
                                        <a href="{{ route('categories.edit', $data->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-pencil-square-o"></i></a>
                                        <a href="{{ route('categories.delete', $data->id) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
