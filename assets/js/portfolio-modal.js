/**
 * Rafi Portfolio Core — Native Same-Page Preview Dialog (Projects & Certificates)
 * Supports optional Page 1 / Page 2 toggle (e.g. BNSP certificate + 9 Units of Competency).
 */
document.addEventListener('DOMContentLoaded', function () {
	var previewDialog = document.getElementById('rf-preview-dialog');
	if (!previewDialog) return;

	var pImg = document.getElementById('rf-preview-dialog-img');
	var pTitle = document.getElementById('rf-preview-dialog-title');
	var pBadge = document.getElementById('rf-preview-dialog-badge');
	var pClose = document.getElementById('rf-preview-dialog-close');
	var pPages = document.getElementById('rf-preview-dialog-pages');
	var pPage1Btn = document.getElementById('rf-preview-page-1');
	var pPage2Btn = document.getElementById('rf-preview-page-2');

	var activeMainImg = '';
	var activeAltImg = '';
	var activeTitle = '';

	function setActivePage(page) {
		if (!pImg) return;
		var isSecond = page === 2 && Boolean(activeAltImg);
		pImg.src = isSecond ? activeAltImg : activeMainImg;
		pImg.alt = activeTitle
			? 'Pratinjau (' + (isSecond ? 'Halaman 2' : 'Halaman 1') + '): ' + activeTitle
			: 'Pratinjau visual';
		if (pPage1Btn) {
			pPage1Btn.classList.toggle('is-active', !isSecond);
			pPage1Btn.setAttribute('aria-pressed', String(!isSecond));
		}
		if (pPage2Btn) {
			pPage2Btn.classList.toggle('is-active', isSecond);
			pPage2Btn.setAttribute('aria-pressed', String(isSecond));
		}
	}

	function showPreviewDialog(detail) {
		if (!detail || !detail.img) return;
		activeMainImg = detail.img;
		activeAltImg = detail.imgAlt || '';
		activeTitle = detail.title || 'Pratinjau';

		if (pTitle) pTitle.textContent = activeTitle;
		if (pBadge) pBadge.textContent = detail.badge || 'PRATINJAU';

		if (pPages) {
			if (activeAltImg) {
				pPages.hidden = false;
				if (pPage2Btn && detail.altLabel) {
					pPage2Btn.textContent = detail.altLabel;
				}
			} else {
				pPages.hidden = true;
			}
		}

		setActivePage(1);

		if (!previewDialog.open) {
			previewDialog.showModal();
		}
	}

	if (pPage1Btn) {
		pPage1Btn.addEventListener('click', function () {
			setActivePage(1);
		});
	}
	if (pPage2Btn) {
		pPage2Btn.addEventListener('click', function () {
			setActivePage(2);
		});
	}

	document.addEventListener('rf:openPreview', function (e) {
		showPreviewDialog(e.detail);
	});

	document.querySelectorAll('.rf-preview-trigger').forEach(function (el) {
		el.addEventListener('click', function (e) {
			if (el.getAttribute('href') === '#rf-works') {
				var target = document.getElementById('rf-works');
				if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}
			e.preventDefault();
			showPreviewDialog({
				img: el.getAttribute('data-preview-img'),
				imgAlt: el.getAttribute('data-preview-img-alt'),
				altLabel: el.getAttribute('data-preview-alt-label'),
				title: el.getAttribute('data-preview-title'),
				badge: el.getAttribute('data-preview-badge')
			});
		});
	});

	if (pClose) {
		pClose.addEventListener('click', function () {
			previewDialog.close();
		});
	}

	if (!('closedBy' in HTMLDialogElement.prototype)) {
		previewDialog.addEventListener('click', function (event) {
			if (event.target !== previewDialog) return;
			var rect = previewDialog.getBoundingClientRect();
			var isInside = (
				rect.top <= event.clientY &&
				event.clientY <= rect.top + rect.height &&
				rect.left <= event.clientX &&
				event.clientX <= rect.left + rect.width
			);
			if (!isInside) previewDialog.close();
		});
	}
});
