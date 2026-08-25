/**
 * Progressive enhancement for the dashboard.
 *
 * Live updates re-fetch the page the reader is already on and swap the regions
 * marked [data-live]; the server stays the only place that knows how a run is
 * rendered, so there is no second copy of the markup to keep in sync.
 */
(function () {
    'use strict';

    var POLL_MS = 15000;

    document.addEventListener('click', function (event) {
        var copyButton = event.target.closest('.copy');
        if (copyButton) {
            copyRunId(copyButton);
            return;
        }

        var shot = event.target.closest('a[data-lightbox]');
        if (shot && typeof HTMLDialogElement === 'function') {
            event.preventDefault();
            openLightbox(shot);
            return;
        }

        var tab = event.target.closest('.tab');
        if (tab) {
            selectTab(tab);
        }
    });

    function copyRunId(button) {
        var value = button.getAttribute('data-copy') || '';
        if (!navigator.clipboard) {
            return;
        }

        navigator.clipboard.writeText(value).then(function () {
            var original = button.textContent;
            button.textContent = 'Copied';
            window.setTimeout(function () {
                button.textContent = original;
            }, 1400);
        });
    }

    function selectTab(tab) {
        var container = tab.closest('[data-tabs]');
        if (!container) {
            return;
        }

        Array.prototype.forEach.call(container.querySelectorAll('.tab'), function (candidate) {
            var isCurrent = candidate === tab;
            candidate.classList.toggle('is-current', isCurrent);
            candidate.setAttribute('aria-selected', isCurrent ? 'true' : 'false');

            var panel = document.getElementById(candidate.getAttribute('aria-controls'));
            if (panel) {
                panel.hidden = !isCurrent;
            }
        });
    }

    function lightbox() {
        var dialog = document.getElementById('hfcd-lightbox');
        if (dialog) {
            return dialog;
        }

        dialog = document.createElement('dialog');
        dialog.id = 'hfcd-lightbox';
        dialog.className = 'lightbox';
        dialog.innerHTML =
            '<button type="button" class="lightbox-close" aria-label="Close">×</button>' +
            '<img alt=""><figcaption></figcaption>';
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog || event.target.classList.contains('lightbox-close')) {
                dialog.close();
            }
        });
        document.body.appendChild(dialog);

        return dialog;
    }

    function openLightbox(link) {
        var dialog = lightbox();
        var caption = link.getAttribute('data-caption') || '';

        dialog.querySelector('img').src = link.getAttribute('href');
        dialog.querySelector('img').alt = caption;
        dialog.querySelector('figcaption').textContent = caption;
        dialog.showModal();
    }

    /* ---------------------------------------------------------------- live */

    function hasLiveRun() {
        return document.querySelector('.pulse') !== null;
    }

    function isRunPage() {
        return document.querySelector('[data-live="workflow"]') !== null;
    }

    function refresh() {
        return fetch(window.location.href, { cache: 'no-store', credentials: 'same-origin' })
            .then(function (response) {
                return response.ok ? response.text() : null;
            })
            .then(function (html) {
                if (!html) {
                    return;
                }

                var fresh = new DOMParser().parseFromString(html, 'text/html');
                Array.prototype.forEach.call(document.querySelectorAll('[data-live]'), function (region) {
                    // Never clobber a disclosure the reader has opened.
                    if (region.querySelector('details[open]')) {
                        return;
                    }

                    var replacement = fresh.querySelector('[data-live="' + region.dataset.live + '"]');
                    if (replacement) {
                        region.innerHTML = replacement.innerHTML;
                    }
                });
            })
            .catch(function () {
                /* offline or restarting: the next tick tries again */
            });
    }

    function tick() {
        if (document.hidden) {
            return;
        }
        // A finished run never changes again; the list can always gain a new run.
        if (isRunPage() && !hasLiveRun()) {
            return;
        }

        refresh();
    }

    if (document.querySelector('[data-live]')) {
        window.setInterval(tick, POLL_MS);
    }
}());
