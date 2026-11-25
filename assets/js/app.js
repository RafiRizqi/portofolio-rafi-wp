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
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
