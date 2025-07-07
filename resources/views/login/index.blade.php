<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOGIN | DND APP</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('template') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('template') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('template') }}/dist/css/adminlte.min.css">

    <!-- PWA  -->
    <meta name="theme-color" content="#6777ef" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-danger" style="border-color: #917FB3;">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>DnD App</b></a>
            </div>
            <div class="card-body">
                <!-- <p class="login-box-msg">Sign in</p> -->
                <form action="/login" method="POST">
                    @csrf
                    <div class="input-group mb-3 mt-3">
                        <input type="text" class="form-control" placeholder="User Name" name="username" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <!-- /.col -->
                        <div class="col-4 ">
                            <button type="submit" class="btn btn-block"
                                style="background-color: #917FB3; color: white;">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <script src="{{ asset('sw.js') }}"></script>
    <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                },
            );
        } else {
            console.error("Service workers are not supported.");
        }
    </script>

</body>

</html>

{{-- @extends('layouts.pages.guest')

@section('content')
    <div class="auth-main">
        <div class="auth-wrapper v1">
            <form class="auth-form" action="/login" method="POST">
                @csrf
                <div class="card my-5">
                    <div class="card-body">
                        <div class="text-center mb-5">
                            <img src="{{ asset('dnd-purple.png') }}" width="100" alt="img">
                        </div>

                        <!-- Alert for Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" id="floatingUsername"
                                placeholder="User Name" name="username" autofocus>
                            <label for="floatingUsername">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" placeholder="Password" id="floatingPassword"
                                name="password">
                            <label for="floatingPassword">Password</label>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection --}}
