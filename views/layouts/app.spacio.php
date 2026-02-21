<template>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
        <main>
            @yield('content')
        </main>
    </body>
    <script src="/spacio.js" defer></script>
    </html>
</template>
