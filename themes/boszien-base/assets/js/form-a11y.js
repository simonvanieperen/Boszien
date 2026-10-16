(() => {
	'use strict';

	const forms = document.querySelectorAll('.jetpack-contact-form__form');

	forms.forEach((form) => {
		const summary = document.createElement('div');
		summary.className = 'bz-form-error-summary';
		summary.setAttribute('role', 'alert');
		summary.setAttribute('aria-live', 'assertive');
		summary.setAttribute('tabindex', '-1');
		summary.hidden = true;
		form.prepend(summary);

		const connectError = (field) => {
			const wrap = field.closest('.grunion-field-wrap');
			const message = wrap ? wrap.querySelector('[id$="-error-message"]') : null;
			if (!message) {
				return;
			}

			const ids = new Set((field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
			ids.add(message.id);
			field.setAttribute('aria-describedby', Array.from(ids).join(' '));
		};

		const updateValidation = (focusFirst) => {
			const invalid = Array.from(form.querySelectorAll('input:invalid, textarea:invalid, select:invalid'))
				.filter((field) => field.type !== 'hidden');

			form.querySelectorAll('[aria-invalid="true"]').forEach((field) => {
				if (field.validity.valid) {
					field.removeAttribute('aria-invalid');
				}
			});

			invalid.forEach((field) => {
				field.setAttribute('aria-invalid', 'true');
				connectError(field);
			});

			if (!invalid.length) {
				summary.hidden = true;
				summary.textContent = '';
				return;
			}

			const fieldsLabel = invalid.length === 1 ? 'gemarkeerd veld' : 'gemarkeerde velden';
			summary.textContent = `Controleer ${invalid.length} ${fieldsLabel} en probeer het opnieuw.`;
			summary.hidden = false;

			if (focusFirst) {
				invalid[0].focus();
			}
		};

		form.addEventListener('invalid', (event) => {
			event.target.setAttribute('aria-invalid', 'true');
			window.setTimeout(() => connectError(event.target), 0);
		}, true);

		form.addEventListener('input', () => updateValidation(false));
		form.addEventListener('change', () => updateValidation(false));

		const submit = form.querySelector('[type="submit"]');
		if (submit) {
			submit.addEventListener('click', () => {
				window.setTimeout(() => updateValidation(true), 120);
			});
		}
	});
})();
