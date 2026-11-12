/* Guanaco Viajes · comportamiento de la web pública (sin dependencias) */
(function () {
  'use strict';

  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- cabecera: transparente arriba, se oculta al bajar y reaparece al subir ---------- */
  var hdr = $('#site-header');
  if (hdr) {
    var overlay = hdr.classList.contains('is-overlay');
    var last = window.scrollY, ticking = false;
    var setTop = function (y) {
      if (!overlay) { return; }
      hdr.classList.toggle('at-top', y <= 8);
      hdr.classList.toggle('is-solid', y > 8);
    };
    var onScroll = function () {
      ticking = false;
      if (document.body.classList.contains('menu-open')) { return; }
      var y = window.scrollY;
      setTop(y);
      if (y <= 8) { hdr.classList.remove('is-hidden'); }
      else if (y > last + 4 && y > hdr.offsetHeight) { hdr.classList.add('is-hidden'); closeSubs(); }
      else if (y < last - 4) { hdr.classList.remove('is-hidden'); }
      last = y;
    };
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
    }, { passive: true });
    setTop(window.scrollY);
  }

  /* ---------- desplegables del menú ---------- */
  function closeSubs(except) {
    $$('.has-sub').forEach(function (li) {
      if (li === except) { return; }
      li.classList.remove('open');
      var b = $('.sub-toggle', li);
      if (b) { b.setAttribute('aria-expanded', 'false'); }
    });
  }
  $$('.has-sub').forEach(function (li) {
    var b = $('.sub-toggle', li);
    b.addEventListener('click', function () {
      var open = !li.classList.contains('open');
      closeSubs(li);
      li.classList.toggle('open', open);
      b.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    li.addEventListener('mouseenter', function () { b.setAttribute('aria-expanded', 'true'); });
    li.addEventListener('mouseleave', function () {
      if (!li.classList.contains('open')) { b.setAttribute('aria-expanded', 'false'); }
    });
  });

  /* ---------- panel de menú en móvil ---------- */
  var burger = $('.burger'), panel = $('#mpanel');
  function setPanel(open) {
    if (!burger || !panel) { return; }
    panel.hidden = !open;
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    burger.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
    document.body.classList.toggle('menu-open', open);
  }
  if (burger && panel) {
    burger.addEventListener('click', function () { setPanel(panel.hidden); });
    window.addEventListener('resize', function () { if (window.innerWidth > 980) { setPanel(false); } });
  }

  /* ---------- burbuja de contacto ---------- */
  var bubble = $('.bubble'), bmenu = $('#bubble-menu');
  function setBubble(open) {
    if (!bubble || !bmenu) { return; }
    bmenu.hidden = !open;
    bubble.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  if (bubble && bmenu) {
    bubble.addEventListener('click', function (e) { e.stopPropagation(); setBubble(bmenu.hidden); });
    document.addEventListener('click', function (e) { if (!e.target.closest('#bubble')) { setBubble(false); } });
  }

  document.addEventListener('click', function (e) { if (!e.target.closest('.has-sub')) { closeSubs(); } });
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') { return; }
    closeSubs(); setBubble(false); setPanel(false);
  });

  /* ---------- pestañas accesibles (flechas, Inicio, Fin) ---------- */
  function initTabs(list, onChange) {
    var tabs = $$('[role="tab"]', list);
    function select(t, focus) {
      tabs.forEach(function (x) {
        var on = x === t;
        x.classList.toggle('on', on);
        x.setAttribute('aria-selected', on ? 'true' : 'false');
        x.tabIndex = on ? 0 : -1;
      });
      if (focus) { t.focus(); }
      onChange(t, tabs.indexOf(t));
    }
    tabs.forEach(function (t) {
      t.addEventListener('click', function () { select(t, false); });
      t.addEventListener('keydown', function (e) {
        var i = tabs.indexOf(t), n = null;
        if (e.key === 'ArrowRight') { n = tabs[(i + 1) % tabs.length]; }
        else if (e.key === 'ArrowLeft') { n = tabs[(i - 1 + tabs.length) % tabs.length]; }
        else if (e.key === 'Home') { n = tabs[0]; }
        else if (e.key === 'End') { n = tabs[tabs.length - 1]; }
        if (n) { e.preventDefault(); select(n, true); }
      });
    });
  }

  /* Tipos de viaje: cambia descripción y foto */
  var tvTabs = $('.tv-tabs');
  if (tvTabs) {
    initTabs(tvTabs, function (t, idx) {
      var key = t.getAttribute('data-t');
      $$('.tv-panel').forEach(function (p) {
        var on = p.getAttribute('data-p') === key;
        p.classList.toggle('on', on);
        p.hidden = !on;
      });
      $$('.tv-img img').forEach(function (im, i) { im.classList.toggle('on', i === idx); });
    });
  }

  /* Destinos: Sudamérica / Europa */
  var rails = [];
  var dTabs = $('[data-tabs="destinos"]');
  if (dTabs) {
    initTabs(dTabs, function (t) {
      var key = t.getAttribute('data-s');
      $$('.set').forEach(function (s) {
        var on = s.getAttribute('data-s') === key;
        s.classList.toggle('on', on);
        s.hidden = !on;
      });
      rails.forEach(function (r) { r(); });
    });
  }

  /* Carriles de tarjetas con flechas (móvil) */
  $$('.rail').forEach(function (rail) {
    var c = $('.cards', rail), l = $('.arr.l', rail), n = $('.arr.r', rail);
    if (!c || !l || !n) { return; }
    function upd() {
      var over = c.scrollWidth > c.clientWidth + 2;
      l.classList.toggle('hide', !over || c.scrollLeft < 30);
      n.classList.toggle('hide', !over || c.scrollLeft + c.clientWidth >= c.scrollWidth - 8);
    }
    function go(d) { c.scrollBy({ left: d * c.clientWidth * 0.74, behavior: reduce ? 'auto' : 'smooth' }); }
    l.addEventListener('click', function () { go(-1); });
    n.addEventListener('click', function () { go(1); });
    c.addEventListener('scroll', upd, { passive: true });
    window.addEventListener('resize', upd);
    rails.push(upd);
    upd();
  });

  /* ---------- carrusel de inicio ---------- */
  var car = $('#carousel');
  if (car) {
    var slides = $$('.slide', car);
    var dots = $$('.cdots button:not(.cpause)', car);
    var pauseBtn = $('.cpause', car);
    var idx = 0, timer = null, paused = reduce, hover = false;
    var secs = parseFloat(car.getAttribute('data-interval')) || 4;
    var ms = Math.max(2, secs) * 1000;

    var show = function (n) {
      idx = (n + slides.length) % slides.length;
      slides.forEach(function (s, i) {
        var on = i === idx;
        s.classList.toggle('is-active', on);
        s.inert = !on;
        if (on) { s.removeAttribute('aria-hidden'); } else { s.setAttribute('aria-hidden', 'true'); }
      });
      dots.forEach(function (d, i) {
        if (i === idx) { d.setAttribute('aria-current', 'true'); } else { d.removeAttribute('aria-current'); }
      });
      if (hdr) { hdr.setAttribute('data-tone', slides[idx].getAttribute('data-tone') || 'oscuro'); }
    };
    var stop = function () { if (timer) { clearInterval(timer); timer = null; } };
    var play = function () {
      stop();
      if (paused || hover || slides.length < 2 || document.hidden) { return; }
      timer = setInterval(function () { show(idx + 1); }, ms);
    };
    var setPaused = function (p) {
      paused = p;
      if (pauseBtn) {
        pauseBtn.setAttribute('data-state', p ? 'paused' : 'playing');
        pauseBtn.setAttribute('aria-label', p ? 'Reanudar la reproducción automática' : 'Pausar la reproducción automática');
      }
      play();
    };

    // Elegir una diapositiva (punto o gesto) no detiene el carrusel: solo reinicia la cuenta atrás.
    // Solo el botón de pausa lo detiene de forma permanente.
    dots.forEach(function (d, i) { d.addEventListener('click', function () { show(i); play(); }); });
    if (pauseBtn) { pauseBtn.addEventListener('click', function () { setPaused(!paused); }); }
    // La pausa al pasar el ratón solo cuenta con ratón: en el móvil un toque no debe dejar el carrusel parado.
    car.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') { hover = true; stop(); } });
    car.addEventListener('pointerleave', function (e) { if (e.pointerType === 'mouse') { hover = false; play(); } });
    // Con teclado, el foco dentro del carrusel lo pausa (para poder leer); con toque o ratón no.
    var byKeyboard = false;
    document.addEventListener('keydown', function () { byKeyboard = true; }, true);
    document.addEventListener('pointerdown', function () { byKeyboard = false; }, true);
    car.addEventListener('focusin', function () { if (byKeyboard) { hover = true; stop(); } });
    car.addEventListener('focusout', function () { hover = false; play(); });
    document.addEventListener('visibilitychange', play);

    // Deslizar con el dedo: izquierda = siguiente, derecha = anterior. El carrusel es infinito.
    var sx = 0, sy = 0, tracking = false, swiped = false;
    car.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'mouse') { return; }
      tracking = true; swiped = false; sx = e.clientX; sy = e.clientY;
    });
    car.addEventListener('pointerup', function (e) {
      if (!tracking) { return; }
      tracking = false;
      var dx = e.clientX - sx, dy = e.clientY - sy;
      if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.4) {
        swiped = true;
        show(idx + (dx < 0 ? 1 : -1));
        play();
      }
    });
    car.addEventListener('pointercancel', function () { tracking = false; });
    car.addEventListener('click', function (e) {
      if (swiped) { e.preventDefault(); e.stopPropagation(); swiped = false; }
    }, true);

    show(0);
    setPaused(paused);
  }

  /* ---------- primer paso del formulario en la home ---------- */
  var hf = $('#home-form');
  if (hf) {
    var chips = $$('.chip', hf), err = $('#hf-err');
    chips.forEach(function (c) {
      c.addEventListener('click', function () {
        var on = c.getAttribute('aria-pressed') !== 'true';
        var unknown = c.getAttribute('data-pais') === 'aun-no-lo-se';
        chips.forEach(function (x) {
          var xUnknown = x.getAttribute('data-pais') === 'aun-no-lo-se';
          if (unknown ? x !== c : xUnknown) { x.setAttribute('aria-pressed', 'false'); }
        });
        c.setAttribute('aria-pressed', on ? 'true' : 'false');
        if (err) { err.classList.remove('on'); }
      });
    });
    hf.addEventListener('submit', function (e) {
      e.preventDefault();
      var sel = chips.filter(function (c) { return c.getAttribute('aria-pressed') === 'true'; })
        .map(function (c) { return c.getAttribute('data-pais'); });
      if (!sel.length) { if (err) { err.classList.add('on'); } return; }
      try { sessionStorage.setItem('gv_destinos', JSON.stringify(sel)); } catch (x) { /* sin almacenamiento */ }
      window.location.href = '/presupuesto';
    });
  }
})();
