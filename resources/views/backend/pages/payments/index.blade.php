@extends('backend.layouts.main')


@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Ödənişlər</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Ödənişlər</strong>
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

                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover dataTables-base" data-order="2">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Payment Status</th>
                                    <th>Məbləğ</th>
                                    <th>Imtahan</th>
                                    <th>İstifadəçi</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $data)
                                    <tr class="gradeX">

                                        <td>{{ $data->transaction_id }}</td>
                                        <td>
                                            @if ($data->payment_status == 0)
                                                <span class="text-center text-warning">Gözləyir</span>
                                            @elseif($data->payment_status == 1)
                                                <span class="text-center text-success">Uğurlu</span>
                                            @elseif($data->payment_status == 2)
                                                <span class="text-center text-danger">Uğursuz</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->amount }}₼</td>
                                        <td>
                                            {!! !empty($data->exam) ? $data->exam->name[app()->getLocale() . '_name'] : '<span class="text-danger text-center">Silinmiş imtahan</span>' !!}
                                        </td>
                                        <td>
                                            {!! !empty($data->user) ? $data->user->name : '<span class="text-danger text-center">Silinmiş İstifadəçi</span>' !!}
                                        </td>

                                        <td class="text-right">
                                            <a href="{{ route('payments.delete', $data->id) }}"
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
