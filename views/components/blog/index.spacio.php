<template>
    <div class="rounded-3xl border border-amber-100 bg-white/90 p-8 shadow-[0_24px_70px_rgba(15,23,42,0.12)]">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Blog</div>
                <h1 class="mt-4 text-3xl font-semibold text-stone-900">Posts</h1>
                <p class="mt-2 text-sm text-stone-500">Create posts without leaving the page.</p>
            </div>
            <button data-spacio-action="refresh" class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-white px-5 py-2 text-sm font-semibold text-stone-700 shadow-sm transition hover:-translate-y-0.5 hover:border-stone-300 hover:text-stone-900">Refresh</button>
        </div>

        <form method="POST" action="/_spacio/component" data-spacio-action="save" class="mt-8 grid gap-4 sm:grid-cols-2" data-spacio-island="form">
            <div class="rounded-2xl border border-stone-200 bg-white px-5 py-4">
                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400" for="title">Title</label>
                <input id="title" name="title" type="text" value="{{ title }}" data-spacio-slug-source data-spacio-action="preview" data-spacio-debounce="400" data-spacio-target="form" class="mt-3 w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-700 shadow-sm focus:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-100" />
                @if ($errors['title'] ?? null)
                    <p class="mt-2 text-xs text-rose-600">{{ errors.title[0] }}</p>
                @endif
            </div>
            <div class="rounded-2xl border border-stone-200 bg-white px-5 py-4">
                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400" for="slug">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ slug }}" data-spacio-slug-target data-spacio-action="preview" data-spacio-debounce="400" data-spacio-target="form" class="mt-3 w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-700 shadow-sm focus:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-100" />
                @if ($errors['slug'] ?? null)
                    <p class="mt-2 text-xs text-rose-600">{{ errors.slug[0] }}</p>
                @endif
            </div>
            <div class="rounded-2xl border border-stone-200 bg-white px-5 py-4 sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400" for="body">Body</label>
                <textarea id="body" name="body" rows="4" class="mt-3 w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-700 shadow-sm focus:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-100">{{ body }}</textarea>
            </div>
            <div class="sm:col-span-2 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-6 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-400">Publish</button>
            </div>
        </form>

        <div class="mt-10 space-y-4">
            @foreach ($posts as $post)
                <div class="rounded-2xl border border-stone-200 bg-white px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">{{ post.slug }}</p>
                    <h2 class="mt-2 text-xl font-semibold text-stone-900">{{ post.title }}</h2>
                    @if ($post['body'] ?? null)
                        <p class="mt-2 text-sm text-stone-600">{{ post.body }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</template>
<script>
(() => {
  const root = document.currentScript?.closest('[data-spacio-component]')
  if (!root) return

  const slugify = (value) =>
    value
      .toString()
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)+/g, '')

  const source = root.querySelector('[data-spacio-slug-source]')
  const target = root.querySelector('[data-spacio-slug-target]')
  if (!source || !target) return

  target.addEventListener('input', () => {
    target.dataset.autofill = 'false'
  })

  source.addEventListener('input', () => {
    const canAutofill = target.value === '' || target.dataset.autofill === 'true'
    if (!canAutofill) return

    target.value = slugify(source.value)
    target.dataset.autofill = 'true'
  })
})()
</script>
