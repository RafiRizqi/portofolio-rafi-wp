/**
 * 3D Glassmorphism Carousel & Dual-View Motion Controller v2.4.0
 * Features:
 * 1. 3D Coverflow Carousel Stage (Swipe, Arrow buttons, Keyboard navigation, Focus depth)
 * 2. Taqy-Style Minimalist List with GPU Lerp Floating Cursor Image Preview
 * 3. Farline Monospace Indexing Integration
 * 4. Dual-View Segmented Control Switcher with localStorage state persistence
 *
 * @package Rafi_Portfolio_Core
 */

(function () {
	'use strict';

	function initShowcaseModules() {
		const showcases = document.querySelectorAll('[data-rf-showcase], [data-rf-carousel]');
		if (!showcases.length) return;

		showcases.forEach((wrap) => {
			// 1. CAROUSEL STAGE ELEMENTS
			const track = wrap.querySelector('.rf-carousel-track');
			const cards = Array.from(wrap.querySelectorAll('.rf-carousel-card'));
			const prevBtn = wrap.querySelector('.rf-btn-prev');
			const nextBtn = wrap.querySelector('.rf-btn-next');
			const dots = Array.from(wrap.querySelectorAll('.rf-dot'));
			const counterEl = wrap.querySelector('.rf-carousel-counter');
			const carouselStage = wrap.querySelector('.rf-carousel-stage');
			const carouselFooter = wrap.querySelector('.rf-showcase-footer');

			// 2. MINIMALIST LIST STAGE ELEMENTS (Taqy-Style)
			const listStage = wrap.querySelector('.rf-taqy-list-stage');
			const listRows = Array.from(wrap.querySelectorAll('.rf-taqy-row'));
			const floatingPreview = wrap.querySelector('.rf-floating-preview');
			const previewImg = wrap.querySelector('.rf-preview-img');
			const previewBadge = wrap.querySelector('.rf-preview-badge');

			// 3. DUAL-VIEW SWITCHER
			const viewBtns = Array.from(wrap.querySelectorAll('.rf-view-btn'));

			let currentIndex = 0;
			const total = cards.length;

			function formatNum(n) {
				return String(n).padStart(2, '0');
			}

			// CAROUSEL LOGIC
			function updateCarouselClasses() {
				if (!cards.length) return;
				const prevIndex = (currentIndex - 1 + total) % total;
				const nextIndex = (currentIndex + 1) % total;

				cards.forEach((card, i) => {
					card.classList.remove('is-active', 'is-prev', 'is-next', 'is-hidden');
					card.removeAttribute('tabindex');

					if (i === currentIndex) {
						card.classList.add('is-active');
						card.setAttribute('aria-hidden', 'false');
						card.querySelectorAll('a, button').forEach((el) => el.removeAttribute('tabindex'));
					} else if (i === prevIndex) {
						card.classList.add('is-prev');
						card.setAttribute('aria-hidden', 'true');
						card.querySelectorAll('a, button').forEach((el) => el.setAttribute('tabindex', '-1'));
					} else if (i === nextIndex) {
						card.classList.add('is-next');
						card.setAttribute('aria-hidden', 'true');
						card.querySelectorAll('a, button').forEach((el) => el.setAttribute('tabindex', '-1'));
					} else {
						card.classList.add('is-hidden');
						card.setAttribute('aria-hidden', 'true');
						card.querySelectorAll('a, button').forEach((el) => el.setAttribute('tabindex', '-1'));
					}
				});

				dots.forEach((dot, i) => {
					const active = i === currentIndex;
					dot.classList.toggle('is-active', active);
					dot.setAttribute('aria-selected', String(active));
				});

				if (counterEl) {
					counterEl.textContent = `${formatNum(currentIndex + 1)} / ${formatNum(total)}`;
				}
			}

			function goTo(index) {
				currentIndex = (index + total) % total;
				updateCarouselClasses();
			}

			function goNext() {
				goTo(currentIndex + 1);
			}

			function goPrev() {
				goTo(currentIndex - 1);
			}

			if (prevBtn) {
				prevBtn.addEventListener('click', (e) => {
					e.preventDefault();
					goPrev();
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', (e) => {
					e.preventDefault();
					goNext();
				});
			}

			dots.forEach((dot, i) => {
				dot.addEventListener('click', (e) => {
					e.preventDefault();
					goTo(i);
				});
			});

			cards.forEach((card) => {
				card.addEventListener('click', (e) => {
					if (card.classList.contains('is-prev')) {
						e.preventDefault();
						goPrev();
					} else if (card.classList.contains('is-next')) {
						e.preventDefault();
						goNext();
					} else if (card.classList.contains('is-active') && card.dataset.previewImg) {
						e.preventDefault();
						document.dispatchEvent(new CustomEvent('rf:openPreview', {
							detail: {
								img: card.dataset.previewImg,
								title: card.dataset.previewTitle || '',
								badge: card.dataset.previewBadge || 'PROYEK PILIHAN'
							}
						}));
					}
				});

				card.addEventListener('keydown', (e) => {
					if ((e.key === 'Enter' || e.key === ' ') && card.classList.contains('is-active') && card.dataset.previewImg) {
						e.preventDefault();
						document.dispatchEvent(new CustomEvent('rf:openPreview', {
							detail: {
								img: card.dataset.previewImg,
								title: card.dataset.previewTitle || '',
								badge: card.dataset.previewBadge || 'PROYEK PILIHAN'
							}
						}));
					}
				});
			});

			wrap.setAttribute('tabindex', '0');
			wrap.addEventListener('keydown', (e) => {
				// Only navigate carousel if carousel stage is visible
				if (carouselStage && carouselStage.style.display !== 'none') {
					if (e.key === 'ArrowLeft') {
						e.preventDefault();
						goPrev();
					} else if (e.key === 'ArrowRight') {
						e.preventDefault();
						goNext();
					}
				}
			});

			// Touch swipe for mobile carousel
			let touchStartX = 0;
			let touchEndX = 0;
			wrap.addEventListener(
				'touchstart',
				(e) => {
					touchStartX = e.changedTouches[0].screenX;
				},
				{ passive: true }
			);

			wrap.addEventListener(
				'touchend',
				(e) => {
					if (carouselStage && carouselStage.style.display !== 'none') {
						touchEndX = e.changedTouches[0].screenX;
						const diff = touchEndX - touchStartX;
						if (Math.abs(diff) > 40) {
							if (diff < 0) goNext();
							else goPrev();
						}
					}
				},
				{ passive: true }
			);

			updateCarouselClasses();

			// 4. DUAL-VIEW SWITCHER CONTROLLER
			function switchView(targetView) {
				viewBtns.forEach((btn) => {
					const isTarget = btn.getAttribute('data-view') === targetView;
					btn.classList.toggle('is-active', isTarget);
					btn.setAttribute('aria-selected', String(isTarget));
				});

				if (targetView === 'list') {
					if (carouselStage) carouselStage.style.display = 'none';
					if (carouselFooter) carouselFooter.style.display = 'none';
					if (listStage) {
						listStage.style.display = 'block';
						// Stagger list rows
						listRows.forEach((row, rIdx) => {
							row.style.animationDelay = `${rIdx * 50}ms`;
							row.classList.add('is-revealed');
						});
					}
				} else {
					if (listStage) listStage.style.display = 'none';
					if (carouselStage) {
						carouselStage.style.display = 'block';
						updateCarouselClasses();
					}
					if (carouselFooter) carouselFooter.style.display = 'flex';
				}

				try {
					localStorage.setItem('rf_showcase_view', targetView);
				} catch (err) {}
			}

			viewBtns.forEach((btn, bIdx) => {
				btn.addEventListener('click', () => {
					const view = btn.getAttribute('data-view') || 'carousel';
					switchView(view);
				});

				btn.addEventListener('keydown', (e) => {
					if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
						e.preventDefault();
						const nextBtn = viewBtns[(bIdx + 1) % viewBtns.length];
						nextBtn.focus();
						switchView(nextBtn.getAttribute('data-view') || 'carousel');
					}
				});
			});

			// Restore user preference
			try {
				const savedView = localStorage.getItem('rf_showcase_view');
				if (savedView === 'list' && listStage) {
					switchView('list');
				}
			} catch (err) {}

			// 5. FLOATING CURSOR IMAGE PREVIEW PHYSICS (Taqy-Style Desktop)
			const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
			if (canHover && floatingPreview && previewImg && listStage) {
				let targetX = 0;
				let targetY = 0;
				let currentX = 0;
				let currentY = 0;
				let isTracking = false;
				let rafId = null;
				let stageRect = listStage.getBoundingClientRect();

				const updateStageRect = () => {
					stageRect = listStage.getBoundingClientRect();
				};
				window.addEventListener('resize', updateStageRect, { passive: true });
				window.addEventListener('scroll', updateStageRect, { passive: true });

				function renderPreviewPhysics() {
					if (!isTracking) return;

					// Smooth Lerp Physics
					currentX += (targetX - currentX) * 0.14;
					currentY += (targetY - currentY) * 0.14;

					// Dynamic tilt angle based on horizontal drag speed
					const deltaX = targetX - currentX;
					const tilt = Math.max(-5, Math.min(5, deltaX * 0.18));

					floatingPreview.style.transform = `translate3d(${Math.round(currentX)}px, ${Math.round(currentY)}px, 0) rotate(${tilt.toFixed(2)}deg)`;
					rafId = requestAnimationFrame(renderPreviewPhysics);
				}

				listStage.addEventListener(
					'mousemove',
					(e) => {
						updateStageRect();
						// Offset slightly so cursor does not obscure preview content
						targetX = e.clientX - stageRect.left + 24;
						targetY = e.clientY - stageRect.top - 120;

						// Clamp inside viewport boundaries
						if (targetX + 320 > stageRect.width) {
							targetX = e.clientX - stageRect.left - 340;
						}
					},
					{ passive: true }
				);

				listRows.forEach((row) => {
					row.addEventListener('mouseenter', () => {
						const imgSrc = row.getAttribute('data-preview-img');
						const cat = row.getAttribute('data-cat') || 'Studi Kasus';

						if (imgSrc) {
							previewImg.src = imgSrc;
							if (previewBadge) previewBadge.textContent = cat;

							floatingPreview.classList.add('is-active');

							if (!isTracking) {
								isTracking = true;
								rafId = requestAnimationFrame(renderPreviewPhysics);
							}
						}
					});
				});

				listStage.addEventListener('mouseleave', () => {
					floatingPreview.classList.remove('is-active');
					isTracking = false;
					if (rafId) {
						cancelAnimationFrame(rafId);
						rafId = null;
					}
				});
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initShowcaseModules);
	} else {
		initShowcaseModules();
	}
})();
