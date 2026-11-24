/**
 * Rafi Portfolio Core — Certificate Lightbox Modal & Credential Preview (v2.0.0)
 * Handles interactive certificate modal, document frame zoom, copy credential code, and keyboard accessibility.
 */
document.addEventListener('DOMContentLoaded', function () {
	var modal = document.getElementById('rf-cert-modal');
	if (!modal) return;

	var modalImg = document.getElementById('rf-modal-img');
	var modalTitle = document.getElementById('rf-modal-title');
	var modalIssuer = document.getElementById('rf-modal-issuer');
	var modalDate = document.getElementById('rf-modal-date');
	var modalCode = document.getElementById('rf-modal-code');
	var modalDesc = document.getElementById('rf-modal-desc');
	var modalFullLink = document.getElementById('rf-modal-full-link');
	var copyBtn = document.getElementById('rf-modal-copy-btn');
	var closeButtons = modal.querySelectorAll('.rf-cert-modal-close, .rf-modal-close-btn');

	var lastActiveElement = null;

	function openModal(card) {
		lastActiveElement = card;

		var title = card.getAttribute('data-cert-title') || 'Sertifikat Kompetensi';
		var issuer = card.getAttribute('data-cert-issuer') || 'Penerbit';
		var date = card.getAttribute('data-cert-date') || '';
		var code = card.getAttribute('data-cert-code') || 'Kredensial Terverifikasi';
		var desc = card.getAttribute('data-cert-desc') || '';
		var img = card.getAttribute('data-cert-img') || '';
		var verify = card.getAttribute('data-cert-verify') || '';

		if (modalTitle) modalTitle.textContent = title;
		if (modalIssuer) modalIssuer.textContent = issuer;
		if (modalDate) {
			modalDate.textContent = date;
			modalDate.style.display = date ? 'inline-flex' : 'none';
		}
		if (modalCode) modalCode.textContent = code;
		if (modalDesc) {
			modalDesc.textContent = desc;
			modalDesc.style.display = desc ? 'block' : 'none';
		}
		if (modalImg) {
			modalImg.src = img;
			modalImg.alt = 'Dokumen Sertifikat: ' + title;
		}
		if (modalFullLink) {
			modalFullLink.href = img || verify || '#';
		}

		// Reset copy button state
		if (copyBtn) {
			var copyText = copyBtn.querySelector('.rf-copy-text');
			if (copyText) copyText.textContent = 'Salin';
			copyBtn.classList.remove('is-copied');
		}

		modal.classList.add('is-open');
		modal.setAttribute('aria-hidden', 'false');
		document.documentElement.style.overflow = 'hidden';
		document.body.style.overflow = 'hidden';

		// Focus on close button
		var primaryClose = modal.querySelector('.rf-cert-modal-close');
		if (primaryClose) {
			primaryClose.focus();
		}
	}

	function closeModal() {
		modal.classList.remove('is-open');
		modal.setAttribute('aria-hidden', 'true');
		document.documentElement.style.overflow = '';
		document.body.style.overflow = '';

		if (lastActiveElement && typeof lastActiveElement.focus === 'function') {
			lastActiveElement.focus();
		}
	}

	// Attach click and Enter key events to all cert tiles
	function bindCertTiles() {
		var tiles = document.querySelectorAll('.rf-cert-tile');
		tiles.forEach(function (tile) {
			// Ensure tile is keyboard navigable
			if (!tile.getAttribute('tabindex')) {
				tile.setAttribute('tabindex', '0');
			}
			tile.style.cursor = 'pointer';

			tile.addEventListener('click', function (e) {
				// Don't trigger if clicking an external link inside tile (if any)
				if (e.target.closest('a') && !e.target.closest('.rf-cert-tile-trigger')) {
					return;
				}
				e.preventDefault();
				openModal(tile);
			});

			tile.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					openModal(tile);
				}
			});
		});
	}

	bindCertTiles();

	// Close button events
	closeButtons.forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			closeModal();
		});
	});

	// Close on clicking backdrop outside card
	modal.addEventListener('click', function (e) {
		if (e.target === modal) {
			closeModal();
		}
	});

	// Close on Escape key
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && modal.classList.contains('is-open')) {
			closeModal();
		}
	});

	// Copy credential code to clipboard
	if (copyBtn && modalCode) {
		copyBtn.addEventListener('click', function (e) {
			e.preventDefault();
			var textToCopy = modalCode.textContent.trim();
			if (!textToCopy) return;

			var copyText = copyBtn.querySelector('.rf-copy-text');

			function onCopySuccess() {
				if (copyText) copyText.textContent = 'Tersalin!';
				copyBtn.classList.add('is-copied');
				setTimeout(function () {
					if (copyText) copyText.textContent = 'Salin';
					copyBtn.classList.remove('is-copied');
				}, 2200);
			}

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(textToCopy).then(onCopySuccess).catch(function () {
					fallbackCopy(textToCopy, onCopySuccess);
				});
			} else {
				fallbackCopy(textToCopy, onCopySuccess);
			}
		});
	}

	function fallbackCopy(text, callback) {
		var textArea = document.createElement('textarea');
		textArea.value = text;
		textArea.style.position = 'fixed';
		textArea.style.left = '-9999px';
		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();
		try {
			document.execCommand('copy');
			if (callback) callback();
		} catch (err) {}
		document.body.removeChild(textArea);
	}
});
