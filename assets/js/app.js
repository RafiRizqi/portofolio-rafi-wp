/**
 * portfolio-taqy-farline.js
 * Pure Taqy ✦ Farline Transformation — Interactive Layer
 * Version: 3.0.0
 *
 * Features:
 *  1. Click-to-copy email (hero + contact) + toast notification
 *  2. Web Audio API mechanical tick (zero file download, 0 KB overhead)
 *  3. Sound toggle + localStorage persistence
 *  4. Mobile hamburger nav toggle
 *  5. Smooth anchor scrolling with header offset
 *  6. Nav link active-section highlight on scroll
 *  7. Smooth GPU motion & scroll reveals
 */

(function () {
	'use strict';

	/* ─────────────────────────────────────────────────────────────────
	   CONSTANTS
	──────────────────────────────────────────────────────────────────── */
	const LS_SOUND = 'rf_tf_sound';   // 'on' | 'off'

	/* ─────────────────────────────────────────────────────────────────
	   1. WEB AUDIO — SYNTHESIZED MECHANICAL TICK
	──────────────────────────────────────────────────────────────────── */
	let audioCtx = null;

	function getAudioCtx() {
		if (!audioCtx) {
			try {
				audioCtx = new (window.AudioContext || window.webkitAudioContext)();
			} catch (e) { /* no audio support */ }
		}
		return audioCtx;
	}

	function playTick() {
		if (getSoundPref() !== 'on') return;
		const ctx = getAudioCtx();
		if (!ctx) return;

		// Resume suspended context (Chrome autoplay policy)
		if (ctx.state === 'suspended') ctx.resume();

		// White noise burst — mechanical click feel
		const bufSize = ctx.sampleRate * 0.015; // 15ms
		const buffer  = ctx.createBuffer(1, bufSize, ctx.sampleRate);
		const data    = buffer.getChannelData(0);
		for (let i = 0; i < bufSize; i++) {
			data[i] = (Math.random() * 2 - 1) * (1 - i / bufSize) * 0.3;
		}
		const src = ctx.createBufferSource();
		src.buffer = buffer;

		// High-pass filter — remove muddiness, keep the sharp transient
		const hpf = ctx.createBiquadFilter();
		hpf.type            = 'highpass';
		hpf.frequency.value = 1800;

		// Gain
		const gain = ctx.createGain();
		gain.gain.setValueAtTime(1, ctx.currentTime);
		gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.015);

		src.connect(hpf);
		hpf.connect(gain);
		gain.connect(ctx.destination);
		src.start();
		src.stop(ctx.currentTime + 0.015);
	}

	/* ─────────────────────────────────────────────────────────────────
	   2. SOUND PREFERENCE
	──────────────────────────────────────────────────────────────────── */
	function getSoundPref() {
		return localStorage.getItem(LS_SOUND) || 'off'; // default OFF until user enables
	}

	function setSoundPref(val) {
		localStorage.setItem(LS_SOUND, val);
	}

	function initSoundToggle() {
		const btn   = document.getElementById('rf-sound-toggle');
		const icon  = document.getElementById('rf-sound-icon');
		const label = document.getElementById('rf-sound-label');
		if (!btn) return;

		function updateUI(pref) {
			const isOn = pref === 'on';
			btn.setAttribute('aria-pressed', String(isOn));
			icon.textContent  = isOn ? '🔊' : '🔇';
			label.textContent = isOn ? 'SOUND: ON' : 'SOUND: OFF';
		}

		updateUI(getSoundPref());

		btn.addEventListener('click', () => {
			const next = getSoundPref() === 'on' ? 'off' : 'on';
			setSoundPref(next);
			updateUI(next);
			if (next === 'on') {
				// Resume audio context on first user gesture
				const ctx = getAudioCtx();
				if (ctx && ctx.state === 'suspended') ctx.resume();
				playTick();
			}
		});
	}

	/* ─────────────────────────────────────────────────────────────────
	   3. CLICK-TO-COPY EMAIL
	──────────────────────────────────────────────────────────────────── */
	function initCopyEmail() {
		// Hero copy button
		const heroBtn = document.getElementById('rf-copy-email');
		if (heroBtn) {
			heroBtn.addEventListener('click', () => {
				const email = heroBtn.dataset.email || '';
				copyText(email, () => {
					const lbl = document.getElementById('rf-copy-label');
					if (lbl) {
						const orig = lbl.textContent;
						lbl.textContent = '✓ COPIED!';
						setTimeout(() => { lbl.textContent = orig; }, 2000);
					}
				});
			});
		}

		// Contact section copy button + toast
		const contactBtn = document.getElementById('rf-contact-copy-email');
		const toast      = document.getElementById('rf-copy-toast');
		if (contactBtn) {
			contactBtn.addEventListener('click', () => {
				const email = contactBtn.dataset.email || '';
				copyText(email, () => {
					if (toast) {
						toast.hidden = false;
						setTimeout(() => { toast.hidden = true; }, 2800);
					}
				});
				playTick();
			});
		}
	}

	function copyText(text, cb) {
		if (navigator.clipboard) {
			navigator.clipboard.writeText(text).then(cb).catch(() => fallbackCopy(text, cb));
		} else {
			fallbackCopy(text, cb);
		}
	}

	function fallbackCopy(text, cb) {
		const ta = document.createElement('textarea');
		ta.value = text;
		ta.style.cssText = 'position:fixed;opacity:0;pointer-events:none;';
		document.body.appendChild(ta);
		ta.select();
		try { document.execCommand('copy'); cb && cb(); } catch (e) { /* silent */ }
		document.body.removeChild(ta);
	}

	/* ─────────────────────────────────────────────────────────────────
	   7. MOBILE HAMBURGER
	──────────────────────────────────────────────────────────────────── */
	function initHamburger() {
		const btn = document.getElementById('rf-hamburger');
		const nav = document.getElementById('rf-mobile-nav');
		if (!btn || !nav) return;

		btn.addEventListener('click', () => {
			const expanded = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', String(!expanded));
			nav.classList.toggle('is-open', !expanded);
			nav.setAttribute('aria-hidden', String(expanded));
		});

		// Close on nav link click
		nav.querySelectorAll('a').forEach((a) => {
			a.addEventListener('click', () => {
				btn.setAttribute('aria-expanded', 'false');
				nav.classList.remove('is-open');
				nav.setAttribute('aria-hidden', 'true');
			});
		});
	}

	/* ─────────────────────────────────────────────────────────────────
	   8. SMOOTH SCROLL WITH HEADER OFFSET
	──────────────────────────────────────────────────────────────────── */
	function initSmoothScroll() {
		const header = document.getElementById('rf-tf-header');

		document.querySelectorAll('a[href^="#"]').forEach((a) => {
			a.addEventListener('click', (e) => {
				const target = document.querySelector(a.getAttribute('href'));
				if (!target) return;
				e.preventDefault();
				const offset = header ? header.offsetHeight + 16 : 72;
				const top    = target.getBoundingClientRect().top + window.scrollY - offset;
				window.scrollTo({ top, behavior: 'smooth' });
				playTick();
			});
		});
	}

	/* ─────────────────────────────────────────────────────────────────
	   9. ACTIVE NAV HIGHLIGHT ON SCROLL
	──────────────────────────────────────────────────────────────────── */
	function initActiveNav() {
		const sections = document.querySelectorAll('section[id]');
		const navLinks = document.querySelectorAll('.rf-tf-nav-link');
		if (!sections.length || !navLinks.length) return;

		const header = document.getElementById('rf-tf-header');

		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						navLinks.forEach((l) => {
							const href = l.getAttribute('href');
							l.classList.toggle(
								'rf-tf-nav-link--active',
								href === '#' + entry.target.id
							);
						});
					}
				});
			},
			{
				rootMargin: `-${header ? header.offsetHeight : 64}px 0px -60% 0px`,
				threshold: 0,
			}
		);

		sections.forEach((s) => observer.observe(s));
	}

	/* ─────────────────────────────────────────────────────────────────
	   10. BUTTON TICK FEEDBACK
	──────────────────────────────────────────────────────────────────── */
	function initButtonTicks() {
		document.querySelectorAll('.rf-tf-btn, .rf-tf-view-btn, .rf-tf-nav-link, .rf-tf-social-link, .rf-tf-cert-view').forEach((el) => {
			el.addEventListener('click', () => playTick(), { passive: true });
		});
	}

	/* ─────────────────────────────────────────────────────────────────
	   11. SMOOTH GPU MOTION & SCROLL REVEALS
	──────────────────────────────────────────────────────────────────── */
	function initMotionReveals() {
		const header = document.getElementById('rf-tf-header');
		if (header) {
			let scrollTicking = false;
			const updateHeader = () => {
				scrollTicking = false;
				header.classList.toggle('is-scrolled', window.scrollY > 12);
			};
			updateHeader();
			window.addEventListener('scroll', () => {
				if (!scrollTicking) {
					scrollTicking = true;
					requestAnimationFrame(updateHeader);
				}
			}, { passive: true });
		}

		if (!('IntersectionObserver' in window)) return;

		document.documentElement.classList.add('rf-anim-ready');
		const targets = document.querySelectorAll(
			'#rf-tf-root .rf-tf-section-header, #rf-tf-root .rf-tf-carousel-stage, #rf-tf-root .rf-tf-spec-row, #rf-tf-root .rf-tf-ledger-row, #rf-tf-root .rf-tf-cert-card, #rf-tf-root .rf-tf-contact-cta'
		);

		const io = new IntersectionObserver((entries, obs) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-in');
					obs.unobserve(entry.target);
				}
			});
		}, { threshold: 0.08, rootMargin: '0px 0px -32px 0px' });

		targets.forEach((el, idx) => {
			el.classList.add('rf-reveal');
			el.style.transitionDelay = `${(idx % 4) * 65}ms`;
			io.observe(el);
		});
	}

	/* ─────────────────────────────────────────────────────────────────
	   12. GEN Z EASTER EGGS & VISUAL FX HELPERS
	──────────────────────────────────────────────────────────────────── */
	let eggToastEl = null;
	let eggToastTimer = null;

	window.rfShowToast = function (icon, title, subtitle, action) {
		if (!eggToastEl) {
			eggToastEl = document.createElement('div');
			eggToastEl.className = 'rf-egg-toast';
			eggToastEl.setAttribute('role', 'status');
			eggToastEl.setAttribute('aria-live', 'polite');
			document.body.appendChild(eggToastEl);
		}
		eggToastEl.innerHTML = '';

		const iconSpan = document.createElement('span');
		iconSpan.className = 'rf-egg-toast__icon';
		iconSpan.textContent = icon || '✨';

		const body = document.createElement('div');
		body.className = 'rf-egg-toast__body';

		const titleEl = document.createElement('strong');
		titleEl.className = 'rf-egg-toast__title';
		titleEl.textContent = title || '';

		const subEl = document.createElement('span');
		subEl.className = 'rf-egg-toast__sub';
		subEl.textContent = subtitle || '';

		body.appendChild(titleEl);
		if (subtitle) body.appendChild(subEl);
		eggToastEl.appendChild(iconSpan);
		eggToastEl.appendChild(body);

		if (action && action.href && action.label) {
			const link = document.createElement('a');
			link.className = 'rf-egg-toast__cta';
			link.href = action.href;
			link.target = '_blank';
			link.rel = 'noopener noreferrer';
			link.textContent = action.label;
			eggToastEl.appendChild(link);
		}

		void eggToastEl.offsetWidth;
		eggToastEl.classList.add('is-visible');

		if (eggToastTimer) clearTimeout(eggToastTimer);
		eggToastTimer = setTimeout(() => {
			eggToastEl.classList.remove('is-visible');
		}, action ? 6000 : 4500);
	};

	window.rfLaunchConfetti = function (originX, originY) {
		const canvas = document.createElement('canvas');
		canvas.className = 'rf-egg-canvas';
		canvas.width = window.innerWidth;
		canvas.height = window.innerHeight;
		document.body.appendChild(canvas);
		const ctx = canvas.getContext('2d');
		if (!ctx) {
			canvas.remove();
			return;
		}

		const colors = ['#38bdf8', '#2563eb', '#f59e0b', '#10b981', '#ec4899', '#ffffff'];
		const cx = typeof originX === 'number' ? originX : canvas.width / 2;
		const cy = typeof originY === 'number' ? originY : canvas.height * 0.4;
		const particles = Array.from({ length: 68 }, () => {
			const angle = Math.random() * Math.PI * 2;
			const speed = 4 + Math.random() * 9;
			return {
				x: cx,
				y: cy,
				vx: Math.cos(angle) * speed,
				vy: Math.sin(angle) * speed - 4,
				size: 5 + Math.random() * 5,
				color: colors[Math.floor(Math.random() * colors.length)],
				rot: Math.random() * Math.PI,
				vRot: (Math.random() - 0.5) * 0.25,
				alpha: 1,
			};
		});

		const start = performance.now();
		function tick(now) {
			const elapsed = now - start;
			ctx.clearRect(0, 0, canvas.width, canvas.height);
			let alive = false;
			for (const p of particles) {
				p.x += p.vx;
				p.y += p.vy;
				p.vy += 0.28;
				p.vx *= 0.99;
				p.rot += p.vRot;
				if (elapsed > 1400) p.alpha = Math.max(0, 1 - (elapsed - 1400) / 900);
				if (p.alpha > 0.02 && p.y < canvas.height + 40) alive = true;
				ctx.save();
				ctx.globalAlpha = p.alpha;
				ctx.translate(p.x, p.y);
				ctx.rotate(p.rot);
				ctx.fillStyle = p.color;
				ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.65);
				ctx.restore();
			}
			if (alive && elapsed < 2400) {
				requestAnimationFrame(tick);
			} else {
				canvas.remove();
			}
		}
		requestAnimationFrame(tick);
	};

	window.rfStartMatrixRain = function () {
		const existing = document.getElementById('rf-matrix-canvas');
		if (existing) existing.remove();

		const canvas = document.createElement('canvas');
		canvas.id = 'rf-matrix-canvas';
		canvas.className = 'rf-egg-canvas';
		canvas.style.background = 'rgba(2, 6, 23, 0.42)';
		canvas.width = window.innerWidth;
		canvas.height = window.innerHeight;
		document.body.appendChild(canvas);

		const ctx = canvas.getContext('2d');
		if (!ctx) {
			canvas.remove();
			return;
		}

		const chars = '01RAFI{}<>/$#ABCDEF';
		const fontSize = 15;
		const cols = Math.floor(canvas.width / fontSize);
		const drops = Array.from({ length: cols }, () => Math.floor(Math.random() * -20));
		let stopped = false;
		const start = performance.now();

		function stopRain() {
			if (stopped) return;
			stopped = true;
			document.removeEventListener('keydown', onEsc);
			canvas.style.opacity = '0';
			setTimeout(() => canvas.remove(), 420);
		}
		function onEsc(e) {
			if (e.key === 'Escape') stopRain();
		}
		document.addEventListener('keydown', onEsc);

		function draw(now) {
			if (stopped) return;
			const elapsed = now - start;
			ctx.fillStyle = 'rgba(2, 6, 23, 0.14)';
			ctx.fillRect(0, 0, canvas.width, canvas.height);
			ctx.fillStyle = '#22c55e';
			ctx.font = `700 ${fontSize}px "JetBrains Mono", monospace`;

			for (let i = 0; i < drops.length; i++) {
				const text = chars[Math.floor(Math.random() * chars.length)];
				ctx.fillText(text, i * fontSize, drops[i] * fontSize);
				if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
					drops[i] = 0;
				}
				drops[i]++;
			}

			if (elapsed < 4000) {
				requestAnimationFrame(draw);
			} else {
				stopRain();
			}
		}
		requestAnimationFrame(draw);
	};

	function initTabVisibilityEgg() {
		const originalTitle = document.title;
		let returnTimer = null;

		document.addEventListener('visibilitychange', () => {
			if (returnTimer) {
				clearTimeout(returnTimer);
				returnTimer = null;
			}
			if (document.hidden) {
				document.title = '👀 Eh, jangan lupa balik ya...';
			} else {
				document.title = 'Hehe balik lagi ✨ — Rafi Rizqi';
				returnTimer = setTimeout(() => {
					document.title = originalTitle;
				}, 2000);
			}
		});
	}

	function initConsoleGreeting() {
		try {
			console.log(
				'%c ⚡ RAFI RIZQI // DEV CONSOLE %c\nHalo Senior Dev / Tech Recruiter! Lagi ngecek kerapian kode ya?\nTenang — 0 error, zero bloat, & siap diajak ngoding bareng 😎\n👉 Ketik "hire", "matrix", atau "hihu" di Spotlight Search (Ctrl+K)!',
				'background:#0f172a;color:#38bdf8;font-size:13px;font-weight:700;padding:8px 14px;border-radius:8px;border:1px solid #38bdf8;',
				'color:#475569;font-size:12px;line-height:1.6;'
			);
		} catch (_) {}
	}

	function initRobotEyeTracking() {
		const trigger = document.getElementById('rf-palette-trigger');
		const eyes = trigger ? trigger.querySelector('.rf-robot-eyes') : null;
		if (!trigger || !eyes) return;
		if (window.matchMedia('(pointer: coarse)').matches || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			return;
		}

		let rafPending = false;
		let lastX = 0;
		let lastY = 0;

		function updateEyes() {
			rafPending = false;
			const rect = trigger.getBoundingClientRect();
			const cx = rect.left + rect.width / 2;
			const cy = rect.top + rect.height / 2;
			const dx = lastX - cx;
			const dy = lastY - cy;
			const dist = Math.hypot(dx, dy) || 1;
			const strength = Math.min(1, dist / 240);
			const moveX = (dx / dist) * 2.6 * strength;
			const moveY = (dy / dist) * 1.8 * strength;
			eyes.style.transform = `translate(${moveX.toFixed(2)}px, ${moveY.toFixed(2)}px)`;
		}

		document.addEventListener('mousemove', (e) => {
			lastX = e.clientX;
			lastY = e.clientY;
			if (!rafPending) {
				rafPending = true;
				requestAnimationFrame(updateEyes);
			}
		}, { passive: true });

		document.addEventListener('mouseleave', () => {
			eyes.style.transform = 'translate(0px, 0px)';
		});
	}

	function initPortraitEgg() {
		const frame = document.querySelector('#rf-tf-root .rf-tf-portrait-frame');
		if (!frame) return;

		let clicks = 0;
		let resetTimer = null;
		let unlocked = false;

		frame.addEventListener('click', () => {
			clicks++;
			if (resetTimer) clearTimeout(resetTimer);
			resetTimer = setTimeout(() => {
				clicks = 0;
			}, 3000);

			if (clicks >= 5) {
				clicks = 0;
				const coolNow = frame.classList.toggle('is-cool');
				if (coolNow) {
					const rect = frame.getBoundingClientRect();
					if (typeof window.rfLaunchConfetti === 'function') {
						window.rfLaunchConfetti(rect.left + rect.width / 2, rect.top + rect.height * 0.35);
					}
					if (!unlocked && typeof window.rfShowToast === 'function') {
						unlocked = true;
						window.rfShowToast('🏆', 'Achievement Unlocked!', 'Stalker HR Terdeteksi 🕵️‍♂️');
					}
				}
			}
		});
	}

	/* ─────────────────────────────────────────────────────────────────
	   INIT
	──────────────────────────────────────────────────────────────────── */
	function init() {
		initSoundToggle();
		initCopyEmail();
		initHamburger();
		initSmoothScroll();
		initActiveNav();
		initButtonTicks();
		initMotionReveals();
		initTabVisibilityEgg();
		initConsoleGreeting();
		initRobotEyeTracking();
		initPortraitEgg();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
