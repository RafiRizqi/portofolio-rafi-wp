/**
 * 3D Glassmorphism Carousel for Portfolio Showcase (v1.6.0)
 * Features:
 * - 3-Card Stage: Center focused, Left & Right in glass/blur behind
 * - Circular Nav Buttons & Pagination Dots
 * - Touch Swipe for Mobile & Keyboard Arrow Support
 * - Direct Click on Peeking Cards to Focus
 */
document.addEventListener('DOMContentLoaded', () => {
	const carousels = document.querySelectorAll('[data-rf-carousel]');
	if (!carousels.length) {
		return;
	}

	carousels.forEach((wrap) => {
		const track = wrap.querySelector('.rf-carousel-track');
		const cards = Array.from(wrap.querySelectorAll('.rf-carousel-card'));
		const prevBtn = wrap.querySelector('.rf-btn-prev');
		const nextBtn = wrap.querySelector('.rf-btn-next');
		const dots = Array.from(wrap.querySelectorAll('.rf-dot'));

		if (!cards.length) {
			return;
		}

		let currentIndex = 0;
		const total = cards.length;

		function updateClasses() {
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
		}

		function goTo(index) {
			currentIndex = (index + total) % total;
			updateClasses();
		}

		function goNext() {
			goTo(currentIndex + 1);
		}

		function goPrev() {
			goTo(currentIndex - 1);
		}

		// Button listeners
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

		// Dot listeners
		dots.forEach((dot, i) => {
			dot.addEventListener('click', (e) => {
				e.preventDefault();
				goTo(i);
			});
		});

		// Click on peeking side cards to bring them into center focus
		cards.forEach((card, i) => {
			card.addEventListener('click', (e) => {
				if (card.classList.contains('is-prev')) {
					e.preventDefault();
					goPrev();
				} else if (card.classList.contains('is-next')) {
					e.preventDefault();
					goNext();
				}
			});
		});

		// Keyboard Navigation when focused on wrap
		wrap.setAttribute('tabindex', '0');
		wrap.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowLeft') {
				e.preventDefault();
				goPrev();
			} else if (e.key === 'ArrowRight') {
				e.preventDefault();
				goNext();
			}
		});

		// Touch swipe support for mobile
		let touchStartX = 0;
		let touchEndX = 0;
		const minSwipeDistance = 45;

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
				touchEndX = e.changedTouches[0].screenX;
				const diff = touchEndX - touchStartX;
				if (Math.abs(diff) > minSwipeDistance) {
					if (diff < 0) {
						goNext();
					} else {
						goPrev();
					}
				}
			},
			{ passive: true }
		);

		// Initial render
		updateClasses();
	});
});
