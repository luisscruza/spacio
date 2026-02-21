<template>
    @extends('layouts.app')

    @section('title')
        Create user
    @endsection

    @section('content')
        <div class="mx-auto w-full max-w-3xl px-6 py-12">
             <!-- <div class="rounded-3xl border border-amber-100 bg-white/90 p-8 shadow-[0_24px_70px_rgba(15,23,42,0.12)]">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">
                    New user
                </div>
                <h1 class="mt-4 text-3xl font-semibold text-stone-900">Create a user</h1>
                <p class="mt-2 text-sm text-stone-500">Fill in the essentials to create a new profile.</p>

                <form method="POST" action="/users" class="mt-8 space-y-6">
                    <div class="rounded-2xl border border-stone-200 bg-white px-6 py-5">
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400" for="name">Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            placeholder="Enter user name"
                            class="mt-3 w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-700 shadow-sm focus:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-100"
                            value="{{ old('name') }}"
                            required
                        />
                        <p class="mt-2 text-xs text-rose-600">@errors('name')</p>
                    </div>

                    <div class="rounded-2xl border border-stone-200 bg-white px-6 py-5">
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400" for="email">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="hello@spacio.dev"
                            class="mt-3 w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-700 shadow-sm focus:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-100"
                            value="{{ old('email') }}"
                        />
                        <p class="mt-2 text-xs text-rose-600">@errors('email')</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                        <a href="/" class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-white px-5 py-2 text-sm font-semibold text-stone-700 shadow-sm transition hover:-translate-y-0.5 hover:border-stone-300 hover:text-stone-900">Cancel</a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-6 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-400">Create user</button>
                    </div>
                </form>
            </div> -->
            <!-- Component a la Livewire. -->
            @component('users.create-form')
        </div>
    @endsection
</template>
