/**
 * Rafi Portfolio Core — Command Palette (Ctrl+K Spotlight) & Reading Progress Bar (v2.1.0)
 * Linear / Vercel style instant navigation and 60fps scroll progress.
 */
document.addEventListener('DOMContentLoaded', function () {
	// --------------------------------------------------------------------------
	// 1. Reading / Scroll Progress Bar
	// --------------------------------------------------------------------------
	var progressBar = document.getElementById('rf-progress-bar');
	if (progressBar) {
		var ticking = false;

		function updateProgress() {
			var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
			var docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
			var scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
			progressBar.style.width = Math.min(100, Math.max(0, scrollPercent)) + '%';
			ticking = false;
		}

		window.addEventListener('scroll', function () {
			if (!ticking) {
				window.requestAnimationFrame(updateProgress);
				ticking = true;
			}
		}, { passive: true });

		updateProgress();
	}

	// --------------------------------------------------------------------------
	// 2. Command Palette (Ctrl + K)
	// --------------------------------------------------------------------------
	var backdrop = document.getElementById('rf-command-palette');
	if (!backdrop) return;

	var dialog = backdrop.querySelector('.rf-palette-dialog');
	var input = document.getElementById('rf-palette-input');
	var resultsContainer = document.getElementById('rf-palette-results');
	var emptyState = document.getElementById('rf-palette-empty');
	var emptyQuery = document.getElementById('rf-empty-query');
	var triggerBtn = document.getElementById('rf-palette-trigger');
	var allItems = Array.from(backdrop.querySelectorAll('.rf-palette-item'));
	var allGroups = Array.from(backdrop.querySelectorAll('.rf-palette-group'));
	var filterTabs = Array.from(backdrop.querySelectorAll('.rf-palette-tab'));
	var countEl = document.getElementById('rf-palette-count');

	var isOpen = false;
	var focusedIndex = 0;
	var activeFilter = 'all';

	function getVisibleItems() {
		return allItems.filter(function (item) {
			return item.style.display !== 'none';
		});
	}

	function updateFocusedItem(visibleItems) {
		allItems.forEach(function (el) {
			el.classList.remove('is-focused');
		});

		if (visibleItems.length > 0) {
			if (focusedIndex >= visibleItems.length) {
				focusedIndex = visibleItems.length - 1;
			}
			if (focusedIndex < 0) {
				focusedIndex = 0;
			}
			var activeItem = visibleItems[focusedIndex];
			activeItem.classList.add('is-focused');
			activeItem.scrollIntoView({ block: 'nearest' });
		}
	}

	function openPalette() {
		isOpen = true;
		backdrop.classList.add('is-open');
		backdrop.setAttribute('aria-hidden', 'false');
		document.documentElement.classList.add('rf-palette-locked');
		document.body.classList.add('rf-palette-locked');

		activeFilter = 'all';
		filterTabs.forEach(function (tab) {
			var isAll = tab.getAttribute('data-filter') === 'all';
			tab.classList.toggle('is-active', isAll);
			tab.setAttribute('aria-selected', String(isAll));
		});

		if (input) {
			input.value = '';
			filterItems('');
			setTimeout(function () {
				input.focus();
			}, 50);
		}
	}

	function closePalette() {
		isOpen = false;
		backdrop.classList.remove('is-open');
		backdrop.setAttribute('aria-hidden', 'true');
		document.documentElement.classList.remove('rf-palette-locked');
		document.body.classList.remove('rf-palette-locked');
	}

	function filterItems(query) {
		var q = (query || '').toLowerCase().trim();
		var visibleCount = 0;

		allItems.forEach(function (item) {
			var grp = item.closest('.rf-palette-group');
			var grpName = grp ? grp.getAttribute('data-group') : '';
			var matchesTab = activeFilter === 'all' || grpName === activeFilter;

			var label = (item.querySelector('.rf-item-label') || {}).textContent || '';
			var desc = (item.querySelector('.rf-item-desc') || {}).textContent || '';
			var keywords = item.getAttribute('data-keywords') || '';
			var haystack = (label + ' ' + desc + ' ' + keywords).toLowerCase();

			if (matchesTab && (!q || haystack.indexOf(q) !== -1)) {
				item.style.display = 'flex';
				visibleCount++;
			} else {
				item.style.display = 'none';
			}
		});

		// Hide empty groups
		allGroups.forEach(function (grp) {
			var groupItems = Array.from(grp.querySelectorAll('.rf-palette-item'));
			var hasVisible = groupItems.some(function (it) {
				return it.style.display !== 'none';
			});
			grp.style.display = hasVisible ? 'block' : 'none';
		});

		if (countEl) {
			countEl.textContent = visibleCount + ' item';
		}

		// Empty state handling
		if (emptyState) {
			if (visibleCount === 0) {
				emptyState.style.display = 'block';
				if (emptyQuery) emptyQuery.textContent = query ? '"' + query + '"' : 'kategori ini';
			} else {
				emptyState.style.display = 'none';
			}
		}

		focusedIndex = 0;
		updateFocusedItem(getVisibleItems());
	}

	// Quick filter tab clicks
	filterTabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			activeFilter = tab.getAttribute('data-filter') || 'all';
			filterTabs.forEach(function (t) {
				var active = t === tab;
				t.classList.toggle('is-active', active);
				t.setAttribute('aria-selected', String(active));
			});
			filterItems(input ? input.value : '');
			if (input) input.focus();
		});
	});

	// Global Keyboard Shortcut: Ctrl + K / Cmd + K
	document.addEventListener('keydown', function (e) {
		var isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
		var isCtrlK = (isMac ? e.metaKey : e.ctrlKey) && e.key.toLowerCase() === 'k';

		if (isCtrlK) {
			e.preventDefault();
			if (isOpen) {
				closePalette();
			} else {
				openPalette();
			}
			return;
		}

		if (e.key === 'Escape' && isOpen) {
			e.preventDefault();
			closePalette();
			return;
		}

		// Navigation inside open palette
		if (isOpen) {
			var visibleItems = getVisibleItems();

			if (e.key === 'ArrowDown') {
				e.preventDefault();
				focusedIndex = (focusedIndex + 1) % (visibleItems.length || 1);
				updateFocusedItem(visibleItems);
			} else if (e.key === 'ArrowUp') {
				e.preventDefault();
				focusedIndex = (focusedIndex - 1 + visibleItems.length) % (visibleItems.length || 1);
				updateFocusedItem(visibleItems);
			} else if (e.key === 'Enter') {
				e.preventDefault();
				if (visibleItems.length > 0 && visibleItems[focusedIndex]) {
					visibleItems[focusedIndex].click();
					closePalette();
				}
			}
		}
	});

	// Trigger button click
	if (triggerBtn) {
		triggerBtn.addEventListener('click', function (e) {
			e.preventDefault();
			openPalette();
		});
	}

	// Backdrop click to close (outside dialog)
	backdrop.addEventListener('click', function (e) {
		if (e.target === backdrop) {
			closePalette();
		}
	});

	// Search input typing
	if (input) {
		input.addEventListener('input', function (e) {
			filterItems(e.target.value);
		});
	}

	// Item hover to sync focus
	allItems.forEach(function (item) {
		item.addEventListener('mouseenter', function () {
			var visibleItems = getVisibleItems();
			var idx = visibleItems.indexOf(item);
			if (idx !== -1) {
				focusedIndex = idx;
				updateFocusedItem(visibleItems);
			}
		});

		item.addEventListener('click', function (e) {
			var href = item.getAttribute('href') || '';
			closePalette();
			if (href.startsWith('#') && !item.classList.contains('rf-preview-trigger')) {
				var targetEl = document.querySelector(href);
				if (targetEl) {
					e.preventDefault();
					targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
				}
			}
		});
	});

	// Robot head eye-tracking toward cursor (desktop pointer:fine + motion allowed)
	if (
		triggerBtn &&
		window.matchMedia('(pointer: fine)').matches &&
		window.matchMedia('(prefers-reduced-motion: no-preference)').matches
	) {
		var robotEyes = triggerBtn.querySelector('.rf-robot-eyes');
		if (robotEyes) {
			var eyeRaf = null;
			document.addEventListener('mousemove', function (e) {
				if (eyeRaf) return;
				eyeRaf = requestAnimationFrame(function () {
					eyeRaf = null;
					var rect = triggerBtn.getBoundingClientRect();
					var cx = rect.left + rect.width / 2;
					var cy = rect.top + rect.height / 2;
					var dx = Math.max(-2.5, Math.min(2.5, (e.clientX - cx) / 140));
					var dy = Math.max(-2.5, Math.min(2.5, (e.clientY - cy) / 140));
					robotEyes.style.transform = 'translate3d(' + dx.toFixed(2) + 'px, ' + dy.toFixed(2) + 'px, 0)';
				});
			}, { passive: true });
		}
	}
});
