(() => {
  const endpoint = '/_spacio/component'
  const debounceTimers = new Map()

  const runScripts = (root) => {
    const scripts = root.querySelectorAll('script')
    scripts.forEach((script) => {
      const replacement = document.createElement('script')
      if (script.type) replacement.type = script.type
      if (script.src) {
        replacement.src = script.src
        replacement.defer = script.defer
        replacement.async = script.async
      } else {
        replacement.textContent = script.textContent
      }
      script.replaceWith(replacement)
    })
  }

  const replaceComponentHtml = (component, html, target) => {
    if (!target) {
      component.outerHTML = html
      return
    }

    const parser = new DOMParser()
    const doc = parser.parseFromString(html, 'text/html')
    const incoming = doc.querySelector('[data-spacio-component]')
    if (!incoming) return

    const nextIsland = incoming.querySelector(`[data-spacio-island="${target}"]`)
    const currentIsland = component.querySelector(`[data-spacio-island="${target}"]`)
    if (!nextIsland || !currentIsland) return

    currentIsland.replaceWith(nextIsland)
    runScripts(nextIsland)
  }

  const triggerAction = (el) => {
    const debounce = parseInt(el.getAttribute('data-spacio-debounce') || '0', 10)

    if (debounce > 0) {
      const existing = debounceTimers.get(el)
      if (existing) {
        clearTimeout(existing)
      }

      const timer = setTimeout(() => handleAction(el), debounce)
      debounceTimers.set(el, timer)
      return
    }

    handleAction(el)
  }


  const showErrorModal = (html) => {
    let overlay = document.getElementById('spacio-error-overlay')
    if (!overlay) {
      overlay = document.createElement('div')
      overlay.id = 'spacio-error-overlay'
      overlay.innerHTML = `
        <div class="spacio-error-backdrop"></div>
        <div class="spacio-error-modal">
          <button class="spacio-error-close" type="button">Close</button>
          <div class="spacio-error-content"></div>
        </div>
      `
      document.body.appendChild(overlay)

      const style = document.createElement('style')
      style.textContent = `
        #spacio-error-overlay { position: fixed; inset: 0; z-index: 9999; display: grid; place-items: center; }
        #spacio-error-overlay .spacio-error-backdrop { position: absolute; inset: 0; background: transparent; }
        #spacio-error-overlay .spacio-error-modal { position: relative; width: min(960px, 92vw); max-height: 88vh; overflow: auto; background: #fff; border-radius: 18px; box-shadow: 0 30px 80px rgba(15, 23, 42, 0.35); }
        #spacio-error-overlay .spacio-error-close { position: sticky; top: 0; width: 100%; text-align: right; padding: 12px 18px; border: none; background: #f8fafc; font-weight: 600; cursor: pointer; }
        #spacio-error-overlay .spacio-error-content { padding: 0; }
      `
      overlay.appendChild(style)

      overlay.addEventListener('click', (event) => {
        if (event.target.classList.contains('spacio-error-backdrop')) {
          overlay.remove()
        }
      })

      overlay.querySelector('.spacio-error-close').addEventListener('click', () => {
        overlay.remove()
      })
    }

    const content = overlay.querySelector('.spacio-error-content')
    content.innerHTML = html
  }

  const findComponent = (el) => el.closest('[data-spacio-component]')

  const handleAction = async (el) => {
    const component = findComponent(el)
    if (!component) return

    const action = el.getAttribute('data-spacio-action')
    if (!action) return

    const props = component.getAttribute('data-spacio-props') || '{}'
    const form = el.closest('form') || component.querySelector('form')
    const data = form ? new FormData(form) : new FormData()

    data.set('spacio_component', component.getAttribute('data-spacio-component'))
    data.set('spacio_action', action)
    data.set('spacio_props', props)

    const response = await fetch(endpoint, {
      method: 'POST',
      body: data,
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })

    if (!response.ok) {
      const html = await response.text()
      if (html) {
        showErrorModal(html)
      }
      return
    }

    const redirect = response.headers.get('X-Spacio-Redirect')
    if (redirect) {
      window.location.href = redirect
      return
    }

    const html = await response.text()
    const target = el.getAttribute('data-spacio-target')
    replaceComponentHtml(component, html, target)
  }

  document.addEventListener('click', (event) => {
    const target = event.target.closest('[data-spacio-action]')
    if (!target) return

    if (target.tagName === 'FORM') {
      return
    }

    event.preventDefault()
    triggerAction(target)
  })


  document.addEventListener('submit', (event) => {
    const form = event.target.closest('form')
    if (!form) return

    const action = form.getAttribute('data-spacio-action')
    if (!action) return

    event.preventDefault()
    triggerAction(form)
  })

  document.addEventListener('input', (event) => {
    const target = event.target.closest('[data-spacio-action]')
    if (!target) return

    triggerAction(target)
  })
})()
