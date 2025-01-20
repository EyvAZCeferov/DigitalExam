@extends('frontend.layouts.app')
@section('title', trans('additional.headers.register'))
@push('css')
    <style>
        .pin-code {
            padding: 0;
            margin: 0 auto;
            display: flex;
            justify-content: center;

        }

        .pin-code input {
            border: none;
            text-align: center;
            width: 48px;
            height: 48px;
            font-size: 36px;
            background-color: #F3F3F3;
            margin-right: 5px;
            border-radius: 10px;
        }



        .pin-code input:focus {
            border: 1px solid #573D8B;
            outline: none;
        }


        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endpush
@push('js')
    <script>
        function sendform(event, id) {
            event.preventDefault();
            showLoader();
            let formData = new FormData(document.querySelector('#' + id));
            const data = {};
            for (const [key, value] of formData.entries()) {
                if (key === 'picture' && value instanceof File && value.size > 0) {
                    data[key] = value;
                } else if (key !== 'picture') {
                    data[key] = value;
                }
            }

            $.ajax({
                url: "{{ route('user.register') }}",
                type: "post",
                data: data,
                dataType: "json",
                headers: {
                    'X-CSRF-Token': $("meta[name=_token]").attr('content'),
                },
                success: function(response) {
                    let parsedResponse = response;
                    if (response.message != null) toast(response.message, response.status);

                    if (parsedResponse.status != null && parsedResponse.message != null) {
                        hideLoader();
                        toast(parsedResponse.status, parsedResponse.message);
                    }

                    if (parsedResponse.url != null) {
                        hideLoader();
                        window.location.href = parsedResponse.url;
                    }

                    if (response.authattempt != null) {
                        hideLoader();
                        toggleModalnow('modal_pin_code', 'open')
                        // var modal_a=$("#modal_pin_code");
                        // modal_a.toggle();
                        document.getElementById("attempt_id").value = response.authattempt.id
                    }

                },
                error: function(xhr, statusText, error) {
                    hideLoader();
                    toast(error, 'danger');
                }
            });
        }

        var pinContainer = document.querySelector(".pin-code");
        pinContainer.addEventListener('keyup', function(event) {
            var target = event.srcElement;

            var maxLength = parseInt(target.attributes["maxlength"].value, 10);
            var myLength = target.value.length;

            if (myLength >= maxLength) {
                var next = target;
                while (next = next.nextElementSibling) {
                    if (next == null) break;
                    if (next.tagName.toLowerCase() == "input") {
                        next.focus();
                        break;
                    }
                }
            }

            if (myLength === 0) {
                var next = target;
                while (next = next.previousElementSibling) {
                    if (next == null) break;
                    if (next.tagName.toLowerCase() == "input") {
                        next.focus();
                        break;
                    }
                }
            }

            var allFilled = true;
            var pincode = '';
            var inputs = pinContainer.querySelectorAll('input');
            inputs.forEach(function(input) {
                if (input.value.length < input.maxLength) {
                    allFilled = false;
                } else {
                    pincode += input.value + '';
                }
            });

            if (allFilled) {
                document.getElementById("pin_code").value = pincode;
                sendform(event, 'sendform');
            }
        }, false);

        pinContainer.addEventListener('keydown', function(event) {
            var target = event.srcElement;
            target.value = "";
        }, false);
    </script>
@endpush
@section('content')
    <section class="register_or_login py-5">
        <div class="row">
            <div class="col-0 col-sm-0 col-md-6 col-lg-7">
                <img src="{{ asset('front/assets/img/bg_images/register_bg.png') }}" class="img-responsive"
                    alt="{{ trans('additional.headers.login') . ' / ' . trans('additional.headers.register') }}">
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 right_column">
                <h2 class="text-center mt-2 mb-5">@lang('additional.headers.register')</h2>
                <form method="post" class="w-100" enctype="multipart/form-data" onsubmit="sendform(event,'user_register')"
                    id="user_register">
                    @csrf
                    <input name="subdomain" value="{{ session()->get('subdomain') ?? null }}" type="hidden">
                    <div class="row">
                        <div class="user_or_freelancer_row">
                            <div class="user_or_freelancer_tab user_or_freelancer_tab_student active"
                                onclick="tabselect('student')">
                                @lang('additional.forms.user_type_1')
                            </div>
                            <div class="user_or_freelancer_tab user_or_freelancer_tab_company"
                                onclick="tabselect('company')">
                                @lang('additional.forms.user_type_2')
                            </div>
                        </div>
                    </div>

                    @csrf
                    <input type="hidden" name="user_type" id="user_type" value="1">

                    <div class="account-form-item mb-3">
                        <div class="account-form-input">
                            <input type="text" placeholder="@lang('additional.forms.name')" name="name" value="{{ old('name') }}"
                                class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input">
                            <input type="email" placeholder="@lang('additional.forms.email')" name="email" value="{{ old('email') }}"
                                class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input">
                            <input type="text" placeholder="@lang('additional.forms.phone')" id="phone" name="phone"
                                value="{{ old('phone') }}" class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="account-form-item mb-3 tab_company_element" style="display: none">
                        <label for="file-upload" class="account-form-input custom-file-upload ">
                            <input id="file-upload" onchange="changedFileLabel('file-upload')" name="picture"
                                type="file">
                            <span class="file-name"> @lang('additional.forms.picture')</span>
                        </label>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input account-form-input-pass">
                            <input type="password" class="form-control form-control-lg" placeholder="@lang('additional.forms.password')"
                                name="password" value="{{ old('password') }}" id="password">
                            <span id="password_icon" class="input_icon" onclick="toggleInputFunction('password')"><i
                                    class="fa fa-eye-slash"></i></span>
                        </div>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input account-form-input-pass">
                            <input type="password" class="form-control form-control-lg" placeholder="@lang('additional.forms.password_confirmation')"
                                name="password_confirmation" value="{{ old('password_confirmation') }}"
                                id="password_confirmation">
                            <span id="password_confirmation_icon" class="input_icon"
                                onclick="toggleInputFunction('password_confirmation')"><i
                                    class="fa fa-eye-slash"></i></span>
                        </div>
                    </div>

                    <div class="account-form-button mt-4 mb-2">
                        <button type="submit" class="btn btn-primary btn-block">@lang('additional.headers.register')</button>
                    </div>
                </form>
            </div>
        </div>


        {{-- Modal Pin Code --}}
        <div id="modal_pin_code" class="modal custom-modal modal-lg" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-dialog-centered" role="document">

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" onclick="toggleModalnow('modal_pin_code', 'hide')"
                            data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="fin_code" />
                        <input type="hidden" name="attempt_id" />
                        <input type="hidden" name="user_id" />
                        <h1 class="text-center">Birdəfəlik şifrə</h1>
                        <div class="pin-code">
                            <input type="number" name="pin_code[0]" maxlength="1" autofocus id="pin_code_0">
                            <input type="number" name="pin_code[1]" maxlength="1">
                            <input type="number" name="pin_code[2]" maxlength="1">
                            <input type="number" name="pin_code[3]" maxlength="1">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- Modal Pin Code --}}
    </section>
@endsection
