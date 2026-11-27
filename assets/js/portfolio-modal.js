/**
 * Rafi Portfolio Core — Native Same-Page Preview Dialog (Projects, Certificates & CV)
 * Supports multi-slide switcher (data-preview-gallery or Page 1/2 alt) and direct file download.
 */
document.addEventListener('DOMContentLoaded', function () {
	var previewDialog = document.getElementById('rf-preview-dialog');
	if (!previewDialog) return;

	var pImg = document.getElementById('rf-preview-dialog-img');
	var pTitle = document.getElementById('rf-preview-dialog-title');
	var pBadge = document.getElementById('rf-preview-dialog-badge');
	var pClose = document.getElementById('rf-preview-dialog-close');
	var pDownload = document.getElementById('rf-preview-dialog-download');
	var pPages = document.getElementById('rf-preview-dialog-pages');

	var activeSlides = [];
	var activeTitle = '';

	function buildSlides(detail) {
		var slides = [];
		if (detail.gallery) {
			detail.gallery.split(';').forEach(function (entry) {
				var sep = entry.indexOf('|');
				if (sep !== -1) {
					var label = entry.slice(0, sep).trim();
					var url = entry.slice(sep + 1).trim();
					if (label && url) slides.push({ label: label, img: url });
				}
			});
		}
		if (slides.length === 0) {
			slides.push({ label: 'Halaman 1 · Sertifikat', img: detail.img });
			if (detail.imgAlt) {
				slides.push({
					label: detail.altLabel || 'Halaman 2 · Daftar Unit',
					img: detail.imgAlt
				});
			}
		}
		return slides;
	}

	function setActiveSlide(index) {
		if (!pImg || !activeSlides[index]) return;
		var slide = activeSlides[index];
		pImg.src = slide.img;
		pImg.alt = activeTitle
			? 'Pratinjau (' + slide.label + '): ' + activeTitle
			: 'Pratinjau visual';

		if (pPages) {
			var buttons = pPages.querySelectorAll('.rf-preview-page-btn');
			buttons.forEach(function (btn, idx) {
				var active = idx === index;
				btn.classList.toggle('is-active', active);
				btn.setAttribute('aria-pressed', String(active));
			});
		}
	}

	function renderSlideTabs() {
		if (!pPages) return;
		pPages.innerHTML = '';
		if (activeSlides.length < 2) {
			pPages.hidden = true;
			return;
		}
		pPages.hidden = false;
		activeSlides.forEach(function (slide, idx) {
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'rf-preview-page-btn' + (idx === 0 ? ' is-active' : '');
			btn.setAttribute('aria-pressed', String(idx === 0));
			btn.textContent = slide.label;
			btn.addEventListener('click', function () {
				setActiveSlide(idx);
			});
			pPages.appendChild(btn);
		});
	}

	function showPreviewDialog(detail) {
		if (!detail || !detail.img) return;
		activeTitle = detail.title || 'Pratinjau';
		activeSlides = buildSlides(detail);

		if (pTitle) pTitle.textContent = activeTitle;
		if (pBadge) pBadge.textContent = detail.badge || 'PRATINJAU';

		if (pDownload) {
			if (detail.download) {
				pDownload.hidden = false;
				pDownload.href = detail.download;
				pDownload.setAttribute('download', detail.downloadName || '');
			} else {
				pDownload.hidden = true;
				pDownload.removeAttribute('href');
			}
		}

		renderSlideTabs();
		setActiveSlide(0);

		if (!previewDialog.open) {
			previewDialog.showModal();
		}
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
				gallery: el.getAttribute('data-preview-gallery'),
				imgAlt: el.getAttribute('data-preview-img-alt'),
				altLabel: el.getAttribute('data-preview-alt-label'),
				download: el.getAttribute('data-preview-download'),
				downloadName: el.getAttribute('data-preview-download-name'),
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
