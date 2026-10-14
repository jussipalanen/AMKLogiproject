/*
 * Albums: selecting images for moving/deleting, confirmations, and checking the chosen
 * files before upload. Everything also works without JavaScript; the server validates again.
 */
(() => {
    'use strict'

    const formatSize = bytes => `${(bytes / 1024 / 1024).toFixed(1).replace('.', ',')} Mt`

    // Image grid: "Valitse kaikki", selected count and buttons that need a selection.
    const initBulkForm = form => {
        const boxes = () => [...form.querySelectorAll('.amk-image-select')]
        const selectAll = form.querySelector('[data-select-all]')
        const counter = form.querySelector('[data-selected-count]')

        const update = () => {
            const all = boxes()
            const checked = all.filter(box => box.checked).length
            if (selectAll) {
                selectAll.checked = all.length > 0 && checked === all.length
                selectAll.indeterminate = checked > 0 && checked < all.length
            }
            if (counter) counter.textContent = checked > 0 ? `(${checked})` : ''
            form.querySelectorAll('[data-needs-selection]').forEach(button => { button.disabled = checked === 0 })
            all.forEach(box => box.closest('.amk-image-tile')?.classList.toggle('is-selected', box.checked))
        }

        form.addEventListener('change', event => {
            if (event.target === selectAll) {
                boxes().forEach(box => { box.checked = selectAll.checked })
            }
            update()
        })

        form.addEventListener('submit', event => {
            const message = event.submitter?.dataset.confirmMessage
            if (message && !window.confirm(message)) event.preventDefault()
        })

        update()
    }

    // Upload: list the chosen files and stop obviously too many / too large ones early.
    const initUploadForm = form => {
        const input = form.querySelector('input[type="file"]')
        const list = form.querySelector('[data-upload-list]')
        const status = form.querySelector('[data-upload-status]')
        const maxFiles = Number(form.dataset.maxFiles)
        const maxBytes = Number(form.dataset.maxSizeMb) * 1024 * 1024

        const problems = () => {
            const files = [...input.files]
            const errors = []
            if (files.length === 0) errors.push('Valitse ladattavat kuvat.')
            if (files.length > maxFiles) errors.push(`Valitse enintään ${maxFiles} kuvaa kerralla.`)
            if (files.some(file => file.size > maxBytes)) errors.push(`Kuva saa olla enintään ${form.dataset.maxSizeMb} Mt.`)
            return errors
        }

        input.addEventListener('change', () => {
            const files = [...input.files]
            list.replaceChildren(...files.map(file => {
                const item = document.createElement('li')
                item.textContent = `${file.name} (${formatSize(file.size)})`
                if (file.size > maxBytes) item.classList.add('is-invalid')
                return item
            }))
            list.hidden = files.length === 0
            status.textContent = problems().join(' ') || `${files.length} kuvaa valittu.`
        })

        form.addEventListener('submit', event => {
            const errors = problems()
            if (errors.length > 0) {
                event.preventDefault()
                status.textContent = errors.join(' ')
                return
            }
            status.textContent = 'Ladataan…'
            form.querySelector('[data-upload-submit]').disabled = true
        })
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-bulk-form]').forEach(initBulkForm)
        document.querySelectorAll('[data-upload-form]').forEach(initUploadForm)
        document.querySelectorAll('form[data-confirm-form]').forEach(form => {
            form.addEventListener('submit', event => {
                if (!window.confirm(form.dataset.confirmForm)) event.preventDefault()
            })
        })
    })
})()
