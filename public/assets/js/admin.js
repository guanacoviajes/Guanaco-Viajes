/* Panel de administración · comportamiento mínimo (sin dependencias) */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* menú en móvil */
  var burger = $('.adm-burger'), scrim = $('.adm-scrim');
  function nav(open) {
    document.body.classList.toggle('nav-open', open);
    if (burger) { burger.setAttribute('aria-expanded', open ? 'true' : 'false'); }
    if (scrim) { scrim.hidden = !open; }
  }
  if (burger) { burger.addEventListener('click', function () { nav(!document.body.classList.contains('nav-open')); }); }
  if (scrim) { scrim.addEventListener('click', function () { nav(false); }); }
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { nav(false); } });

  /* confirmación antes de borrar o de acciones delicadas */
  $$('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!window.confirm(f.getAttribute('data-confirm'))) { e.preventDefault(); }
    });
  });

  /* copiar texto (códigos de recuperación) */
  $$('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var el = $(b.getAttribute('data-copy'));
      if (!el || !navigator.clipboard) { return; }
      navigator.clipboard.writeText(el.innerText.trim().replace(/\s*\n\s*/g, '\n')).then(function () { b.textContent = 'Copiado'; });
    });
  });

  /* contador de caracteres en los campos con límite */
  $$('textarea[maxlength], input[maxlength][data-count]').forEach(function (el) {
    var c = document.createElement('div');
    c.className = 'counter';
    el.parentNode.appendChild(c);
    var upd = function () { c.textContent = el.value.length + ' / ' + el.maxLength; };
    el.addEventListener('input', upd); upd();
  });

  /* el «slug» se propone a partir del nombre mientras no se haya tocado a mano */
  var from = $('[data-slug-from]'), to = $('[data-slug-to]');
  if (from && to) {
    var touched = to.value !== '';
    to.addEventListener('input', function () { touched = true; });
    from.addEventListener('input', function () {
      if (touched) { return; }
      to.value = from.value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    });
  }

  /* vista previa de la imagen elegida antes de subirla */
  $$('input[type=file][data-preview]').forEach(function (inp) {
    inp.addEventListener('change', function () {
      var img = $(inp.getAttribute('data-preview'));
      if (img && inp.files && inp.files[0]) { img.src = URL.createObjectURL(inp.files[0]); img.hidden = false; }
    });
  });

  /* bloques repetibles (zonas, cuándo ir, preguntas): con JavaScript se ve una casilla y un botón «Añadir otra» */
  $$('[data-blocks]').forEach(function (set) {
    var slots = $$('[data-slot]', set), add = $('[data-add]', set);
    var lastFilled = -1;
    slots.forEach(function (sl, i) { if (sl.getAttribute('data-filled') === '1') { lastFilled = i; } });
    var visible = Math.max(1, lastFilled + 1);
    function paint() {
      slots.forEach(function (sl, i) {
        sl.hidden = i >= visible;
        // los huecos ocultos no se envían (así no se acumulan archivos vacíos en el envío)
        $$('input,textarea', sl).forEach(function (el) { el.disabled = i >= visible; });
      });
      if (add) { add.hidden = visible >= slots.length; }
    }
    if (add) {
      add.addEventListener('click', function () {
        if (visible < slots.length) {
          visible += 1; paint();
          var first = $('input,textarea', slots[visible - 1]);
          if (first) { first.focus(); }
        }
      });
    }
    paint();
  });

  /* contador de caracteres junto a los campos con límite estricto */
  $$('[data-count]').forEach(function (el) {
    var max = parseInt(el.getAttribute('data-count'), 10) || 0;
    var out = document.createElement('small');
    out.className = 'counter'; out.setAttribute('aria-live', 'polite');
    el.parentNode.appendChild(out);
    function upd() {
      var n = el.value.length;
      out.textContent = n + ' / ' + max;
      out.classList.toggle('over', n >= max);
    }
    el.addEventListener('input', upd); upd();
  });

  /* países de un artículo: se recuerda el orden en que se marcan; el primero es el principal */
  $$('[data-multicheck]').forEach(function (set) {
    var name = set.getAttribute('data-name');
    var box = $('[data-order]', set);
    var order = $$('input[type=hidden]', box).map(function (h) { return h.value; });
    function paint() {
      $$('.mc-item', set).forEach(function (lab) {
        var inp = $('input[type=checkbox]', lab), badge = $('.mc-main', lab);
        badge.hidden = !(inp.checked && order[0] === inp.value);
      });
    }
    function write() {
      while (box.firstChild) { box.removeChild(box.firstChild); }
      order.forEach(function (v) {
        var h = document.createElement('input');
        h.type = 'hidden'; h.name = name + '_orden[]'; h.value = v; box.appendChild(h);
      });
      paint();
    }
    $$('input[type=checkbox]', set).forEach(function (inp) {
      if (inp.checked && order.indexOf(inp.value) === -1) { order.push(inp.value); }
      inp.addEventListener('change', function () {
        var i = order.indexOf(inp.value);
        if (inp.checked && i === -1) { order.push(inp.value); }
        if (!inp.checked && i !== -1) { order.splice(i, 1); }
        write();
      });
    });
    order = order.filter(function (v) { var c = $('input[type=checkbox][value="' + v + '"]', set); return c && c.checked; });
    write();
  });

  /* evita el doble clic al guardar */
  $$('form[data-once]').forEach(function (f) {
    f.addEventListener('submit', function () {
      var b = $('button[type=submit]', f);
      if (b) { setTimeout(function () { b.disabled = true; b.textContent = 'Guardando…'; }, 0); }
    });
  });
})();
