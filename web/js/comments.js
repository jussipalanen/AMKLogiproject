/*
 * Comment wall: sends and deletes comments without reloading the page (the original
 * app used jQuery $.ajax). Enter sends, Shift+Enter adds a line break. Without
 * JavaScript both forms still work as normal POSTs.
 */
(() => {
    'use strict'

    const jsonHeaders = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }

    // The comment list scrolls inside its box: show the first new comment, or the latest one.
    const scrollComments = list => {
        const ul = list?.querySelector('.comment-list')
        if (!ul) return
        const firstNew = ul.querySelector('.comment-new')
        ul.scrollTop = firstNew ? firstNew.offsetTop : ul.scrollHeight
    }

    const showStatus = (element, message) => {
        if (!element) return
        element.textContent = message
        element.hidden = message === ''
    }

    const postForm = async (form, body) => {
        const response = await fetch(form.action, { method: 'POST', body, headers: jsonHeaders })
        const data = await response.json().catch(() => ({}))

        if (!response.ok) {
            const errors = data.errors ? Object.values(data.errors) : []
            throw new Error(errors[0] || data.message || 'Pyyntö epäonnistui.')
        }

        return data
    }

    const initCommentForm = (form, list) => {
        const status = form.querySelector('[data-comment-status]')
        const button = form.querySelector('button[type="submit"]')
        const textarea = form.querySelector('textarea')
        let sending = false

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
                showStatus(status, 'Kommentti ei voi olla tyhjä.')
                textarea.focus()
                return
            }

            sending = true
            button.disabled = true
            showStatus(status, 'Lähetetään kommenttia…')

            try {
                const data = await postForm(form, new FormData(form))
                list.innerHTML = data.html
                form.reset()
                showStatus(status, '')
                list.dispatchEvent(new Event('comments:updated'))
                scrollComments(list)
            } catch (error) {
                showStatus(status, error.message === 'Pyyntö epäonnistui.' ? 'Kommentin lähetys epäonnistui.' : error.message)
            } finally {
                sending = false
                button.disabled = false
                textarea.focus()
            }
        })
    }

    const initDeleteForm = (form, list) => {
        const status = document.querySelector('[data-delete-status]')
        const checkboxes = () => [...list.querySelectorAll('.amk-comment-select')]
        let sending = false

        // Keep "Valitse kaikki", the selected count and "Poista valitut" in sync.
        const updateSelection = () => {
            const boxes = checkboxes()
            const checked = boxes.filter(box => box.checked).length
            const selectAll = list.querySelector('[data-select-all]')
            const deleteSelected = list.querySelector('[data-delete-selected]')
            const counter = list.querySelector('[data-selected-count]')

            if (selectAll) {
                selectAll.checked = boxes.length > 0 && checked === boxes.length
                selectAll.indeterminate = checked > 0 && checked < boxes.length
            }
            if (deleteSelected) deleteSelected.disabled = checked === 0
            if (counter) counter.textContent = checked > 0 ? `(${checked})` : ''
            list.querySelectorAll('.comment').forEach(item => {
                item.classList.toggle('comment-selected', item.querySelector('.amk-comment-select')?.checked === true)
            })
        }

        list.addEventListener('change', event => {
            if (event.target.matches('[data-select-all]')) {
                checkboxes().forEach(box => { box.checked = event.target.checked })
            }
            if (event.target.matches('[data-select-all], .amk-comment-select')) {
                updateSelection()
            }
        })
        list.addEventListener('comments:updated', updateSelection)
        updateSelection()

        form.addEventListener('submit', async event => {
            event.preventDefault()
            if (sending) return

            const submitter = event.submitter
            let message = submitter?.dataset.confirmMessage

            if (submitter?.hasAttribute('data-delete-selected')) {
                const count = checkboxes().filter(box => box.checked).length
                if (count === 0) return
                message = count === 1 ? 'Poistetaanko valittu kommentti?' : `Poistetaanko ${count} valittua kommenttia?`
            }

            if (message && !window.confirm(message)) return

            // Checked ids[] boxes belong to the form; add the clicked button's own name/value.
            const body = new FormData(form)
            if (submitter?.name) body.append(submitter.name, submitter.value)

            sending = true
            showStatus(status, 'Poistetaan…')

            try {
                const data = await postForm(form, body)
                list.innerHTML = data.html
                updateSelection()
                showStatus(status, data.message || '')
            } catch (error) {
                showStatus(status, error.message === 'Pyyntö epäonnistui.' ? 'Poistaminen epäonnistui.' : error.message)
            } finally {
                sending = false
            }
        })
    }

    document.addEventListener('DOMContentLoaded', () => {
        const list = document.querySelector('[data-comment-list]')
        if (!list) return

        scrollComments(list)

        const commentForm = document.querySelector('[data-comment-form]')
        if (commentForm) initCommentForm(commentForm, list)

        const deleteForm = document.querySelector('[data-comment-delete-form]')
        if (deleteForm) initDeleteForm(deleteForm, list)
    })
})()
