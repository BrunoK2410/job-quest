<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{secure_asset('css/style.css')}}">
    <script src="//unpkg.com/alpinejs" defer></script>
    <title>{{$title ?? 'Job Quest | Find and list jobs'}}</title>
</head>

<body class="bg-amber-50">
    <x-header/>
    @if(request()->is('/'))
    <x-hero/>
    <x-top-banner/>
    @endif
    <main class="container mx-auto p-4 mt-4">
        {{-- Display alert messages --}}
        @if(session('success'))
        <x-alert type="success" message="{{session('success')}}" />
        @endif
        @if(session('error'))
        <x-alert type="error" message="{{session('error')}}" />
        @endif
        {{$slot}}
    </main>

    <script src="{{secure_asset('js/script.js')}}"></script>
</body>

</html>