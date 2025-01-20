@extends('backend.layouts.main')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-success float-right"><i class="fa fa-users"></i></span>
                        <h5>İstifadəçilər</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ $users }}</h1>
                        <small>Ümumi istifadəçi sayı</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-info float-right"><i class="fa fa-credit-card-alt"></i></span>
                        <h5>İmtahanlar</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ $exams }}</h1>
                        <small>Ümumi imtahan sayı</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-primary float-right"><i class="fa fa-language" ></i></span>
                        <h5>İmtahan nəticələri</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ $exam_results }}</h1>
                        <small>Ümumi imtahan nəticəsi</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-danger float-right"><i class="fa fa-pencil"></i></span>
                        <h5>İdarəçilər</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ $admins }}</h1>
                        <small>Ümumi idarəçi sayı</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


