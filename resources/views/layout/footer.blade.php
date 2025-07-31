<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 2.0.0
    </div>
    <strong>Copyright &copy; 2023 - 2024 <a href="#" style="color: #917FB3;">BUSINESS DEVELOPMENT</a>.</strong> All
    rights
    reserved.
</footer>

<!-- jQuery -->
<script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap 4 Bundle -->
<script src="{{ asset('template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- overlayScrollbars -->
<script src="{{ asset('template/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('template/dist/js/adminlte.min.js') }}"></script>

<!-- AdminLTE Demo (Optional for demo purposes) -->
<script src="{{ asset('template/dist/js/demo.js') }}"></script>

<!-- Moment.js (for date manipulation) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>

<!-- Select2 (for advanced select boxes) -->
<script src="{{ asset('template/plugins/select2/js/select2.full.min.js') }}"></script>

<!-- Date Range Picker -->
<script src="{{ asset('template/plugins/daterangepicker/daterangepicker.js') }}"></script>

<!-- Bootstrap4 Duallistbox -->
<script src="{{ asset('template/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>

<!-- Select2 -->
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> --}}
<!-- datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<script src="{{ asset('script.js') }}"></script>
{{-- <script src="{{ asset('sw.js') }}"></script>
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
</script> --}}
@yield('footer')
</body>

</html>
