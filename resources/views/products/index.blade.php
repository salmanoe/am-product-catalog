<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Product Catalog</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div id="app">
            <product-list></product-list>
        </div>
    </body>
</html>