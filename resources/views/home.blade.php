<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Movies2Gether</title>

        <link rel="icon" href="{{ URL::asset('storage/assets/Icon.png') }}" type="image/x-icon"/>
        <link href="https://vjs.zencdn.net/7.10.2/video-js.css" rel="stylesheet" />
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    </head>
    <body class="antialiased">
        <div id="app">
            <App></App>
        </div>

        <script src="https://vjs.zencdn.net/7.10.2/video.min.js"></script>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
