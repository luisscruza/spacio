<template>
    @extends('layouts.app')

    @section('title')
        User {{ user.id }}
    @endsection

    @section('content')
        <div class="mx-auto w-full max-w-3xl px-6 py-12">
            <div class="rounded-3xl border border-amber-100 bg-white/90 p-8 shadow-[0_24px_70px_rgba(15,23,42,0.12)]">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">
                    Profile
                </div>

                <div class="mt-6 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-semibold text-stone-900">User {{ user.id }}</h1>
                        <p class="mt-2 text-sm text-stone-500">Account overview and quick actions.</p>
                    </div>
                    <a href="/" class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-white px-5 py-2 text-sm font-semibold text-stone-700 shadow-sm transition hover:-translate-y-0.5 hover:border-stone-300 hover:text-stone-900">Back to home</a>
                </div>

                <div class="mt-8 grid gap-6 sm:grid-cols-2">
                    <div class="rounded-2xl border border-stone-200 bg-white px-6 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">Name</p>
                        <p class="mt-3 text-lg font-semibold text-stone-800">{{ user.name }}</p>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white px-6 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">User ID</p>
                        <p class="mt-3 text-lg font-semibold text-stone-800">{{ user.id }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</template>
