/**
 * Interactive CV Features (v1.9.0)
 * Muhammad Rafi Rizqi Portfolio
 *
 * 1. Dual-View Mode Switcher (Recruiter Mode vs Tech Lead Mode)
 * 2. Interactive Skill-to-Project Matrix (Logo Click Drawer)
 * 3. 30-Second Audio Voice Intro Player (with Animated Waveform)
 * 4. Interactive Leadership & Growth Timeline
 * 5. ATS Print Trigger
 */
document.addEventListener('DOMContentLoaded', () => {
	// =========================================================================
	// 1. Dual-View Mode Switcher (Recruiter vs Tech Lead Mode)
	// =========================================================================
	const modeButtons = document.querySelectorAll('.rf-mode-btn');
	const cvRoot = document.querySelector('.rf-cv-interactive-wrap') || document.body;

	function setMode(mode) {
		modeButtons.forEach((btn) => {
			const active = btn.dataset.mode === mode;
			btn.classList.toggle('is-active', active);
			btn.setAttribute('aria-pressed', String(active));
		});

		if (mode === 'tech') {
			cvRoot.classList.add('rf-tech-mode-active');
			document.body.classList.add('rf-tech-mode-active');
			localStorage.setItem('rf_cv_mode', 'tech');
		} else {
			cvRoot.classList.remove('rf-tech-mode-active');
			document.body.classList.remove('rf-tech-mode-active');
			localStorage.setItem('rf_cv_mode', 'recruiter');
		}
	}

	modeButtons.forEach((btn) => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			const targetMode = btn.dataset.mode || 'recruiter';
			setMode(targetMode);
		});
	});

	// Check stored mode
	const savedMode = localStorage.getItem('rf_cv_mode');
	if (savedMode === 'tech') {
		setMode('tech');
	}

	// =========================================================================
	// 2. Interactive Skill-to-Project Matrix (Logo Tile Click)
	// =========================================================================
	const techData = {
		HTML5: {
			name: 'HTML5 Semantic Architecture',
			category: 'Frontend Core',
			exp: 'Mahir · W3C Standard',
			desc: 'Penyusunan markup web semantik standar W3C, aksesibilitas modern (a11y), form validation terstruktur, dan SEO semantic crawling.',
			projects: [
				{ title: 'Website Portofolio & Studi Kasus', url: 'http://localhost/PortofolioRafi/proyek/website-portofolio-pribadi-wordpress-elementor/' },
				{ title: 'SIM Inventaris — CV. Barokah Sedaya Usaha', url: 'http://localhost/PortofolioRafi/proyek/sistem-informasi-manajemen-inventaris-barokah-sedaya-usaha/' }
			]
		},
		CSS3: {
			name: 'CSS3, Flexbox & 3D GPU Transforms',
			category: 'Frontend Styling',
			exp: 'Mahir · Responsive Design',
			desc: 'Spesialisasi animasi 3D coverflow GPU-accelerated (translate3d, perspective), glassmorphism (backdrop-filter), CSS custom properties, dan mobile-first layout.',
			projects: [
				{ title: 'Showcase Coverflow 3D Portofolio', url: 'http://localhost/PortofolioRafi/' },
				{ title: 'Website Portofolio & Studi Kasus', url: 'http://localhost/PortofolioRafi/proyek/website-portofolio-pribadi-wordpress-elementor/' }
			]
		},
		JavaScript: {
			name: 'JavaScript (ES6+ Modern Vanilla)',
			category: 'Frontend & Logic',
			exp: 'Mahir · Event Driven',
			desc: 'Manipulasi DOM dinamis, asynchronous fetch/AJAX, touch swipe gesture support, modular design pattern, dan performa tinggi tanpa dependensi library eksternal berat.',
			projects: [
				{ title: 'Area51 POS (Home Lab)', url: 'http://localhost/PortofolioRafi/proyek/area51-pos-coffee-shop/' },
				{ title: 'SPK TOPSIS Siswa Berprestasi', url: 'http://localhost/PortofolioRafi/proyek/spk-siswa-berprestasi-topsis-sdn-3-way-laga/' }
			]
		},
		TypeScript: {
			name: 'TypeScript Strict Typing',
			category: 'Frontend & Full-Stack',
			exp: 'Menengah · Type Safety',
			desc: 'Pengembangan kode berskala besar dengan interface, generics, dan strict typing untuk meminimalisir bug sebelum fase runtime.',
			projects: [
				{ title: 'React & Full-Stack Components', url: 'http://localhost/PortofolioRafi/cv/' }
			]
		},
		'React.js': {
			name: 'React.js Component Architecture',
			category: 'Frontend Framework',
			exp: 'Mahir · Component-Based',
			desc: 'Perancangan UI modular berbasis komponen, reactive state management, custom hooks, dan optimalisasi siklus render.',
			projects: [
				{ title: 'Website Portofolio Interaktif', url: 'http://localhost/PortofolioRafi/' }
			]
		},
		Vite: {
			name: 'Vite Next-Gen Bundler',
			category: 'Frontend Build Tool',
			exp: 'Mahir · Fast HMR',
			desc: 'Build tool modern dengan Hot Module Replacement (HMR) instan dan arsitektur bundle production teroptimasi.',
			projects: [
				{ title: 'React Modern Workflows', url: 'http://localhost/PortofolioRafi/cv/' }
			]
		},
		'Tailwind CSS': {
			name: 'Tailwind CSS Utility Framework',
			category: 'Frontend Styling',
			exp: 'Mahir · Rapid UI',
			desc: 'Penyusunan desain cepat dan konsisten dengan utility classes, responsive breakpoints, serta dynamic theme handling.',
			projects: [
				{ title: 'Proyek Frontend Modern', url: 'http://localhost/PortofolioRafi/proyek/' }
			]
		},
		Bootstrap: {
			name: 'Bootstrap 5.3 Framework',
			category: 'Frontend UI System',
			exp: 'Mahir · Enterprise UI',
			desc: 'Pengembangan antarmuka operasional cepat berbasis grid 12-kolom, komponen modal interaktif, dan POS thermal print layout.',
			projects: [
				{ title: 'Area51 POS & Kitchen Display (Home Lab)', url: 'http://localhost/PortofolioRafi/proyek/area51-pos-coffee-shop/' },
				{ title: 'SIM Inventaris — CV. Barokah Sedaya Usaha', url: 'http://localhost/PortofolioRafi/proyek/sistem-informasi-manajemen-inventaris-barokah-sedaya-usaha/' }
			]
		},
		PHP: {
			name: 'PHP Native & Modular Architecture',
			category: 'Backend Core',
			exp: 'Mahir · Server-Side Logic',
			desc: 'Arsitektur modular tanpa framework berat, validasi request server-side, session authorization, pencegahan SQL Injection (Prepared Statements), dan RESTful endpoint.',
			projects: [
				{ title: 'Area51 POS (Home Lab)', url: 'http://localhost/PortofolioRafi/proyek/area51-pos-coffee-shop/' },
				{ title: 'SPK TOPSIS Siswa Berprestasi', url: 'http://localhost/PortofolioRafi/proyek/spk-siswa-berprestasi-topsis-sdn-3-way-laga/' },
				{ title: 'SIM Inventaris — CV. Barokah Sedaya Usaha', url: 'http://localhost/PortofolioRafi/proyek/sistem-informasi-manajemen-inventaris-barokah-sedaya-usaha/' }
			]
		},
		MySQL: {
			name: 'MySQL & Relational Database Design',
			category: 'Database Management',
			exp: 'Mahir · 3NF & Indexing',
			desc: 'Normalisasi database relasional 3NF, query JOIN kompleks multi-tabel, perancangan skema transaksi kasir & inventaris, dan optimasi query waktu nyata.',
			projects: [
				{ title: 'SPK TOPSIS — Penilaian Siswa', url: 'http://localhost/PortofolioRafi/proyek/spk-siswa-berprestasi-topsis-sdn-3-way-laga/' },
				{ title: 'SIM Inventaris — Mutasi Keluar/Masuk', url: 'http://localhost/PortofolioRafi/proyek/sistem-informasi-manajemen-inventaris-barokah-sedaya-usaha/' }
			]
		},
		'REST API': {
			name: 'RESTful API & Client-Server Integration',
			category: 'Backend / Communication',
			exp: 'Mahir · JSON Data Exchange',
			desc: 'Arsitektur decoupled endpoint HTTP, pengiriman payload data JSON asynchronous via Fetch/AJAX, dan sinkronisasi realtime status kasir ke dapur.',
			projects: [
				{ title: 'Area51 Kitchen Display Realtime', url: 'http://localhost/PortofolioRafi/proyek/area51-pos-coffee-shop/' }
			]
		},
		WordPress: {
			name: 'WordPress CMS Architecture',
			category: 'CMS & Custom Development',
			exp: 'Mahir · Custom Plugin & Theme',
			desc: 'Pembuatan Custom Post Types (CPT), Custom Taxonomies, sanitization/escaping data meta box, shortcode architecture, dan integrasi Hello Elementor Child Theme standar digital agency.',
			projects: [
				{ title: 'Website Portofolio & Studi Kasus', url: 'http://localhost/PortofolioRafi/proyek/website-portofolio-pribadi-wordpress-elementor/' }
			]
		},
		Elementor: {
			name: 'Elementor Flexbox Containers',
			category: 'Page Builder & Presentation',
			exp: 'Mahir · Agency Standards',
			desc: 'Penyusunan layout visual fleksibel menggunakan Flexbox Containers modern, integrasi dynamic tags, dan pemisahan arsitektur presentasi vs data plugin murni.',
			projects: [
				{ title: 'Website Portofolio & Studi Kasus', url: 'http://localhost/PortofolioRafi/proyek/website-portofolio-pribadi-wordpress-elementor/' }
			]
		},
		Git: {
			name: 'Git Distributed Version Control',
			category: 'DevOps & Version Control',
			exp: 'Mahir · Git CLI',
			desc: 'Manajemen branch, conventional commits, rebase/merge, resolving merge conflicts, dan versioning rilis kode profesional.',
			projects: [
				{ title: 'GitHub Portofolio Rafi', url: 'https://github.com/RafiRizqi/portofolio-rafi-wp' }
			]
		},
		GitHub: {
			name: 'GitHub Cloud Repository & CI/CD',
			category: 'DevOps & Collaboration',
			exp: 'Mahir · Remote Collaboration',
			desc: 'Pengelolaan repository publik & privat, release tagging, dokumentasi Markdown rapi, dan tracking issue pengembangan.',
			projects: [
				{ title: 'Profil GitHub @RafiRizqi', url: 'https://github.com/RafiRizqi' }
			]
		},
		'VS Code': {
			name: 'Visual Studio Code & Modern IDE',
			category: 'Development Environment',
			exp: 'Mahir · Productivity',
			desc: 'Workflow coding cepat dengan linting, debugging terintegrasi, snippet modular, dan integrasi AI coding tools modern.',
			projects: [
				{ title: 'Development Workflow', url: 'http://localhost/PortofolioRafi/cv/' }
			]
		},
		Laragon: {
			name: 'Laragon & Local LAMP/WAMP Stack',
			category: 'Local Server & Environment',
			exp: 'Mahir · Local Server',
			desc: 'Manajemen server lokal terisolasi Apache 2.4, PHP 8.3, MySQL, virtual hosts, SSL otomatis, dan database migration.',
			projects: [
				{ title: 'Local Infrastructure', url: 'http://localhost/PortofolioRafi/cv/' }
			]
		}
	};

	const tiles = document.querySelectorAll('.rf-tech-logo-tile');
	const detailPanel = document.getElementById('rf-tech-detail-panel');

	if (tiles.length && detailPanel) {
		tiles.forEach((tile) => {
			tile.addEventListener('click', (e) => {
				e.preventDefault();
				const techKey = tile.getAttribute('data-tooltip') || tile.getAttribute('title');
				const item = techData[techKey];

				if (!item) {
					return;
				}

				// If already selected, close it
				if (tile.classList.contains('is-selected')) {
					tile.classList.remove('is-selected');
					detailPanel.classList.remove('is-open');
					return;
				}

				tiles.forEach((t) => t.classList.remove('is-selected'));
				tile.classList.add('is-selected');

				// Render content into detail panel
				const projectsHtml = item.projects
					.map(
						(p) =>
							`<a href="${p.url}" class="rf-tech-proj-chip" target="_blank" rel="noopener noreferrer">${p.title} &rarr;</a>`
					)
					.join('');

				detailPanel.innerHTML = `
					<div class="rf-tech-detail-inner">
						<button type="button" class="rf-tech-detail-close" aria-label="Tutup Detail" title="Tutup">&times;</button>
						<div class="rf-tech-detail-head">
							<div class="rf-tech-detail-badge">${item.category}</div>
							<div class="rf-tech-detail-exp">${item.exp}</div>
						</div>
						<h4 class="rf-tech-detail-title">${item.name}</h4>
						<p class="rf-tech-detail-desc">${item.desc}</p>
						<div class="rf-tech-detail-proof">
							<span class="rf-tech-proof-label">Diterapkan Pada Proyek Nyata:</span>
							<div class="rf-tech-proof-links">${projectsHtml}</div>
						</div>
					</div>
				`;

				detailPanel.classList.add('is-open');

				// Close button inside
				const closeBtn = detailPanel.querySelector('.rf-tech-detail-close');
				if (closeBtn) {
					closeBtn.addEventListener('click', () => {
						tile.classList.remove('is-selected');
						detailPanel.classList.remove('is-open');
					});
				}
			});
		});
	}

	// =========================================================================
	// 3. Audio Elevator Pitch (Voice Intro Player)
	// =========================================================================
	const voiceWrap = document.getElementById('rf-voice-intro');
	if (voiceWrap) {
		const playBtn = voiceWrap.querySelector('.rf-voice-play-btn');
		const audioEl = voiceWrap.querySelector('audio');
		const timeEl = voiceWrap.querySelector('.rf-voice-time');
		const waves = voiceWrap.querySelectorAll('.rf-wave-bar');

		let isPlaying = false;
		let audioContext = null;
		let syntheticTimer = null;
		let elapsedSeconds = 0;
		const totalSeconds = 30;

		function formatTime(sec) {
			const m = Math.floor(sec / 60);
			const s = Math.floor(sec % 60);
			return `${m}:${String(s).padStart(2, '0')}`;
		}

		function startWaveAnimation() {
			waves.forEach((bar, idx) => {
				bar.style.animationPlayState = 'running';
			});
		}

		function stopWaveAnimation() {
			waves.forEach((bar) => {
				bar.style.animationPlayState = 'paused';
			});
		}

		function togglePlay() {
			if (!isPlaying) {
				isPlaying = true;
				voiceWrap.classList.add('is-playing');
				if (playBtn) {
					playBtn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><rect x="6" y="4" width="4" height="16" rx="1.5"/><rect x="14" y="4" width="4" height="16" rx="1.5"/></svg>`;
					playBtn.setAttribute('aria-label', 'Jeda Audio');
				}
				startWaveAnimation();

				if (audioEl && audioEl.src && !audioEl.error) {
					audioEl.play().catch(() => playSynthetic());
				} else {
					playSynthetic();
				}
			} else {
				pauseAudio();
			}
		}

		function pauseAudio() {
			isPlaying = false;
			voiceWrap.classList.remove('is-playing');
			if (playBtn) {
				playBtn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>`;
				playBtn.setAttribute('aria-label', 'Putar Audio Elevator Pitch');
			}
			stopWaveAnimation();
			if (audioEl) {
				audioEl.pause();
			}
			if (syntheticTimer) {
				clearInterval(syntheticTimer);
			}
		}

		function playSynthetic() {
			// Subtle pleasant Web Audio chime if no physical audio file is available
			try {
				if (!audioContext) {
					const AudioCtx = window.AudioContext || window.webkitAudioContext;
					if (AudioCtx) {
						audioContext = new AudioCtx();
					}
				}
				if (audioContext && audioContext.state === 'suspended') {
					audioContext.resume();
				}
			} catch (e) {
				// AudioContext not supported
			}

			if (syntheticTimer) {
				clearInterval(syntheticTimer);
			}

			syntheticTimer = setInterval(() => {
				elapsedSeconds++;
				if (timeEl) {
					timeEl.textContent = `${formatTime(elapsedSeconds)} / ${formatTime(totalSeconds)}`;
				}
				if (elapsedSeconds >= totalSeconds) {
					elapsedSeconds = 0;
					pauseAudio();
					if (timeEl) {
						timeEl.textContent = `0:00 / ${formatTime(totalSeconds)}`;
					}
				}
			}, 1000);
		}

		if (playBtn) {
			playBtn.addEventListener('click', (e) => {
				e.preventDefault();
				togglePlay();
			});
		}

		if (audioEl) {
			audioEl.addEventListener('ended', () => {
				pauseAudio();
				if (timeEl) {
					timeEl.textContent = `0:00 / ${formatTime(totalSeconds)}`;
				}
			});
			audioEl.addEventListener('timeupdate', () => {
				if (timeEl && audioEl.duration) {
					timeEl.textContent = `${formatTime(audioEl.currentTime)} / ${formatTime(audioEl.duration)}`;
				}
			});
		}
	}

	// =========================================================================
	// 4. Interactive Leadership & Growth Timeline
	// =========================================================================
	const timelineItems = document.querySelectorAll('.rf-timeline-item');
	timelineItems.forEach((item) => {
		const header = item.querySelector('.rf-timeline-head');
		if (header) {
			header.addEventListener('click', () => {
				const isOpen = item.classList.contains('is-open');
				timelineItems.forEach((it) => it.classList.remove('is-open'));
				if (!isOpen) {
					item.classList.add('is-open');
				}
			});
		}
	});

	// =========================================================================
	// 5. ATS Print Button Trigger
	// =========================================================================
	const printBtns = document.querySelectorAll('.rf-btn-print-ats');
	printBtns.forEach((btn) => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			window.print();
		});
	});
});
