<!-- Required Js -->
<script src="{{ asset('admin/assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('admin/assets/js/script.js') }}"></script>
<script src="{{ asset('admin/assets/js/theme.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugins/feather.min.js') }}"></script>

@stack('scripts')

{{-- if (pc_dark_layout == 'default') {
<script>
  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    dark_layout = 'true';
  } else {
    dark_layout = 'false';
  }
  layout_change_default();
  if (dark_layout == 'true') {
    layout_change('dark');
  } else {
    layout_change('light');
  }
</script>
}  --}}