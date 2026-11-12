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

  /* evita el doble clic al guardar */
  $$('form[data-once]').forEach(function (f) {
    f.addEventListener('submit', function () {
      var b = $('button[type=submit]', f);
      if (b) { setTimeout(function () { b.disabled = true; b.textContent = 'Guardando…'; }, 0); }
    });
  });
})();
