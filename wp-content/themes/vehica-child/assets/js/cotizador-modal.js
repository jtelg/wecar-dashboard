(function () {
  "use strict";

  var KEY_DRAFT = "wecar_cotizador_draft";
  var KEY_SUBMISSIONS = "wecar_cotizador_submissions";
  var TABBABLE_SELECTOR = "a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex='-1'])";

  var FIELD_NAMES = [
    "nombre", "mail", "telefono", "localidad",
    "anio", "marca", "modelo", "kilometros",
    "gnc", "dia", "horario"
  ];

  var STEP_FIELDS = {
    1: ["nombre", "mail", "telefono", "localidad"],
    2: ["anio", "marca", "modelo", "kilometros", "gnc"],
    3: ["dia", "horario"]
  };

  var ERROR_MESSAGES = {
    required: "Este campo es obligatorio",
    mail: "Ingresá un mail válido",
    telefono: "Ingresá un teléfono válido",
    anio: "Ingresá un año válido",
    kilometros: "Ingresá una cantidad válida de kilómetros",
    gnc: "Seleccioná una opción",
    dia: "Seleccioná un día",
    horario: "Seleccioná un horario"
  };

  // QA 6.1-6.3 checkpoints:
  // - open/close + focus trap: openModal / closeModal / trapFocus
  // - validation + button gates: validateField / validateStep / updateButtonState
  // - persistence: loadDraft / persistDraft / submitForm
  // - responsive/a11y: cotizador-modal.css media queries and aria-* attributes in page-cotizador.php

  var modal;
  var trigger;
  var steps;
  var progressBars;
  var successScreen;
  var currentStep = 1;
  var lastFocusedTrigger = null;
  var isOpen = false;

  var fields = {
    nombre: "",
    mail: "",
    telefono: "",
    localidad: "",
    anio: "",
    marca: "",
    modelo: "",
    kilometros: "",
    gnc: "",
    dia: "",
    horario: ""
  };

  var currentYear = new Date().getFullYear();

  function ready(fn) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn);
    } else {
      fn();
    }
  }

  function getState() {
    return { step: currentStep, fields: fields };
  }

  function loadDraft() {
    try {
      var raw = window.localStorage.getItem(KEY_DRAFT);
      if (raw) {
        var parsed = JSON.parse(raw);
        if (parsed && typeof parsed === "object") {
          if (typeof parsed.step === "number" && parsed.step >= 1 && parsed.step <= 3) {
            currentStep = parsed.step;
          }
          if (parsed.fields && typeof parsed.fields === "object") {
            FIELD_NAMES.forEach(function (name) {
              if (typeof parsed.fields[name] === "string") {
                fields[name] = parsed.fields[name];
              }
            });
          }
        }
      }
    } catch (err) {
      if (window.console && window.console.warn) {
        window.console.warn("[wecar-cotizador] No se pudo leer el borrador:", err.message);
      }
    }
  }

  function persistDraft() {
    try {
      window.localStorage.setItem(KEY_DRAFT, JSON.stringify(getState()));
    } catch (err) {
      if (window.console && window.console.warn) {
        window.console.warn("[wecar-cotizador] No se pudo guardar el borrador:", err.message);
      }
    }
  }

  function getTabbable() {
    if (!modal) return [];
    var visible = modal.querySelector(".wecar-cotizador-modal__step--active") ||
                  modal.querySelector(".wecar-cotizador-modal__success.is-active");
    if (!visible) return [];
    return Array.prototype.slice.call(visible.querySelectorAll(TABBABLE_SELECTOR))
      .filter(function (el) {
        return el.offsetParent !== null && !el.disabled && el.tabIndex >= 0;
      });
  }

  function trapFocus(event) {
    if (!isOpen || event.key !== "Tab") return;

    var tabbable = getTabbable();
    if (tabbable.length === 0) {
      event.preventDefault();
      return;
    }

    var first = tabbable[0];
    var last = tabbable[tabbable.length - 1];

    if (event.shiftKey) {
      if (document.activeElement === first || !modal.contains(document.activeElement)) {
        event.preventDefault();
        last.focus();
      }
    } else {
      if (document.activeElement === last || !modal.contains(document.activeElement)) {
        event.preventDefault();
        first.focus();
      }
    }
  }

  function handleKeydown(event) {
    if (event.key === "Escape") {
      closeModal();
    } else if (event.key === "Tab") {
      trapFocus(event);
    }
  }

  function updateProgress(step) {
    progressBars.forEach(function (bar) {
      var barStep = parseInt(bar.getAttribute("data-wecar-progress") || "0", 10);
      bar.classList.remove(
        "wecar-cotizador-modal__progress-bar--active",
        "wecar-cotizador-modal__progress-bar--completed"
      );
      if (barStep < step) {
        bar.classList.add("wecar-cotizador-modal__progress-bar--completed");
      } else if (barStep === step) {
        bar.classList.add("wecar-cotizador-modal__progress-bar--active");
      }
    });

    var progress = modal.querySelector(".wecar-cotizador-modal__progress");
    if (progress) {
      progress.setAttribute("aria-valuenow", String(step));
    }
  }

  function focusFirstActiveField() {
    var activeStep = modal.querySelector(".wecar-cotizador-modal__step--active");
    if (!activeStep) return;

    var firstField = activeStep.querySelector(
      "input:not([type='hidden']), " +
      "button:not([disabled]), " +
      "[tabindex]:not([tabindex='-1'])"
    );
    if (firstField) {
      window.setTimeout(function () {
        firstField.focus();
      }, 0);
    }
  }

  function showStep(step) {
    if (step < 1 || step > 3) return;

    hideInlineError();

    steps.forEach(function (panel) {
      var panelStep = parseInt(panel.getAttribute("data-wecar-step") || "0", 10);
      if (panelStep === currentStep && panelStep !== step) {
        panel.classList.add("wecar-cotizador-modal__step--leave");
        window.setTimeout(function () {
          panel.classList.remove("wecar-cotizador-modal__step--leave");
        }, 200);
      }
      panel.classList.toggle("wecar-cotizador-modal__step--active", panelStep === step);
      panel.setAttribute("aria-hidden", panelStep === step ? "false" : "true");
    });

    if (successScreen) {
      successScreen.classList.remove("is-active");
      successScreen.setAttribute("aria-hidden", "true");
    }

    currentStep = step;
    updateProgress(currentStep);
    modal.setAttribute("aria-labelledby", "wecar-cotizador-step-title-" + currentStep);

    updateButtonState();
    focusFirstActiveField();
  }

  function openModal() {
    if (!modal || isOpen) return;

    lastFocusedTrigger = document.activeElement;
    isOpen = true;

    loadDraft();
    restoreFieldsToDOM();
    showStep(currentStep);

    modal.classList.add("is-open");
    document.body.classList.add("wecar-cotizador-modal-is-open");

    document.addEventListener("keydown", handleKeydown);
  }

  function closeModal() {
    if (!modal || !isOpen) return;

    isOpen = false;
    modal.classList.remove("is-open");
    document.body.classList.remove("wecar-cotizador-modal-is-open");
    document.removeEventListener("keydown", handleKeydown);

    persistDraft();

    if (lastFocusedTrigger && typeof lastFocusedTrigger.focus === "function") {
      window.setTimeout(function () {
        lastFocusedTrigger.focus();
      }, 0);
    }
  }

  function getInputForField(name) {
    return modal.querySelector('input[data-wecar-field="' + name + '"]');
  }

  function getErrorForField(name) {
    return modal.querySelector('[data-wecar-error="' + name + '"]');
  }

  function validateField(name) {
    var value = (fields[name] || "").trim();

    if (STEP_FIELDS[1].indexOf(name) !== -1 || STEP_FIELDS[2].indexOf(name) !== -1 || STEP_FIELDS[3].indexOf(name) !== -1) {
      if (value === "") {
        return ERROR_MESSAGES.required;
      }
    }

    if (name === "mail") {
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        return ERROR_MESSAGES.mail;
      }
    } else if (name === "telefono") {
      if (!/^[\d\s+\-()]{6,20}$/.test(value)) {
        return ERROR_MESSAGES.telefono;
      }
    } else if (name === "anio") {
      if (!/^\d{4}$/.test(value)) {
        return ERROR_MESSAGES.anio;
      }
      var yearNum = parseInt(value, 10);
      if (yearNum < 1900 || yearNum > currentYear + 1) {
        return ERROR_MESSAGES.anio;
      }
    } else if (name === "kilometros") {
      if (!/^\d+$/.test(value)) {
        return ERROR_MESSAGES.kilometros;
      }
      var kmNum = parseInt(value, 10);
      if (kmNum < 1 || kmNum > 9999999) {
        return ERROR_MESSAGES.kilometros;
      }
    } else if (name === "gnc") {
      if (["instalado", "tuvo", "nunca"].indexOf(value) === -1) {
        return ERROR_MESSAGES.gnc;
      }
    }

    return null;
  }

  function validateStep(step) {
    var required = STEP_FIELDS[step];
    if (!required) return false;
    for (var i = 0; i < required.length; i++) {
      if (validateField(required[i]) !== null) return false;
    }
    return true;
  }

  function showFieldError(name, message) {
    var input = getInputForField(name);
    var errorEl = getErrorForField(name);

    if (input) {
      input.classList.add("is-error");
    }

    var group = modal.querySelector('[data-wecar-field="' + name + '"]').closest(
      ".wecar-cotizador-modal__radio-group, .wecar-cotizador-modal__pills"
    );
    if (group) {
      group.classList.add("is-error");
    }

    if (errorEl) {
      errorEl.textContent = message || "";
    }
  }

  function clearFieldError(name) {
    var input = getInputForField(name);
    var errorEl = getErrorForField(name);

    if (input) {
      input.classList.remove("is-error");
    }

    var firstControl = modal.querySelector('[data-wecar-field="' + name + '"]');
    if (firstControl) {
      var group = firstControl.closest(
        ".wecar-cotizador-modal__radio-group, .wecar-cotizador-modal__pills"
      );
      if (group) {
        group.classList.remove("is-error");
      }
    }

    if (errorEl) {
      errorEl.textContent = "";
    }
  }

  function updateButtonState() {
    var activeStep = modal.querySelector(".wecar-cotizador-modal__step--active");
    if (!activeStep) return;

    var actionBtn = activeStep.querySelector(
      '[data-wecar-action="next"], [data-wecar-action="submit"]'
    );
    if (!actionBtn) return;

    var isValid = validateStep(currentStep);
    actionBtn.disabled = !isValid;
  }

  function restoreFieldsToDOM() {
    STEP_FIELDS[1].forEach(function (name) {
      var input = getInputForField(name);
      if (input) input.value = fields[name] || "";
    });
    STEP_FIELDS[2].forEach(function (name) {
      if (name === "gnc") return;
      var input = getInputForField(name);
      if (input) input.value = fields[name] || "";
    });

    updateRadioUI("gnc");
    updatePillUI("dia");
    updatePillUI("horario");

    FIELD_NAMES.forEach(function (name) {
      clearFieldError(name);
    });
  }

  function updateRadioUI(name) {
    var value = fields[name] || "";
    var buttons = Array.prototype.slice.call(
      modal.querySelectorAll('[data-wecar-field="' + name + '"]')
    );
    buttons.forEach(function (btn) {
      var selected = btn.getAttribute("data-wecar-value") === value;
      btn.classList.toggle("is-selected", selected);
      btn.setAttribute("aria-checked", String(selected));

      var icon = btn.querySelector(".wecar-cotizador-modal__radio-icon");
      if (icon) {
        var nextSrc = selected
          ? icon.getAttribute("data-wecar-checked-src")
          : icon.getAttribute("data-wecar-unchecked-src");
        if (nextSrc) icon.src = nextSrc;
      }
    });
  }

  function updatePillUI(name) {
    var value = fields[name] || "";
    var pills = Array.prototype.slice.call(
      modal.querySelectorAll('[data-wecar-field="' + name + '"]')
    );
    pills.forEach(function (pill) {
      var selected = pill.getAttribute("data-wecar-value") === value;
      pill.classList.toggle("is-selected", selected);
      pill.setAttribute("aria-pressed", String(selected));
    });
  }

  function handleInput(event) {
    var input = event.target.closest("[data-wecar-field]");
    if (!input || input.tagName.toLowerCase() !== "input") return;

    var name = input.getAttribute("data-wecar-field");
    fields[name] = input.value;
    clearFieldError(name);
    persistDraft();
    updateButtonState();
  }

  function handleBlur(event) {
    var input = event.target.closest("[data-wecar-field]");
    if (!input || input.tagName.toLowerCase() !== "input") return;

    var name = input.getAttribute("data-wecar-field");
    var error = validateField(name);
    if (error) {
      showFieldError(name, error);
    }
    updateButtonState();
  }

  function handleRadioClick(event) {
    var radio = event.target.closest('[data-wecar-field="gnc"]');
    if (!radio) return;

    var value = radio.getAttribute("data-wecar-value");
    fields.gnc = value;
    updateRadioUI("gnc");
    clearFieldError("gnc");
    persistDraft();
    updateButtonState();
  }

  function handlePillClick(event) {
    var pill = event.target.closest('[data-wecar-field]');
    if (!pill) return;

    var name = pill.getAttribute("data-wecar-field");
    if (name !== "dia" && name !== "horario") return;

    var value = pill.getAttribute("data-wecar-value");
    fields[name] = fields[name] === value ? "" : value;
    updatePillUI(name);
    clearFieldError(name);
    persistDraft();
    updateButtonState();
  }

  function generateId() {
    return "wcq-" + Date.now().toString(36) + "-" + Math.random().toString(36).slice(2, 8);
  }

  function showInlineError(message) {
    var el = modal.querySelector("[data-wecar-inline-error]");
    if (!el) return;
    el.textContent = message || "";
    el.classList.add("is-visible");
    el.setAttribute("aria-hidden", "false");
  }

  function hideInlineError() {
    var el = modal.querySelector("[data-wecar-inline-error]");
    if (!el) return;
    el.textContent = "";
    el.classList.remove("is-visible");
    el.setAttribute("aria-hidden", "true");
  }

  function showSuccess() {
    steps.forEach(function (panel) {
      panel.classList.remove("wecar-cotizador-modal__step--active");
      panel.setAttribute("aria-hidden", "true");
    });

    if (successScreen) {
      successScreen.classList.add("is-active");
      successScreen.setAttribute("aria-hidden", "false");
    }

    updateProgress(3);
    focusFirstActiveField();

    window.setTimeout(function () {
      closeModal();
    }, 3000);
  }

  function submitForm() {
    if (!validateStep(3)) return;

    var submission = {
      nombre: fields.nombre,
      mail: fields.mail,
      telefono: fields.telefono,
      localidad: fields.localidad,
      anio: fields.anio,
      marca: fields.marca,
      modelo: fields.modelo,
      kilometros: fields.kilometros,
      gnc: fields.gnc,
      dia: fields.dia,
      horario: fields.horario,
      timestamp: Date.now(),
      id: generateId()
    };

    try {
      var raw = window.localStorage.getItem(KEY_SUBMISSIONS);
      var submissions = [];
      if (raw) {
        var parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) submissions = parsed;
      }
      submissions.push(submission);
      window.localStorage.setItem(KEY_SUBMISSIONS, JSON.stringify(submissions));
    } catch (err) {
      if (window.console && window.console.warn) {
        window.console.warn("[wecar-cotizador] No se pudo guardar la cotización:", err.message);
      }
      showInlineError("No se pudo guardar la cotización. Reintentá o contactanos por WhatsApp.");
      return;
    }

    try {
      window.localStorage.removeItem(KEY_DRAFT);
    } catch (err) {
      if (window.console && window.console.warn) {
        window.console.warn("[wecar-cotizador] No se pudo limpiar el borrador:", err.message);
      }
    }

    // Enviar al backend via AJAX (no bloquea la pantalla de éxito)
    if (typeof wecarCotizador !== 'undefined') {
      var body = new URLSearchParams();
      body.set('action', 'wecar_cotizador_submit');
      body.set('nonce', wecarCotizador.nonce);
      body.set('data', JSON.stringify(submission));

      fetch(wecarCotizador.ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
      }).catch(function (err) {
        if (window.console && window.console.warn) {
          window.console.warn("[wecar-cotizador] Error al enviar al backend:", err.message);
        }
      });
    }

    fields = {
      nombre: "", mail: "", telefono: "", localidad: "",
      anio: "", marca: "", modelo: "", kilometros: "",
      gnc: "", dia: "", horario: ""
    };
    restoreFieldsToDOM();

    showSuccess();
  }

  function handleActionClick(event) {
    var backBtn = event.target.closest('[data-wecar-action="back"]');
    var nextBtn = event.target.closest('[data-wecar-action="next"]');
    var submitBtn = event.target.closest('[data-wecar-action="submit"]');

    if (backBtn) {
      event.preventDefault();
      showStep(currentStep - 1);
      return;
    }

    if (nextBtn) {
      event.preventDefault();
      if (validateStep(currentStep)) {
        showStep(currentStep + 1);
      } else {
        STEP_FIELDS[currentStep].forEach(function (name) {
          var error = validateField(name);
          if (error) showFieldError(name, error);
        });
        updateButtonState();
      }
      return;
    }

    if (submitBtn) {
      event.preventDefault();
      submitForm();
    }
  }

  function handleClick(event) {
    var closeEl = event.target.closest("[data-wecar-cotizador-close]");
    if (closeEl) {
      event.preventDefault();
      closeModal();
      return;
    }

    var openEl = event.target.closest("[data-wecar-cotizador-open]");
    if (openEl) {
      event.preventDefault();
      openModal();
      return;
    }

    if (event.target.closest('[data-wecar-field="gnc"]')) {
      handleRadioClick(event);
      return;
    }

    if (event.target.closest('[data-wecar-field="dia"], [data-wecar-field="horario"]')) {
      handlePillClick(event);
      return;
    }

    handleActionClick(event);
  }

  function init() {
    modal = document.getElementById("wecar-cotizador-modal");
    if (!modal) return;

    trigger = document.querySelector("[data-wecar-cotizador-open]");
    steps = Array.prototype.slice.call(modal.querySelectorAll("[data-wecar-step]"));
    progressBars = Array.prototype.slice.call(modal.querySelectorAll("[data-wecar-progress]"));
    successScreen = modal.querySelector("[data-wecar-success]");

    modal.removeAttribute("hidden");

    modal.addEventListener("input", handleInput, true);
    modal.addEventListener("blur", handleBlur, true);
    document.addEventListener("click", handleClick);

    if (trigger) {
      trigger.addEventListener("click", function (event) {
        event.preventDefault();
        openModal();
      });
    }
  }

  ready(init);
})();
