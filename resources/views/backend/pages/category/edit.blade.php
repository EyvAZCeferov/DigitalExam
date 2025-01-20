@extends('backend.layouts.main')

@push('js')
    <script>
        $(document).ready(function () {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>
@endpush

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Kateqoriyalar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('categories.index') }}">Kateqoriyalar</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Redaktə et</strong>
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
                                <h5>Redaktə et</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('categories.index') }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('categories.update', $category->id) }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Ad</label>
                                        <input type="text"
                                               value="{{ old('name', $category->name) }}"
                                               name="name"
                                               class="form-control {{ $errors->first('name') ? 'is-invalid' : '' }}">

                                    </div>
                                </div>
                                @if($category->parent_id)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Parent</label>
                                            <select name="parent_id" class="form-control {{ $errors->first('parent_id') ? 'is-invalid' : '' }}">
                                                <option hidden disabled selected>Seçim edin</option>
                                                @foreach(\App\Models\Category::whereNull('parent_id')->whereNot('id', $category->id)->get() as $parent)
                                                    <option {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }} value="{{ $parent->id }}">{{ $parent->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <br>

                            <button class="btn btn-primary">Yenilə</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
