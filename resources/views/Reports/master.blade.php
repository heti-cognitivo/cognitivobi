<html>
    <head>
        <title>@yield('title')</title>
        <meta name="description" content="@yield('title')">
    		<meta name="author" content="core team @ Cognitivo">
        @section('favicon')
        <link href="../images/favicon.ico" rel="icon" type="image/x-icon" />
        @show
        @yield('csslinks')
        @yield('jslinks')
    </head>
    <body>
        <div id="header">
          @yield('topbar')
        </div>
        <div class="contents">
            @yield('content')
        </div>
      @section('footer')
      <p style="color:white; font-size:8px;">COGNITIVO &copy; <script>document.write(new Date().getFullYear());</script></p>
      @show
    </body>
</html>
