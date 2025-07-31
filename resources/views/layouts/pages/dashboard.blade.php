@include('layouts.head')

@include('layouts.partials.sidebar')
@include('layouts.partials.header')
<div class="pc-container">
    <div class="pc-content"><!-- [ Main Content ] start -->
        @yield('content')
    </div>
</div>

@include('layouts.partials.footer')

@section('js')
    {{-- <script src="{{ asset('') }}/assets/js/plugins/apexcharts.min.js"></script> --}}
    {{-- <script src="{{ asset('assets/js/pages/dashboard-default.js') }}"></script> --}}

    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    {{-- <script src="/assets/js/config.js"></script> --}}
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    @yield('custom-js')
@endsection

@include('layouts.foot')
