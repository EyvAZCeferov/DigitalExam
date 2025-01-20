@extends('backend.layouts.main')



@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>İmtahanlar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>İmtahanlar</strong>
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
                                <h5>İmtahanlar</h5>
                            </div>
                            <div class="col-6 text-right">
                                @if($trashed==true)
                                    <a href="{{ route('exams.index',['trashed'=>'nottrashed']) }}" class="btn btn-w-m btn-warning">Aktiv List</a>
                                @else
                                    <a href="{{ route('exams.index',['trashed'=>'trashed']) }}" class="btn btn-w-m btn-danger">Silinmişlər</a>
                                @endif
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('exams.create') }}" class="btn btn-w-m btn-primary">Yeni</a>
                            </div>
                        </div>


                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover dataTables-base" data-order="2">
                            <thead>
                            <tr>
                                <th>Ad</th>
                                <th>Bal</th>
                                <th>Kateqoriya</th>
                                <th>Active</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($exams as $data)
                                <tr class="gradeX">

                                    <td>{{ $data->name[app()->getLocale().'_name'] }}</td>
                                    <td>{{ $data->point }}</td>
                                    <td>{{ $data->category?->name['az_name'] }}</td>
                                    <td>{{ $data->status ? 'Bəli' : 'Xeyr' }}</td>

                                    <td class="text-right">
                                        <a href="{{ route('exams.questions', $data->id) }}" class="btn btn-info btn-sm">Suallar</a>
                                        <a href="{{ route('exams.edit', $data->id) }}" class="btn btn-warning btn-sm">Yenilə</a>
                                        <a href="{{ route('exams.delete', $data->id) }}" class="btn btn-danger btn-sm">Sil</a>
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
