@extends('backend.layouts.main')

@push('js')
    <script>
        var elem = document.querySelector('.js-switch');
        var switchery = new Switchery(elem, {
            color: '#1AB394'
        });
    </script>
@endpush

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Kupon Kod</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('coupon_codes.index') }}">Kupon Kod</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Yeni</strong>
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
                                <h5>Yeni</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('coupon_codes.index') }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form
                            @if (isset($data) && !empty($data) && isset($data->id)) action="{{ route('coupon_codes.update', $data->id) }}" @else action="{{ route('coupon_codes.store') }}" @endif
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($data) && !empty($data) && isset($data->id))
                                @method('PUT')
                            @endif

                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                        <button
                                            class="nav-link {{ $localeCode }} @if ($localeCode == 'az') active @endif"
                                            id="nav-{{ $localeCode }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-{{ $localeCode }}" type="button" role="tab"
                                            aria-controls="nav-{{ $localeCode }}" aria-selected="true"
                                            onclick="changeTabElements('{{ $localeCode }}')">{{ $localeCode }}</button>
                                        &nbsp;
                                    @endforeach
                                </div>
                            </nav>

                            <div class="tab-content" id="nav-tabContent">
                                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                    <div class="tab-pane {{ $localeCode }} fade @if ($localeCode == 'az') show active @endif pt-3"
                                        id="nav-{{ $localeCode }}" role="tabpanel"
                                        aria-labelledby="nav-{{ $localeCode }}-tab">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Ad <span
                                                            class="badge badge-info">{{ strtoupper($localeCode) }}</span></label>
                                                    <input type="text"
                                                        value="{{ old('' . $localeCode . '_name', isset($data) && !empty($data) && isset($data->name['' . $localeCode . '_name']) && !empty($data->name['' . $localeCode . '_name']) ? $data->name['' . $localeCode . '_name'] : null) }}"
                                                        name="{{ $localeCode }}_name"
                                                        class="form-control {{ $errors->first('' . $localeCode . '_name') ? 'is-invalid' : '' }}">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Tip</label>
                                        <select name="type"
                                            class="form-control {{ $errors->first('type') ? 'is-invalid' : '' }}">
                                            <option value="value">Məbləğ</option>
                                            <option value="percent">Faiz</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Status</label>
                                        <input type="checkbox" name="status"
                                            {{ isset($data) && !empty($data) && $data->status ? 'checked' : '' }}
                                            class="js-switch {{ $errors->first('status') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Dəyər</label>
                                        <input type="number"  name="discount"
                                            value="{{ isset($data) && !empty($data) && $data->discount ? $data->discount : 0 }}"
                                            class="form-control {{ $errors->first('discount') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Kod</label>
                                        <input type="text"  name="code"
                                            value="{{ isset($data) && !empty($data) && $data->code ? $data->code : null }}"
                                            class="form-control {{ $errors->first('code') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <button class="btn btn-primary">Saxla</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
