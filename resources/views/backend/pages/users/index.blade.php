@extends('backend.layouts.main')

@push('js')
@endpush

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>İstifadəçilər</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>İstifadəçilər</strong>
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
                                <h5>İstifadəçilər</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('users.create') }}" class="btn btn-w-m btn-primary">Yeni</a>
                            </div>
                        </div>


                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover dataTables-base" data-order="2">
                            <thead>
                                <tr>
                                    <th>Şəkil</th>
                                    <th>Ad</th>
                                    <th>E-Poçt</th>
                                    <th>Telefon</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $data)
                                    <tr class="gradeX">

                                        <td>
                                            @if (!empty($data->picture))
                                                <img width="50" height="50" class="rounded-circle"
                                                    src="{{ getImageUrl($data->picture, 'users') ?? null }}" alt="">
                                            @endif

                                        </td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->phone }}</td>

                                        <td class="text-right">
                                            <a href="{{ route('users.edit', $data->id) }}"
                                                class="btn btn-warning btn-sm">Yenilə</a>
                                            <a href="{{ route('users.delete', $data->id) }}"
                                                class="btn btn-danger btn-sm">Sil</a>
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
