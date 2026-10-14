/*
 * Sends wall comments without reloading the page (the original app did this with
 * jQuery $.ajax). Without JavaScript the form still works as a normal POST.
 */
(() => {
    'use strict'

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-comment-form]')
        const list = document.querySelector('[data-comment-list]')
        if (!form || !list) return

        const status = form.querySelector('[data-comment-status]')
        const button = form.querySelector('button[type="submit"]')
        const textarea = form.querySelector('textarea')

        const showStatus = message => {
            status.textContent = message
            status.hidden = message === ''
        }

        form.addEventListener('submit', async event => {
            event.preventDefault()
            button.disabled = true
            showStatus('Lähetetään kommenttia…')

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                const data = await response.json().catch(() => ({}))

                if (!response.ok) {
                    const errors = data.errors ? Object.values(data.errors) : []
                    throw new Error(errors[0] || data.message || 'Kommentin lähetys epäonnistui.')
                }

                list.innerHTML = data.html
                form.reset()
                showStatus('')
                list.lastElementChild?.scrollIntoView({ block: 'nearest', behavior: 'smooth' })
            } catch (error) {
                showStatus(error.message)
            } finally {
                button.disabled = false
                textarea.focus()
            }
        })
    })
})()
