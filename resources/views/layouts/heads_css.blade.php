<!-- [Font] Family -->
<link href="{{ asset('admin/assets/fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap') }}" rel="stylesheet" />

<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('admin/assets/fonts/tabler-icons.min.css') }}" />

<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('admin/assets/fonts/feather.css') }}" />

<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('admin/assets/fonts/fontawesome.css') }}" />

<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('admin/assets/fonts/material.css') }}" />

<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}" id="main-style-link" />
<link rel="stylesheet" href="{{ asset('admin/assets/css/style-preset.css') }}" />

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-14K1GBX9FG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());

  gtag('config', 'G-14K1GBX9FG');
</script>

<!-- WiserNotify -->
<script>
    window.t4hto4 = window.t4hto4 || function() {
        (t4hto4.q = t4hto4.q || []).push(arguments)
    };
    if (window.t4hto4) console.log('WiserNotify pixel installed multiple times in this page');
    t4hto4('init', '1jclj6jkfc4hhry');
    t4hto4('event', 'pageload');
</script>
<script>
    (function(e, r, n) {
        var t = {};
        var i = e.getElementsByTagName("script")[0];
        var s = function() {
            t._c = {};
            t._r = Math.random();
            var n = r.createElement("script");
            n.type = "text/javascript";
            n.async = true;
            n.crossOrigin = "anonymous";
            n.src = "https://pt.wisernotify.com/pixel.js?ti=1jclj6jkfc4hhry";
            i.parentNode.insertBefore(n, i);
        };
        if (e.readyState === "complete") {
            s();
        } else if (window.addEventListener) {
            e.addEventListener("load", s, false);
        } else {
            e.attachEvent("onload", s);
        }
    })(document, document, window);
</script>

<!-- Microsoft Clarity -->
<script type="text/javascript">
  (function (c, l, a, r, i, t, y) {
    c[a] =
      c[a] ||
      function () {
        (c[a].q = c[a].q || []).push(arguments);
      };
    t = l.createElement(r);
    t.async = 1;
    t.src = 'https://www.clarity.ms/tag/' + i;
    y = l.getElementsByTagName(r)[0];
    y.parentNode.insertBefore(t, y);
  })(window, document, 'clarity', 'script', 'gkn6wuhrtb');
</script>
