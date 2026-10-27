
(function () {
	'use strict';

	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var pad = function (n) { return (n < 10 ? '0' : '') + n; };
	var store = {
		get: function (k) { try { return window.sessionStorage.getItem(k); } catch (e) { return null; } },
		set: function (k, v) { try { window.sessionStorage.setItem(k, v); } catch (e) {  } }
	};

	var toggle = $('[data-nav-toggle]');
	var nav = $('[data-nav]');
	if (toggle && nav) {
		var setNav = function (open) {
			nav.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		};
		toggle.addEventListener('click', function () { setNav(!nav.classList.contains('is-open')); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) { setNav(false); toggle.focus(); }
		});
		nav.addEventListener('click', function (e) { if (e.target.closest('a')) { setNav(false); } });
	}

	var banner = $('[data-announce]');
	if (banner) {
		if (store.get('gm_announce') === '1') { banner.classList.add('is-hidden'); }
		var close = $('[data-announce-close]', banner);
		if (close) {
			close.addEventListener('click', function () { banner.classList.add('is-hidden'); store.set('gm_announce', '1'); });
		}
	}

	$$('[data-slider]').forEach(function (root) {
		var track = $('.slider__track', root);
		var slides = $$('[data-slide]', track);
		var prev = $('[data-slider-prev]', root);
		var next = $('[data-slider-next]', root);
		var cur = $('[data-slider-current]', root);
		if (!track || !slides.length) { return; }

		var step = function () {
			var a = slides[0], b = slides[1];
			return b ? b.offsetLeft - a.offsetLeft : a.offsetWidth;
		};
		var update = function () {
			var s = step() || 1;
			var idx = Math.round(track.scrollLeft / s);
			var max = track.scrollWidth - track.clientWidth - 2;
			if (cur) { cur.textContent = pad(Math.min(slides.length, idx + 1)); }
			if (prev) { prev.disabled = track.scrollLeft <= 2; }
			if (next) { next.disabled = track.scrollLeft >= max; }
		};
		if (prev) { prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); }); }
		if (next) { next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); }); }
		track.addEventListener('scroll', function () { window.requestAnimationFrame(update); }, { passive: true });
		track.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowRight') { track.scrollBy({ left: step(), behavior: 'smooth' }); }
			if (e.key === 'ArrowLeft') { track.scrollBy({ left: -step(), behavior: 'smooth' }); }
		});
		window.addEventListener('resize', update);
		update();
	});

	$$('[data-faq-toggle]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var panel = document.getElementById(btn.getAttribute('aria-controls'));
			var open = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', open ? 'false' : 'true');
			btn.textContent = open ? btn.getAttribute('data-more') : btn.getAttribute('data-less');
			if (panel) { panel.hidden = open; }
		});
	});

	$$('[data-gallery]').forEach(function (g) {
		var figs = $$('[data-img]', g);
		var thumbs = $$('[data-thumb]', g);
		var dots = $$('.gallery__dots span', g);
		var n = figs.length;
		var i = 0;
		var render = function () {
			figs.forEach(function (f, k) { f.classList.toggle('is-on', k === i || k === (i + 1) % n); });
			if (n < 2) { figs[0].classList.add('is-on'); }
			thumbs.forEach(function (t, k) { t.setAttribute('aria-selected', k === i ? 'true' : 'false'); });
			dots.forEach(function (d, k) { d.classList.toggle('is-on', k === i); });
		};
		var go = function (k) { i = (k + n) % n; render(); };
		var p = $('[data-gallery-prev]', g), nx = $('[data-gallery-next]', g);
		if (p) { p.addEventListener('click', function () { go(i - 1); }); }
		if (nx) { nx.addEventListener('click', function () { go(i + 1); }); }
		thumbs.forEach(function (t, k) { t.addEventListener('click', function () { go(k); }); });
		g.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowRight') { go(i + 1); }
			if (e.key === 'ArrowLeft') { go(i - 1); }
		});
	});

	var filter = $('[data-office-filter]');
	if (filter) {
		var offices = $$('.office');
		$$('[data-filter]', filter).forEach(function (btn) {
			btn.addEventListener('click', function () {
				var f = btn.getAttribute('data-filter');
				$$('[data-filter]', filter).forEach(function (b) {
					var on = b === btn;
					b.classList.toggle('is-active', on);
					b.setAttribute('aria-pressed', on ? 'true' : 'false');
				});
				offices.forEach(function (o) {
					var types = (o.getAttribute('data-type') || '').split(' ');
					o.hidden = !(f === 'all' || types.indexOf(f) > -1);
				});
			});
		});
	}

	$$('[data-autosubmit]').forEach(function (s) {
		s.addEventListener('change', function () { s.form.submit(); });
	});

	$$('.select select').forEach(function (s) {
		var sync = function () { if (s.value) { s.removeAttribute('data-empty'); } else { s.setAttribute('data-empty', ''); } };
		s.addEventListener('change', sync);
		sync();
	});

	var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	$$('[data-gm-form]').forEach(function (form) {
		var status = $('.form__status', form);
		var say = function (type, msg) {
			if (!status) { return; }
			status.innerHTML = '';
			var p = document.createElement('p');
			p.className = 'notice notice--' + type;
			p.textContent = msg;
			status.appendChild(p);
		};

		form.addEventListener('submit', function (e) {
			var bad = [];
			$$('input, textarea, select', form).forEach(function (el) {
				if (el.type === 'hidden' || el.closest('.hp')) { return; }
				var wrap = el.closest('.field') || el.closest('.check');
				var isCheck = el.type === 'checkbox';
				var msg = '';
				if (el.required && (isCheck ? !el.checked : !el.value.trim())) {
					msg = isCheck ? 'Please accept the terms to continue.' : 'This field is required.';
				} else if (el.type === 'email' && el.value && !emailRe.test(el.value)) {
					msg = 'Enter a valid email address.';
				}
				var errId = el.id ? el.id + '-error' : '';
				var old = errId && document.getElementById(errId);
				if (old) { old.remove(); }
				if (wrap) { wrap.classList.toggle('has-error', !!msg); }
				el.setAttribute('aria-invalid', msg ? 'true' : 'false');
				if (msg && errId && !isCheck) {
					var err = document.createElement('p');
					err.className = 'field__error';
					err.id = errId;
					err.textContent = msg;
					(el.closest('.select') || el).insertAdjacentElement('afterend', err);
					el.setAttribute('aria-describedby', errId);
				} else {
					el.removeAttribute('aria-describedby');
				}
				if (msg) { bad.push(el); }
			});
			if (bad.length) {
				e.preventDefault();
				say('error', 'Please complete the highlighted fields.');
				bad[0].focus();
				return;
			}
			if (!window.fetch || !window.FormData) { return; }
			e.preventDefault();
			var btn = $('button[type="submit"]', form);
			var label = btn ? btn.textContent : '';
			if (btn) { btn.disabled = true; btn.textContent = (window.gmData && window.gmData.sending) || 'Sending…'; }
			var data = new FormData(form);
			data.set('action', 'gm_submit');
			fetch((window.gmData && window.gmData.ajax) || form.action, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (res && res.success) {
						say('ok', res.data.message);
						form.reset();
						$$('.select select', form).forEach(function (s) { s.setAttribute('data-empty', ''); });
					} else {
						say('error', (res && res.data && res.data.message) || 'Something went wrong. Please try again.');
					}
				})
				.catch(function () { say('error', 'Network error. Please try again.'); })
				.then(function () { if (btn) { btn.disabled = false; btn.textContent = label; } });
		});
	});

	if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
		$$('.section-head, .card, .tile, .step, .stat').forEach(function (el) {
			if (el.getBoundingClientRect().top > window.innerHeight) {
				el.classList.add('reveal');
				var i = Array.prototype.indexOf.call(el.parentNode.children, el);
				if (i > 0) { el.style.transitionDelay = Math.min(i, 4) * 60 + 'ms'; }
				io.observe(el);
			}
		});
	}

	var top = $('[data-to-top]');
	if (top) {
		var onScroll = function () { top.hidden = window.scrollY < 800; };
		window.addEventListener('scroll', onScroll, { passive: true });
		top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
		onScroll();
	}
}());
