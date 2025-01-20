@extends('backend.layouts.main')

@push('js')
@endpush

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>İmatahan nəticələri</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>İmatahan nəticələri</strong>
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
                                <h5>İmatahan nəticələri</h5>
                            </div>
                            <div class="col-6">
                                <form action="">
                                    <div class="row">
                                        <div class="col-3">
                                            <label for="">Tarix aralığı</label>
                                            <input type="text" name="date" autocomplete="off"
                                                value="{{ request('date') }}" class="date-range-picker form-control">
                                        </div>
                                        <div class="col-3">
                                            <label for="">Kateqoriya</label>
                                            <select name="category"
                                                class="form-control {{ $errors->first('category_id') ? 'is-invalid' : '' }}">
                                                <option hidden disabled selected>Seçim edin</option>
                                                @foreach (\App\Models\Category::whereNull('parent_id')->with('sub')->get() as $category)
                                                    <optgroup label="{{ $category->name['az_name'] }}">
                                                        @foreach ($category->sub as $sub)
                                                            <option {{ request('category') == $sub->id ? 'selected' : '' }}
                                                                value="{{ $sub->id }}">{{ $sub->name['az_name'] }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <label for="">İstifadəçi</label>
                                            <select name="user"
                                                class="form-control {{ $errors->first('category_id') ? 'is-invalid' : '' }}">
                                                <option hidden disabled selected>Seçim edin</option>
                                                @foreach (\App\Models\User::all() as $user)
                                                    <option {{ request('user') == $user->id ? 'selected' : '' }}
                                                        value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-3 align-self-end">
                                            <button type="submit" class="btn btn-primary">Filtr</button>
                                            @if (!empty(request('user')) || !empty(request('date')) || !empty(request('category')))
                                                <a href="{{ url()->current() }}" class="btn btn-info">Təmizlə</a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover dataTables-base" data-order="2">
                            <thead>
                                <tr>
                                    <th>İstifadəçi</th>
                                    <th>Kateqoriya</th>
                                    <th>İmtahan</th>
                                    <th>Bal</th>
                                    <th>Tarix</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $data)
                                    @if (!empty($data) && !empty($data->exam) && !empty($data->user))
                                        <tr class="gradeX">

                                            <td>{{ @$data->user?->name }}</td>
                                            <td>{{ @$data->exam?->category?->name['az_name'] }}</td>
                                            <td>{{ @$data->exam?->name['az_name'] }}</td>
                                            <td>{{ @$data->exam?->point ?? 0 * $data->correctAnswers() }}</td>
                                            <td>{{ @$data->created_at?->format('d F Y H:i') }}</td>

                                            <td class="text-right">
                                                <a href="{{ route('exam.result.show', $data->id) }}"
                                                    class="btn btn-warning btn-sm">Ətraflı</a>

                                                    <a href="{{ route('exam.result.destroy', $data->id) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
