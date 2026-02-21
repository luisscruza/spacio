(() => {
  const endpoint = '/_spacio/component'

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
      return
    }

    const redirect = response.headers.get('X-Spacio-Redirect')
    if (redirect) {
      window.location.href = redirect
      return
    }

    const html = await response.text()
    component.outerHTML = html
  }

  document.addEventListener('click', (event) => {
    const target = event.target.closest('[data-spacio-action]')
    if (!target) return

    if (target.tagName === 'FORM') {
      return
    }

    event.preventDefault()
    handleAction(target)
  })

  document.addEventListener('submit', (event) => {
    const form = event.target.closest('form')
    if (!form) return

    const action = form.getAttribute('data-spacio-action')
    if (!action) return

    event.preventDefault()
    handleAction(form)
  })
})()
