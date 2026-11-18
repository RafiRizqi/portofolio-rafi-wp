document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('[data-rf-projects]').forEach((moduleEl) => {
		const buttons = moduleEl.querySelectorAll('.rf-filter-btn');
		const cards = moduleEl.querySelectorAll('.rf-project-card');

		if (!buttons.length || !cards.length) {
			return;
		}

		buttons.forEach((btn) => {
			btn.addEventListener('click', () => {
				const filter = btn.getAttribute('data-filter') || 'all';

				buttons.forEach((b) => {
					const active = b === btn;
					b.classList.toggle('is-active', active);
					b.setAttribute('aria-pressed', String(active));
				});

				let visibleIndex = 0;
				cards.forEach((card) => {
					const cats = (card.getAttribute('data-categories') || '').split(' ').filter(Boolean);
					const show = filter === 'all' || cats.includes(filter);
					card.classList.remove('is-filtering-in');

					if (show) {
						card.classList.remove('is-hidden');
						card.classList.add('is-visible');
						card.style.animationDelay = `${Math.min(visibleIndex, 5) * 45}ms`;
						// Force reflow so filter fade-in replays cleanly
						void card.offsetWidth;
						card.classList.add('is-filtering-in');
						visibleIndex++;
					} else {
						card.classList.add('is-hidden');
					}
				});
			});
		});
	});
});
