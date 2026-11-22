/**
 * Portfolio Category Filter & Live Counter v2.3.0
 * Agency-grade client-side category filtering with live counters,
 * staggered animations, keyboard tabs navigation, and empty-state handling.
 *
 * @package Rafi_Portfolio_Core
 */

(function () {
	'use strict';

	function initPortfolioFilters() {
		const modules = document.querySelectorAll('[data-rf-projects]');
		if (!modules.length) return;

		modules.forEach((moduleEl) => {
			const buttons = Array.from(moduleEl.querySelectorAll('.rf-filter-btn'));
			const cards = Array.from(moduleEl.querySelectorAll('.rf-project-card'));
			const statusNum = moduleEl.querySelector('.rf-status-current');
			const emptyState = moduleEl.querySelector('.rf-filter-empty');
			const resetBtn = moduleEl.querySelector('.rf-reset-filter-btn');

			if (!buttons.length || !cards.length) return;

			function applyFilter(filterSlug, activeBtn) {
				// 1. Update button states
				buttons.forEach((btn) => {
					const isActive = btn === activeBtn;
					btn.classList.toggle('is-active', isActive);
					btn.setAttribute('aria-selected', String(isActive));
					btn.setAttribute('tabindex', isActive ? '0' : '-1');
				});

				// 2. Filter cards with staggered entrance
				let visibleCount = 0;
				cards.forEach((card) => {
					const categories = (card.getAttribute('data-categories') || '').split(' ').filter(Boolean);
					const isMatch = filterSlug === 'all' || categories.includes(filterSlug);

					card.classList.remove('is-filtering-in');

					if (isMatch) {
						card.classList.remove('is-hidden');
						card.classList.add('is-visible');
						card.style.animationDelay = `${Math.min(visibleCount, 5) * 45}ms`;
						// Force reflow for clean animation replay
						void card.offsetWidth;
						card.classList.add('is-filtering-in');
						visibleCount++;
					} else {
						card.classList.add('is-hidden');
						card.classList.remove('is-visible');
					}
				});

				// 3. Update status counter
				if (statusNum) {
					statusNum.textContent = String(visibleCount);
				}

				// 4. Toggle empty state
				if (emptyState) {
					if (visibleCount === 0) {
						emptyState.style.display = 'flex';
					} else {
						emptyState.style.display = 'none';
					}
				}
			}

			// Attach click events
			buttons.forEach((btn, index) => {
				btn.addEventListener('click', () => {
					const filter = btn.getAttribute('data-filter') || 'all';
					applyFilter(filter, btn);
				});

				// Accessible keyboard navigation (ArrowLeft / ArrowRight / Home / End)
				btn.addEventListener('keydown', (e) => {
					let targetIndex = -1;
					if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
						e.preventDefault();
						targetIndex = (index + 1) % buttons.length;
					} else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
						e.preventDefault();
						targetIndex = (index - 1 + buttons.length) % buttons.length;
					} else if (e.key === 'Home') {
						e.preventDefault();
						targetIndex = 0;
					} else if (e.key === 'End') {
						e.preventDefault();
						targetIndex = buttons.length - 1;
					}

					if (targetIndex >= 0) {
						buttons[targetIndex].focus();
						const filter = buttons[targetIndex].getAttribute('data-filter') || 'all';
						applyFilter(filter, buttons[targetIndex]);
					}
				});
			});

			// Reset filter button in empty state
			if (resetBtn) {
				resetBtn.addEventListener('click', () => {
					const allBtn = moduleEl.querySelector('.rf-filter-btn[data-filter="all"]');
					if (allBtn) {
						allBtn.focus();
						applyFilter('all', allBtn);
					}
				});
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initPortfolioFilters);
	} else {
		initPortfolioFilters();
	}
})();
