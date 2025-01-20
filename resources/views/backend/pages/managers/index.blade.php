@extends('backend.layouts.main')

@push('js')


@endpush

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>İdarəçilər</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>İdarəçilər</strong>
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
                                <h5>İdarəçilər</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('managers.create') }}" class="btn btn-w-m btn-primary">Yeni</a>
                            </div>
                        </div>


                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover dataTables-base" data-order="2">
                            <thead>
                                <tr>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($managers as $data)
                                <tr class="gradeX">

                                    <td>
                                        <img
                                            width="50"
                                            height="50"
                                            class="rounded-circle"
                                            src="{{ $data->avatar ? asset('avatars/' . $data->avatar) : asset('assets/images/default.jpg') }}"
                                            alt="">
                                    </td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->username }}</td>
                                    <td>{{ $data->email }}</td>
                                    <td>{{ $data->position }}</td>
                                    <td>{{ $data->phone }}</td>
                                    <td>{{ $data->role?->name }}</td>

                                    <td class="text-right">
                                        <a href="{{ route('managers.edit', $data->id) }}" class="btn btn-warning btn-sm">Düzenle</a>
                                        <a href="{{ route('managers.delete', $data->id) }}" class="btn btn-danger btn-sm">Sil</a>
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
