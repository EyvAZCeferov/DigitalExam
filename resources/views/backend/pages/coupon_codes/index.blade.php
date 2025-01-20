@extends('backend.layouts.main')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Kupon kodlar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Kupon kodlar</strong>
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
                                <h5>Kupon kodlar</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('coupon_codes.create') }}" class="btn btn-w-m btn-primary">Yeni</a>
                            </div>
                        </div>


                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover dataTables-base" data-order="2">
                            <thead>
                                <tr>
                                    <th>Ad</th>
                                    <th>Kod</th>
                                    <th>Dəyər</th>
                                    <th>Əlavə edən</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $dat)
                                    <tr class="gradeX">
                                        <td>{{ $dat->name[app()->getLocale() . '_name'] }}</td>
                                        <td>{{ $dat->code }}</td>
                                        <td>{{ $dat->discount }}</td>
                                        <td>{{ $dat->user->name }} / {{ $dat->user->email }} </td>
                                        <td>
                                            <span class="text @if($dat->status==true) text-success @else text-danger @endif">
                                                @if($dat->status==true) Aktiv @else Passiv @endif
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('coupon_codes.edit', $dat->id) }}"
                                                class="btn btn-warning btn-sm">Yenilə</a>
                                            <form action="{{ route('coupon_codes.destroy', $dat->id) }}"
                                                class="d-inline-block" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                                            </form>
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
