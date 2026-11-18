/**
 * Rafi Portfolio — Ultra-Smooth GPU Motion Controller (v1.3.0)
 * - Zero heavy blurs, zero scroll color JS loops, zero mousemove layout thrashing
 * - Lightweight passive scroll listener (rAF-throttled) for top progress bar & header shadow
 * - One-shot IntersectionObserver scroll reveals (unobserves immediately)
 * - 100% safe inside Elementor Editor
 */
(function () {
	'use strict';

	const isElementorEditor =
		document.body.classList.contains('elementor-editor-active') ||
		window.location.search.indexOf('elementor-preview') !== -1;

	// 1. Lightweight Scroll Progress Bar + Header Shadow (rAF-throttled, GPU-only)
	const header = document.querySelector('header#masthead');
	let progressBar = null;

	if (!isElementorEditor) {
		progressBar = document.createElement('div');
		progressBar.className = 'rf-scroll-progress';
		progressBar.setAttribute('aria-hidden', 'true');
		document.body.prepend(progressBar);
	}

	let scrollTicking = false;
	const onScrollFrame = () => {
		const y = window.scrollY;
		if (header) {
			header.classList.toggle('is-scrolled', y > 14);
		}
		if (progressBar) {
			const maxScroll = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
			const p = Math.min(1, Math.max(0, y / maxScroll));
			progressBar.style.transform = `scaleX(${p.toFixed(4)})`;
		}
		scrollTicking = false;
	};

	const onScroll = () => {
		if (!scrollTicking) {
			scrollTicking = true;
			requestAnimationFrame(onScrollFrame);
		}
	};

	onScrollFrame();
	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });

	if (isElementorEditor) {
		return;
	}

	document.documentElement.classList.add('rf-js-ready');

	// 2. Scope only main page Elementor sections (exclude Header & Footer)
	const mainSections = Array.from(
		document.querySelectorAll('[data-elementor-type] > .e-con')
	).filter(
		(el) => !el.closest('header#masthead, footer#colophon, .hfe-site-header, .hfe-site-footer')
	);

	// Stagger Hero widgets smoothly immediately on page load
	if (mainSections.length > 0) {
		const hero = mainSections[0];
		hero.querySelectorAll('.elementor-widget').forEach((w, idx) => {
			w.classList.add('rf-hero-stagger');
			w.style.animationDelay = `${Math.min(idx, 5) * 75}ms`;
		});
	}

	// Mark sections after Hero (and marquee stays always visible smoothly)
	mainSections.slice(1).forEach((sec) => {
		if (!sec.classList.contains('rf-sec-marquee')) {
			sec.classList.add('rf-reveal');
		}
	});

	// Stagger cards in grids & case study blocks
	document.querySelectorAll('.rf-grid').forEach((grid) => {
		grid.querySelectorAll('.rf-card').forEach((card, idx) => {
			card.classList.add('rf-reveal');
			card.style.setProperty('--rf-delay', `${Math.min(idx, 5) * 75}ms`);
		});
	});

	document
		.querySelectorAll('.rf-case-header, .rf-case-hero-img, .rf-case-block, .rf-case-cta')
		.forEach((el, idx) => {
			el.classList.add('rf-reveal');
			el.style.setProperty('--rf-delay', `${Math.min(idx, 4) * 70}ms`);
		});

	// 3. One-shot IntersectionObserver (zero overhead after elements reveal)
	const revealEls = document.querySelectorAll('.rf-reveal');
	if ('IntersectionObserver' in window && revealEls.length > 0) {
		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{
				threshold: 0.06,
				rootMargin: '0px 0px -20px 0px',
			}
		);
		revealEls.forEach((el) => observer.observe(el));
	} else {
		revealEls.forEach((el) => el.classList.add('is-visible'));
	}
})();
