/**
 * @jest-environment jsdom
 */

'use strict';

const path = require('path');

const SCRIPT_PATH = path.join(__dirname, '..', '..', 'assets', 'js', 'form-a11y.js');

const BASE_FORM = `
  <form class="jetpack-contact-form__form">
    <div class="grunion-field-wrap">
      <label for="name">Naam</label>
      <input id="name" name="name" type="text" required />
      <span id="name-error-message">Dit veld is verplicht.</span>
    </div>
    <input type="hidden" name="_wpnonce" value="abc" />
    <button type="submit">Verzenden</button>
  </form>
`;

const TWO_FIELD_FORM = `
  <form class="jetpack-contact-form__form">
    <div class="grunion-field-wrap">
      <input id="name" type="text" required />
      <span id="name-error-message">Verplicht</span>
    </div>
    <div class="grunion-field-wrap">
      <input id="email" type="email" required />
      <span id="email-error-message">Verplicht</span>
    </div>
    <button type="submit">Verzenden</button>
  </form>
`;

/**
 * Renders the given form markup and (re)loads the accessibility script
 * against it. The script binds its behaviour at require-time by querying
 * the current document, so the DOM must be in place before it is required,
 * and the module registry must be reset so a fresh copy runs each time.
 */
function loadScriptWithForm(formHtml) {
	document.body.innerHTML = formHtml;
	jest.resetModules();
	require(SCRIPT_PATH);
	return document.querySelector('.jetpack-contact-form__form');
}

describe('form-a11y.js', () => {
	afterEach(() => {
		document.body.innerHTML = '';
		jest.useRealTimers();
	});

	test('does nothing when no Jetpack contact form is present', () => {
		document.body.innerHTML = '<div>geen formulier hier</div>';
		jest.resetModules();

		expect(() => require(SCRIPT_PATH)).not.toThrow();
		expect(document.querySelector('.bz-form-error-summary')).toBeNull();
	});

	test('prepends an accessible, hidden error summary to each matching form', () => {
		const form = loadScriptWithForm(BASE_FORM);
		const summary = form.querySelector('.bz-form-error-summary');

		expect(summary).not.toBeNull();
		expect(summary).toBe(form.firstElementChild);
		expect(summary.getAttribute('role')).toBe('alert');
		expect(summary.getAttribute('aria-live')).toBe('assertive');
		expect(summary.getAttribute('tabindex')).toBe('-1');
		expect(summary.hidden).toBe(true);
	});

	test('marks a field invalid as soon as the invalid event fires', () => {
		const form = loadScriptWithForm(BASE_FORM);
		const field = form.querySelector('#name');

		field.dispatchEvent(new Event('invalid', { bubbles: false, cancelable: true }));

		expect(field.getAttribute('aria-invalid')).toBe('true');
	});

	test('connects an invalid field to its wrap error message via aria-describedby', () => {
		jest.useFakeTimers();
		const form = loadScriptWithForm(BASE_FORM);
		const field = form.querySelector('#name');
		field.setAttribute('aria-describedby', 'existing-hint');

		field.dispatchEvent(new Event('invalid', { bubbles: false, cancelable: true }));
		jest.runAllTimers();

		const describedBy = field.getAttribute('aria-describedby').split(/\s+/);
		expect(describedBy).toEqual(expect.arrayContaining(['existing-hint', 'name-error-message']));
	});

	test('does not duplicate the error message id on repeated invalid events', () => {
		jest.useFakeTimers();
		const form = loadScriptWithForm(BASE_FORM);
		const field = form.querySelector('#name');

		field.dispatchEvent(new Event('invalid', { bubbles: false, cancelable: true }));
		jest.runAllTimers();
		field.dispatchEvent(new Event('invalid', { bubbles: false, cancelable: true }));
		jest.runAllTimers();

		const describedBy = field.getAttribute('aria-describedby').split(/\s+/).filter(Boolean);
		expect(describedBy.filter((id) => id === 'name-error-message')).toHaveLength(1);
	});

	test('shows a singular Dutch summary message for exactly one invalid field', () => {
		const form = loadScriptWithForm(BASE_FORM);
		const field = form.querySelector('#name');
		const summary = form.querySelector('.bz-form-error-summary');

		field.value = '';
		field.dispatchEvent(new Event('input', { bubbles: true }));

		expect(summary.hidden).toBe(false);
		expect(summary.textContent).toBe('Controleer 1 gemarkeerd veld en probeer het opnieuw.');
		expect(field.getAttribute('aria-invalid')).toBe('true');
	});

	test('shows a plural Dutch summary message for multiple invalid fields', () => {
		const form = loadScriptWithForm(TWO_FIELD_FORM);
		const summary = form.querySelector('.bz-form-error-summary');

		form.querySelector('#name').dispatchEvent(new Event('input', { bubbles: true }));

		expect(summary.textContent).toBe('Controleer 2 gemarkeerde velden en probeer het opnieuw.');
	});

	test('hides and clears the summary once every field becomes valid again', () => {
		const form = loadScriptWithForm(BASE_FORM);
		const field = form.querySelector('#name');
		const summary = form.querySelector('.bz-form-error-summary');

		field.value = '';
		field.dispatchEvent(new Event('input', { bubbles: true }));
		expect(summary.hidden).toBe(false);

		field.value = 'Jan';
		field.dispatchEvent(new Event('input', { bubbles: true }));

		expect(summary.hidden).toBe(true);
		expect(summary.textContent).toBe('');
		expect(field.hasAttribute('aria-invalid')).toBe(false);
	});

	test('reacts to change events the same way it reacts to input events', () => {
		const form = loadScriptWithForm(BASE_FORM);
		const field = form.querySelector('#name');
		const summary = form.querySelector('.bz-form-error-summary');

		field.value = '';
		field.dispatchEvent(new Event('change', { bubbles: true }));

		expect(summary.hidden).toBe(false);
		expect(field.getAttribute('aria-invalid')).toBe('true');
	});

	test('ignores hidden fields when computing the invalid count', () => {
		const form = loadScriptWithForm(BASE_FORM);
		const hiddenField = form.querySelector('input[type="hidden"]');
		const summary = form.querySelector('.bz-form-error-summary');

		form.querySelector('#name').dispatchEvent(new Event('input', { bubbles: true }));

		expect(hiddenField.hasAttribute('aria-invalid')).toBe(false);
		expect(summary.textContent).toBe('Controleer 1 gemarkeerd veld en probeer het opnieuw.');
	});

	test('focuses the first invalid field shortly after the submit button is clicked', () => {
		jest.useFakeTimers();
		const form = loadScriptWithForm(BASE_FORM);
		const field = form.querySelector('#name');
		field.value = '';

		form.querySelector('[type="submit"]').dispatchEvent(new Event('click', { bubbles: true }));
		jest.advanceTimersByTime(120);

		expect(document.activeElement).toBe(field);
	});

	test('does not attempt to focus a field when the form has no submit button', () => {
		document.body.innerHTML = `
      <form class="jetpack-contact-form__form">
        <div class="grunion-field-wrap">
          <input id="name" type="text" required />
          <span id="name-error-message">Verplicht</span>
        </div>
      </form>
    `;
		jest.resetModules();

		expect(() => require(SCRIPT_PATH)).not.toThrow();
	});
});