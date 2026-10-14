/*
 * Sends wall comments without reloading the page (the original app did this with
 * jQuery $.ajax). Enter sends, Shift+Enter adds a line break. Without JavaScript
 * the form still works as a normal POST.
 */
(() => {
    'use strict'

    // The comment list scrolls inside its box: show the first new comment, or the latest one.
    const scrollComments = list => {
        const ul = list?.querySelector('.comment-list')
        if (!ul) return
        const firstNew = ul.querySelector('.comment-new')
        ul.scrollTop = firstNew ? firstNew.offsetTop : ul.scrollHeight
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-comment-form]')
        const list = document.querySelector('[data-comment-list]')
        scrollComments(list)
        if (!form || !list) return

        const status = form.querySelector('[data-comment-status]')
        const button = form.querySelector('button[type="submit"]')
        const textarea = form.querySelector('textarea')
        let sending = false

        const showStatus = message => {
            status.textContent = message
            status.hidden = message === ''
        }

        textarea.addEventListener('keydown', event => {
            // isComposing: don't send while an IME is still composing a character
            if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
                event.preventDefault()
                form.requestSubmit()
            }
        })

        form.addEventListener('submit', async event => {
            event.preventDefault()
            if (sending) return

            if (textarea.value.trim() === '') {
                showStatus('Kommentti ei voi olla tyhjä.')
                textarea.focus()
                return
            }

            sending = true
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
                scrollComments(list)
            } catch (error) {
                showStatus(error.message)
            } finally {
                sending = false
                button.disabled = false
                textarea.focus()
            }
        })
    })
})()
