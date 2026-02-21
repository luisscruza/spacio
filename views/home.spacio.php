<template>
    @extends('layouts.app')

    @section('title')
        Users
    @endsection

    @section('content')
        <div class="mx-auto w-full max-w-4xl px-6 py-12">
            <div class="rounded-3xl border border-amber-100 bg-white/90 p-8 shadow-[0_24px_70px_rgba(15,23,42,0.12)]">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Directory</div>
                        <h1 class="mt-4 text-3xl font-semibold text-stone-900">Users</h1>
                        <p class="mt-2 text-sm text-stone-500">Browse, manage, and jump into a profile.</p>
                    </div>
                    <a href="/users/create" class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-white px-5 py-2 text-sm font-semibold text-stone-700 shadow-sm transition hover:-translate-y-0.5 hover:border-stone-300 hover:text-stone-900">Create new user</a>
                </div>

                <div class="mt-8 space-y-4">
                    @foreach ($users as $user)
                        <a href="/users/{{ user.id }}" class="flex items-center justify-between rounded-2xl border border-stone-200 bg-white px-6 py-4 transition hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">User</p>
                                <p class="mt-2 text-lg font-semibold text-stone-800">{{ user.name }}</p>
                            </div>
                            <span class="rounded-full border border-amber-200 bg-amber-50 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">View</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endsection
</template>
