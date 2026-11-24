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

		// Hero Micro-Interactions: Mouse-Tracking Spotlight & Subtle Tech Canvas
		const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
		const noMotionPreference = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		if (canHover && noMotionPreference) {
			hero.classList.add('rf-hero-container');

			// A. Ambient Radial Spotlight
			const spotlight = document.createElement('div');
			spotlight.className = 'rf-hero-spotlight';
			spotlight.setAttribute('aria-hidden', 'true');
			hero.prepend(spotlight);

			let spotTicking = false;
			let heroRect = hero.getBoundingClientRect();

			const updateHeroRect = () => {
				heroRect = hero.getBoundingClientRect();
			};
			window.addEventListener('resize', updateHeroRect, { passive: true });
			window.addEventListener('scroll', updateHeroRect, { passive: true });

			hero.addEventListener('mouseenter', () => {
				spotlight.classList.add('is-active');
				updateHeroRect();
			});

			hero.addEventListener('mouseleave', () => {
				spotlight.classList.remove('is-active');
			});

			hero.addEventListener(
				'mousemove',
				(e) => {
					if (!spotTicking) {
						spotTicking = true;
						requestAnimationFrame(() => {
							const x = e.clientX - heroRect.left;
							const y = e.clientY - heroRect.top;
							spotlight.style.setProperty('--spot-x', `${Math.round(x)}px`);
							spotlight.style.setProperty('--spot-y', `${Math.round(y)}px`);
							spotTicking = false;
						});
					}
				},
				{ passive: true }
			);

			// B. Interactive Subtle Tech Canvas
			const canvas = document.createElement('canvas');
			canvas.className = 'rf-hero-canvas';
			canvas.setAttribute('aria-hidden', 'true');
			hero.prepend(canvas);

			const ctx = canvas.getContext('2d', { alpha: true });
			const dpr = Math.min(window.devicePixelRatio || 1, 2);
			let width = 0;
			let height = 0;
			let animId = null;
			let isHeroVisible = true;

			const resizeCanvas = () => {
				width = hero.clientWidth;
				height = hero.clientHeight;
				canvas.width = Math.round(width * dpr);
				canvas.height = Math.round(height * dpr);
				canvas.style.width = `${width}px`;
				canvas.style.height = `${height}px`;
				ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
			};

			resizeCanvas();
			window.addEventListener('resize', resizeCanvas, { passive: true });

			// Initialize subtle floating nodes
			const nodeCount = Math.max(16, Math.min(28, Math.floor(width / 45)));
			const nodes = [];
			for (let i = 0; i < nodeCount; i++) {
				nodes.push({
					x: Math.random() * width,
					y: Math.random() * height,
					vx: (Math.random() - 0.5) * 0.35,
					vy: (Math.random() - 0.5) * 0.35,
					radius: Math.random() * 1.5 + 1.2,
				});
			}

			let cursorX = -9999;
			let cursorY = -9999;
			hero.addEventListener(
				'mousemove',
				(e) => {
					cursorX = e.clientX - heroRect.left;
					cursorY = e.clientY - heroRect.top;
				},
				{ passive: true }
			);
			hero.addEventListener('mouseleave', () => {
				cursorX = -9999;
				cursorY = -9999;
			});

			const renderTechCanvas = () => {
				if (!isHeroVisible) return;

				ctx.clearRect(0, 0, width, height);

				for (let i = 0; i < nodes.length; i++) {
					const node = nodes[i];
					node.x += node.vx;
					node.y += node.vy;

					if (node.x < 0 || node.x > width) node.vx *= -1;
					if (node.y < 0 || node.y > height) node.vy *= -1;

					const cdx = node.x - cursorX;
					const cdy = node.y - cursorY;
					const cdist = Math.hypot(cdx, cdy);

					ctx.beginPath();
					ctx.arc(node.x, node.y, node.radius, 0, Math.PI * 2);

					if (cdist < 130) {
						const glow = 1 - cdist / 130;
						ctx.fillStyle = `rgba(56, 189, 248, ${0.45 + glow * 0.45})`;
						ctx.fill();

						ctx.beginPath();
						ctx.moveTo(node.x, node.y);
						ctx.lineTo(cursorX, cursorY);
						ctx.strokeStyle = `rgba(56, 189, 248, ${(glow * 0.2).toFixed(3)})`;
						ctx.lineWidth = 1;
						ctx.stroke();
					} else {
						ctx.fillStyle = 'rgba(37, 99, 235, 0.28)';
						ctx.fill();
					}

					for (let j = i + 1; j < nodes.length; j++) {
						const n2 = nodes[j];
						const dist = Math.hypot(node.x - n2.x, node.y - n2.y);
						if (dist < 95) {
							const alpha = (1 - dist / 95) * 0.09;
							ctx.beginPath();
							ctx.moveTo(node.x, node.y);
							ctx.lineTo(n2.x, n2.y);
							ctx.strokeStyle = `rgba(37, 99, 235, ${alpha.toFixed(3)})`;
							ctx.lineWidth = 0.8;
							ctx.stroke();
						}
					}
				}

				animId = requestAnimationFrame(renderTechCanvas);
			};

			// Pause animation when hero leaves viewport (zero CPU drain)
			if ('IntersectionObserver' in window) {
				const heroObserver = new IntersectionObserver(
					([entry]) => {
						isHeroVisible = entry.isIntersecting;
						if (isHeroVisible) {
							if (!animId) {
								animId = requestAnimationFrame(renderTechCanvas);
							}
						} else {
							if (animId) {
								cancelAnimationFrame(animId);
								animId = null;
							}
						}
					},
					{ threshold: 0.05 }
				);
				heroObserver.observe(hero);
			} else {
				animId = requestAnimationFrame(renderTechCanvas);
			}

			// C. Tactile 3D Tilt on Hero Portrait
			const portraitCard = hero.querySelector('.rf-hero-photo, .elementor-widget-image');
			if (portraitCard) {
				portraitCard.classList.add('rf-hero-tilt');
				portraitCard.addEventListener('mousemove', (e) => {
					const rect = portraitCard.getBoundingClientRect();
					const px = (e.clientX - rect.left) / rect.width - 0.5;
					const py = (e.clientY - rect.top) / rect.height - 0.5;
					const rotX = (-py * 7).toFixed(2);
					const rotY = (px * 7).toFixed(2);
					portraitCard.style.transform = `perspective(800px) rotateX(${rotX}deg) rotateY(${rotY}deg) scale3d(1.02, 1.02, 1.02)`;
				});
				portraitCard.addEventListener('mouseleave', () => {
					portraitCard.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
				});
			}
		}
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
