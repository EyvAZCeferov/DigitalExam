@extends('backend.layouts.main')

@push('css')

    <style>

        .user__avatar__update {
            padding-bottom: 50px;
        }

        .user__avatar__update img {
            width: 150px;
            height: 150px;
            border-radius: 100%;
        }

        .user__avatar__update .avatar {
            width: 150px;
            height: 150px;
            position: relative;
        }

        .user__avatar__update .overlay {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            visibility: hidden;
            opacity: 0;
            transition: 0.4s linear;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 100%;
        }

        .user__avatar__update .avatar:hover .overlay {
            visibility: visible;
            opacity: 1;
        }

        .user__avatar__update .overlay span {
            color: #fff;
            display: inline-block;
            font-family: SF Pro Text, sans-serif, Arial;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            letter-spacing: -.24px;
            line-height: 22px;
        }

        .user__avatar__update input {
            position: absolute;
            visibility: hidden;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .custom__modal {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1071;
            display: none;
            padding: 0.5rem;
        }

        .custom__modal.show {
            display: block;
        }

        .custom__modal .modal__overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            z-index: -1;

            background-color: #000;
            opacity: .5;
        }

        .custom__modal .modal__body {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .2);
            border-radius: 0.3rem;
            outline: 0;
            max-width: 500px;
            margin: 1.75rem auto;

            padding: 1rem;
        }


        input[type="range"]::-webkit-slider-runnable-track {
            background: #ffffff;
            border: 1px solid silver;
            height: 0.5rem;
            border-radius: 3px;
        }

        /******** Firefox ********/
        input[type="range"]::-moz-range-track {
            background: #ffffff;
            border-radius: 3px;
            border: 1px solid silver;
            height: 0.5rem;
        }

        input[type="range"]::-webkit-slider-thumb {
            margin-top: -4.5px;
            cursor: pointer;
        }

        .image-crop img {
            max-height: 300px;
        }

    </style>

@endpush

@push('js')

    <script>
        let isInitialized = false;
        let cropper = '';
        let $image = $(".image-crop > img")
        let $inputImage = $('#avatar');

        $inputImage.change(function () {
            $('.modal').addClass('show')
            let fileReader = new FileReader(),
                files = this.files,
                file;

            if (!files.length) {
                return;
            }

            file = files[0];

            if (/^image\/\w+$/.test(file.type)) {
                fileReader.readAsDataURL(file);
                fileReader.onload = function () {
                    $inputImage.val("");
                    $('.custom__modal').addClass('show')
                    $($image).attr('src', this.result)
                    $($image).addClass('ready')

                    if (isInitialized == true) {
                        cropper.destroy();
                    }
                    initCropper();
                };
            } else {
                alert("Lütfen bir resim dosyası seçin.");
            }
        });

        function initCropper() {
            cropper = new Cropper($($image)[0], {
                viewMode: 1,
                dragMode: 'move',
                aspectRatio: 1,
                zoomOnTouch: true,
                zoomOnWheel: true,
                responsive: true,
                ready: function (e) {
                    let cropper = this.cropper;
                    cropper.zoomTo(0);

                    let imageData = cropper.getImageData();

                    let minSliderZoom = imageData.width / imageData.naturalWidth;

                    $(".image__zoom").attr("max", 1);
                    $(".image__zoom").attr("min", minSliderZoom);
                    $(".image__zoom").attr("value", minSliderZoom);
                }
            });
            isInitialized = true;
        }

        $('.modal_close').on('click', function () {
            $('.custom__modal').removeClass('show')
        })

        $('.crop_image').on('click', function () {

            cropper.getCroppedCanvas().toBlob((blob) => {
                const formData = new FormData();

                formData.append('avatar', blob);
                formData.append('_token', "{{ csrf_token() }}");

                // Use `jQuery.ajax` method for example
                $.ajax("{{ route('admin.update.avatar') }}", {
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success(response) {
                        if (response.success) {
                            toastr.success(response.success, "Success");
                            $('.user__avatar__update img').attr('src', response.url)
                            $('.custom__modal').removeClass('@show')
                        }
                        else {
                            toastr.error(response.error, "Error");
                        }
                    },
                    error(error) {
                        toastr.error(error, "Error");
                    },
                });
            });
        })

        $('.image__zoom').on('input', function () {
            let zoom = parseFloat($(this).val()).toFixed(4)
            console.log(zoom)
            cropper.zoomTo(zoom);
        })
    </script>

@endpush


@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Profil</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Gösterge Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Profil</strong>
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
                        <div class="ibox-tools">
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
                        <div class="user__avatar__update">
                            <div class="avatar">
                                <img
                                    src="{{ auth('admins')->user()?->avatar ? asset('avatars/' . auth('admins')->user()?->avatar) : asset('assets/images/default.jpg') }}"
                                    alt="">
                                <label for="avatar" class="overlay"><span>Fotoğraf seç</span></label>
                                <input type="file" id="avatar" value="avatar">
                            </div>
                        </div>
                        <form action="{{ route('admin.profile.save') }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">İsim soyisim</label>
                                        <input type="text"
                                               value="{{ old('name', auth('admins')->user()->name) }}" name="name"
                                               class="form-control {{ $errors->first('name') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Pozisyon</label>
                                        <input type="text"
                                               value="{{ old('position', auth('admins')->user()->position) }}" name="position"
                                               class="form-control {{ $errors->first('position') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">E-posta</label>
                                        <input type="text"
                                               value="{{ old('email', auth('admins')->user()->email) }}" name="email"
                                               class="form-control {{ $errors->first('email') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Telefon</label>
                                        <input type="text"
                                               value="{{ old('phone', auth('admins')->user()->phone) }}" name="phone"
                                               class="form-control {{ $errors->first('phone') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Kullanıcı adı</label>
                                        <input type="text"
                                               value="{{ old('username', auth('admins')->user()->username) }}" name="username"
                                               class="form-control {{ $errors->first('username') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Şifre</label>
                                        <input type="password"
                                               value="" name="password"
                                               class="form-control {{ $errors->first('password') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
{{--                                <div class="col-md-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="">Role</label>--}}
{{--                                        <select name="role" class="form-control">--}}
{{--                                            <option value="admin" selected>Admin</option>--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>

                            <button class="btn btn-primary">Kaydet</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="custom__modal">
        <div class="modal__overlay modal_close"></div>
        <div class="modal__body">
            <div class="image-crop">
                <img src="{{ asset('assets/images/default.jpg') }}">
            </div>

            <input type="range" style="margin-top: 20px;" value="0" step="0.0001" min="0" max="1" class="image__zoom">

            <div class="d-flex justify-content-between" style="margin-top: 20px;">
                <button type="button" class="btn btn-default modal_close" ><span>İptal etmek</span></button>
                <button type="button" class="btn btn-primary crop_image" ><span>Kırp</span></button>
            </div>
        </div>
    </div>

@endsection
