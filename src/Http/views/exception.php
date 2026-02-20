<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 via-stone-50 to-orange-100 text-stone-900">
  <div class="mx-auto flex min-h-screen w-full max-w-5xl flex-col justify-center px-6 py-12">
    <div class="rounded-3xl border border-amber-100 bg-white/90 shadow-[0_25px_80px_rgba(15,23,42,0.12)] backdrop-blur">
      <div class="border-b border-amber-100 px-8 py-6">
        <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">
          Spacio
        </div>
        <h1 class="mt-4 text-2xl font-semibold text-stone-900 sm:text-3xl">
          <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="mt-2 text-sm text-stone-500">
          <?= htmlspecialchars($method, ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>
      <div class="px-8 py-6">
        <p class="text-base text-stone-700">
          <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </p>

        <?php if ($debug) { ?>
          <div class="mt-6 rounded-2xl border border-stone-200 bg-stone-950 text-stone-100 shadow-inner">
            <div class="flex items-center justify-between border-b border-stone-800 px-4 py-3">
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-300">Stack Trace</p>
              <button
                type="button"
                class="rounded-full bg-amber-500 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow hover:bg-amber-400"
                data-copy-target="#exception-markdown"
              >
                Copy Markdown
              </button>
            </div>
            <div class="px-4 py-3 text-xs text-stone-400">
              <?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <pre class="max-h-[320px] overflow-auto px-4 pb-4 text-xs leading-5 text-stone-200"><?= htmlspecialchars($trace, ENT_QUOTES, 'UTF-8') ?></pre>
          </div>

          <textarea id="exception-markdown" class="sr-only" readonly><?= htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8') ?></textarea>
        <?php } ?>
      </div>
    </div>
  </div>

  <?php if ($debug) { ?>
  <script>
    const copyButtons = document.querySelectorAll('[data-copy-target]');
    copyButtons.forEach((button) => {
      button.addEventListener('click', async () => {
        const target = document.querySelector(button.getAttribute('data-copy-target'));
        if (!target) return;

        const text = target.value || target.textContent || '';
        try {
          await navigator.clipboard.writeText(text);
          button.textContent = 'Copied';
          setTimeout(() => {
            button.textContent = 'Copy Markdown';
          }, 1200);
        } catch (error) {
          button.textContent = 'Copy failed';
        }
      });
    });
  </script>
  <?php } ?>
</body>
</html>
