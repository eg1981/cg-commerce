(function () {
  const form = document.querySelector('form.register, form.woocommerce-form.woocommerce-form-register');
  if (!form) return;

  const qs  = (s) => form.querySelector(s);
  const qsa = (s) => Array.from(form.querySelectorAll(s));

  // שדות
  const firstName = qs('#reg_first_name, [name="first_name"]');
  const lastName  = qs('#reg_last_name, [name="last_name"]');
  const email     = qs('#reg_email, [name="email"]');
  const pass1     = qs('#reg_password, [name="password"]');
  const pass2     = qs('#reg_password2, [name="password2"]');
  const phone     = qs('#reg_phone, [name="phone"]');
  const terms     = qs('#reg_terms, [name="terms"]');
  const submitBtn = form.querySelector('button[type="submit"], .woocommerce-Button');

  let hasTriedSubmit = false;
  const touched = new WeakSet();

  function markTouched(el){ if (el) touched.add(el); }
  function isTouched(el){ return el && touched.has(el); }

  function setError(field, msg) {
    clearError(field);
    field.classList.add('has-error');
    field.setAttribute('aria-invalid', 'true');
    const hint = document.createElement('div');
    hint.className = 'field-error';
    hint.textContent = msg;
    (field.parentElement || field).appendChild(hint);
  }
  function clearError(field) {
    field.classList.remove('has-error');
    field.removeAttribute('aria-invalid');
    const old = field.parentElement && field.parentElement.querySelector('.field-error');
    if (old) old.remove();
  }

  const notEmpty = (v) => typeof v === 'string' && v.trim().length > 0;

  function validatePassword(pw) {
    if (!notEmpty(pw)) return { ok: false, msg: 'יש להזין סיסמה.' };
    if (pw.length < 8) return { ok: false, msg: 'לפחות 8 תווים.' };
    if (/\s/.test(pw)) return { ok: false, msg: 'ללא רווחים.' };
    if (!/[A-Z]/.test(pw)) return { ok: false, msg: 'לפחות אות גדולה באנגלית.' };
    if (!/[a-z]/.test(pw)) return { ok: false, msg: 'לכלול אותיות באנגלית.' };
    if (!/[0-9]/.test(pw)) return { ok: false, msg: 'לכלול מספר.' };
    if (!/[\W_]/.test(pw)) return { ok: false, msg: 'לכלול סימן מיוחד.' };
    return { ok: true };
  }

  function validatePhoneValue(v) {
    const digits = (v || '').replace(/\D/g, '');
    if (!notEmpty(v)) return { ok: false, msg: 'יש להזין מספר טלפון.' };
    if (digits.length < 7) return { ok: false, msg: 'מספר טלפון לא תקין.' };
    return { ok: true };
  }

  function validateEmailValue(v) {
    if (!notEmpty(v)) return { ok: false, msg: 'יש להזין דוא״ל.' };
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(v) ? { ok: true } : { ok: false, msg: 'כתובת דוא״ל לא תקינה.' };
  }

  // מציגים שגיאה רק אם השדה "touched" או אחרי ניסיון שליחה
  function maybeShowError(field, valid, msg){
    if (!valid) {
      if (hasTriedSubmit || isTouched(field)) setError(field, msg);
      return false;
    } else {
      clearError(field);
      return true;
    }
  }

  function validateField(field) {
    if (!field) return true;
    const val = field.value || '';

    if (field === firstName) {
      return maybeShowError(field, notEmpty(val), 'יש למלא שם פרטי.');
    }
    if (field === lastName) {
      return maybeShowError(field, notEmpty(val), 'יש למלא שם משפחה.');
    }
    if (field === email) {
      const r = validateEmailValue(val);
      return maybeShowError(field, r.ok, r.msg);
    }
    if (field === pass1) {
      const r = validatePassword(val);
      return maybeShowError(field, r.ok, r.msg);
    }
    if (field === pass2) {
      if (!notEmpty(val)) return maybeShowError(field, false, 'נא לאמת סיסמה.');
      const same = pass1 ? val === pass1.value : true;
      return maybeShowError(field, same, 'הסיסמאות אינן תואמות.');
    }
    if (field === phone) {
      const r = validatePhoneValue(val);
      return maybeShowError(field, r.ok, r.msg);
    }
    return true;
  }

  function validateTerms() {
    if (!terms) return true;
    const container = terms.closest('p, .form-row') || terms.parentElement;
    const existing = container.querySelector('.field-error');
    const ok = terms.checked === true;

    if (!ok && (hasTriedSubmit || terms.matches(':focus'))) {
      if (!existing) {
        const err = document.createElement('div');
        err.className = 'field-error';
        err.textContent = 'יש לאשר את התקנון.';
        container.appendChild(err);
      }
    } else if (ok && existing) {
      existing.remove();
    }
    return ok;
  }

  function validateAll() {
    const fields = [firstName, lastName, email, pass1, pass2, phone].filter(Boolean);
    let ok = true;
    fields.forEach(f => { if (!validateField(f)) ok = false; });
    if (!validateTerms()) ok = false;

    // אחרי ניסיון שליחה – כפתור נעול עד שהכול תקין
    if (hasTriedSubmit && submitBtn) submitBtn.disabled = !ok;
    return ok;
  }

  // סימון touched
  qsa('input').forEach(inp => {
    inp.addEventListener('input', () => { markTouched(inp); validateField(inp); if (hasTriedSubmit) validateAll(); });
    inp.addEventListener('blur',  () => { markTouched(inp); validateField(inp); if (hasTriedSubmit) validateAll(); });
  });
  if (terms) {
    terms.addEventListener('change', () => { if (hasTriedSubmit) validateAll(); });
  }

  // לא מריצים validateAll בטעינה → אין שגיאות עד האינטראקציה הראשונה
  form.addEventListener('submit', (e) => {
    hasTriedSubmit = true;
    const ok = validateAll();
    if (!ok) {
      e.preventDefault();
      e.stopPropagation();
      const firstErr = form.querySelector('.has-error, .field-error');
      if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
})();
