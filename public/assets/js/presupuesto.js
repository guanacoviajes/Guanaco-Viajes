/* Guanaco Viajes · formulario de presupuesto por pasos.
   El formulario funciona sin JavaScript (todos los pasos visibles). Esto solo lo convierte en pasos. */
(function () {
  'use strict';

  var form = document.getElementById('pform');
  if (!form) { return; }

  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var N = 5;
  var steps = $$('.step', form);
  var cur = 1;
  var prefersReduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- chips, interruptor y consentimiento: estado visual sincronizado con las casillas ---------- */
  function syncChip(label) {
    var input = $('input', label);
    if (input) { label.classList.toggle('on', input.checked); }
  }
  $$('.chip, .tg, .chk', form).forEach(function (l) {
    var input = $('input', l);
    if (!input) { return; }
    syncChip(l);
    input.addEventListener('change', function () {
      if (input.type === 'radio') { $$('input[name="' + input.name + '"]', form).forEach(function (r) { syncChip(r.closest('label')); }); }
      else { syncChip(l); }
      onChange(input);
    });
  });

  /* «Aún no lo sé» excluye a los países, y al revés */
  var unknown = $('input[value="aun-no-lo-se"]', form);
  function onChange(input) {
    if (input.name === 'destinos[]') {
      if (input === unknown && unknown.checked) {
        $$('input[name="destinos[]"]', form).forEach(function (x) { if (x !== unknown) { x.checked = false; syncChip(x.closest('label')); } });
      } else if (input !== unknown && input.checked && unknown.checked) {
        unknown.checked = false; syncChip(unknown.closest('label'));
      }
      ajustaDuracion();
    }
    clearErrorsIn(input.closest('.step'));
  }

  /* Con algún destino de Sudamérica no se ofrece «Un fin de semana largo» */
  function sudamerica() {
    return $$('input[name="destinos[]"]:checked', form).some(function (i) { return i.getAttribute('data-cont') === 'sudamerica'; });
  }
  function ajustaDuracion() {
    var chip = document.getElementById('chipFinde');
    if (!chip) { return; }
    var sa = sudamerica(), inp = $('input', chip);
    chip.hidden = sa;
    if (sa && inp.checked) { inp.checked = false; syncChip(chip); }
  }

  /* ---------- viajeros ---------- */
  function ages() {
    var m = parseInt($('#menores').value, 10) || 0;
    $('#ages').hidden = m < 1;
  }
  $$('.sp button', form).forEach(function (b) {
    b.addEventListener('click', function () {
      var inp = document.getElementById(b.getAttribute('data-t'));
      var v = (parseInt(inp.value, 10) || 0) + parseInt(b.getAttribute('data-d'), 10);
      var min = parseInt(inp.min, 10), max = parseInt(inp.max, 10);
      inp.value = Math.max(min, Math.min(max, v));
      ages();
      clearErrorsIn(inp.closest('.step'));
    });
  });
  $('#menores').addEventListener('input', ages);

  /* ---------- errores ---------- */
  function setErr(id, msg) {
    var el = document.getElementById(id);
    if (!el) { return; }
    el.textContent = msg || '';
    el.classList.toggle('on', !!msg);
  }
  function clearErrorsIn(step) {
    if (!step) { return; }
    $$('.err.on', step).forEach(function (e) { e.classList.remove('on'); e.textContent = ''; });
    $$('.in.bad', step).forEach(function (e) { e.classList.remove('bad'); });
    $$('[aria-invalid]', step).forEach(function (e) { e.removeAttribute('aria-invalid'); });
  }
  function bad(input, id, msg) {
    setErr(id, msg);
    var box = input && input.closest('.in');
    if (box) { box.classList.add('bad'); }
    if (input) { input.setAttribute('aria-invalid', 'true'); }
  }

  /* ---------- validación por paso (la definitiva la hace el servidor) ---------- */
  function checked(sel) { return $$(sel, form).filter(function (i) { return i.checked; }); }
  function validate(n) {
    var ok = true;
    var step = steps[n - 1];
    clearErrorsIn(step);
    if (n === 1 && !checked('input[name="destinos[]"]').length) {
      setErr('e-destinos', 'Elige al menos una opción para continuar.'); ok = false;
    }
    if (n === 2) {
      if (!checked('input[name="meses[]"]').length && !$('input[name="flexible"]', form).checked) {
        setErr('e-fechas', 'Elige un mes o marca que eres flexible.'); ok = false;
      }
    }
    if (n === 3) {
      var a = parseInt($('#adultos').value, 10), m = parseInt($('#menores').value, 10);
      if (!(a >= 1 && a <= 20) || !(m >= 0 && m <= 10)) { setErr('e-viajeros', 'Revisa cuántos sois: entre 1 y 20 adultos y hasta 10 menores.'); ok = false; }
    }
    if (n === 5) {
      var nombre = $('#nombre'), email = $('#email'), tel = $('#telefono'), cons = $('input[name="consentimiento"]', form);
      if (nombre.value.trim().length < 2) { bad(nombre, 'e-nombre', 'Escribe tu nombre.'); ok = false; }
      if (!/^[^\s@,;<>]+@[^\s@,;<>]+\.[^\s@,;<>]+$/.test(email.value.trim())) { bad(email, 'e-email', 'Revisa el correo: parece que falta algo.'); ok = false; }
      if (tel.value.trim() !== '' && !/^[0-9 +().-]{6,20}$/.test(tel.value.trim())) { bad(tel, 'e-telefono', 'Revisa el teléfono: usa solo números, espacios y el prefijo con +.'); ok = false; }
      var canal = checked('input[name="canal"]')[0];
      if (canal && (canal.value === 'whatsapp' || canal.value === 'llamada') && tel.value.trim() === '' && ok) {
        bad(tel, 'e-telefono', 'Para escribirte por WhatsApp o llamarte necesitamos un teléfono.'); ok = false;
      }
      if (!cons.checked) { setErr('e-consentimiento', 'Necesitamos tu consentimiento para poder responderte.'); ok = false; }
    }
    return ok;
  }

  /* ---------- navegación ---------- */
  function show(n, focus) {
    cur = n;
    steps.forEach(function (s, i) { s.classList.toggle('on', i + 1 === n); });
    $$('.seg i').forEach(function (i, k) { i.classList.toggle('on', k < n); });
    var cnt = $('#cnt'); if (cnt) { cnt.textContent = 'Paso ' + n + ' de ' + N; }
    $$('.vsteps li').forEach(function (l) {
      var v = parseInt(l.getAttribute('data-v'), 10);
      l.classList.toggle('on', v === n);
      l.classList.toggle('done', v < n);
    });
    $('#back').classList.toggle('hide', n === 1);
    $('#next').hidden = n === N;
    $('#send').hidden = n !== N;
    if (n === 2) { ajustaDuracion(); }
    if (focus) {
      var f = $('.q', steps[n - 1]);
      if (f) { f.setAttribute('tabindex', '-1'); f.focus({ preventScroll: true }); }
      var top = form.getBoundingClientRect().top + window.pageYOffset - 90;
      if (window.pageYOffset > top) { window.scrollTo({ top: top, behavior: prefersReduce ? 'auto' : 'smooth' }); }
    }
  }

  $('#next').addEventListener('click', function () { if (validate(cur)) { show(cur + 1, true); } });
  $('#back').addEventListener('click', function () { if (cur > 1) { show(cur - 1, true); } });
  form.addEventListener('keydown', function (e) {
    // Intro avanza de paso en lugar de enviar el formulario antes de tiempo
    if (e.key === 'Enter' && cur < N && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'BUTTON') {
      e.preventDefault();
      if (validate(cur)) { show(cur + 1, true); }
    }
  });

  form.addEventListener('submit', function (e) {
    // Se validan todos los pasos; si alguno falla, se vuelve a él.
    for (var n = 1; n <= N; n++) {
      if (!validate(n)) { e.preventDefault(); show(n, true); return; }
    }
    var send = $('#send');
    send.disabled = true;           // evita el doble envío
    send.textContent = 'Enviando…';
  });

  /* ---------- destinos que llegan desde otra página (inicio, fichas de país) ---------- */
  var start = parseInt(form.getAttribute('data-start'), 10) || 1;
  try {
    var saved = JSON.parse(sessionStorage.getItem('gv_destinos') || 'null');
    sessionStorage.removeItem('gv_destinos');
    if (Array.isArray(saved) && saved.length && !checked('input[name="destinos[]"]').length && start === 1) {
      saved.forEach(function (slug) {
        var i = $('input[name="destinos[]"][value="' + String(slug).replace(/[^a-z0-9-]/g, '') + '"]', form);
        if (i) { i.checked = true; syncChip(i.closest('label')); }
      });
      if (checked('input[name="destinos[]"]').length) { start = saved.length === 1 && saved[0] === 'aun-no-lo-se' ? 4 : 2; }
    }
  } catch (x) { /* sin almacenamiento: se empieza por el paso 1 */ }

  form.classList.add('js');
  ages();
  ajustaDuracion();
  show(start, false);
})();
