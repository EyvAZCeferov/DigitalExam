@extends('backend.layouts.main')

@push('js')

@endpush

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Rollar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('roles.index') }}">Rollar</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Yenilə</strong>
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
                                <h5>Yenilə</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('roles.index') }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>

                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('roles.update', $role->id) }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Ad</label>
                                        <input type="text"
                                               value="{{ old('name', $role->name) }}" name="name"
                                               class="form-control {{ $errors->first('name') ? 'is-invalid' : '' }}">

                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Permissions</label>
                                        <select name="permissions[]"
                                                class="form-control {{ $errors->first('permissions') ? 'is-invalid' : '' }} dual_select" multiple>
                                            @foreach(config('permissions') as $permission)
                                                <option
                                                    {{ collect(old('permissions', $role->permissions))->contains($permission) ? 'selected' : '' }} value="{{ $permission }}">{{ $permission }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <button class="btn btn-primary">Yenilə</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
