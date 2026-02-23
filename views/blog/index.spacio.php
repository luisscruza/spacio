<template>
    @extends('layouts.app')

    @section('title')
        Blog
    @endsection

    @section('content')
        <div class="mx-auto w-full max-w-4xl px-6 py-12">
            @component('blog.index')
        </div>
    @endsection
</template>
