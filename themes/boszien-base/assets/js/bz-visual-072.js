(() => {
	'use strict';

	const articleTitle = document.querySelector('.bz72-article-hero h1');
	const approvedTitle = 'Hoe sterk is het bewijs achter een gezondheidsclaim?';
	if (articleTitle && articleTitle.textContent.trim() === approvedTitle && !articleTitle.querySelector('.bz72-title-break')) {
		const lineBreak = document.createElement('br');
		lineBreak.className = 'bz72-title-break';
		lineBreak.setAttribute('aria-hidden', 'true');
		articleTitle.replaceChildren(
			document.createTextNode('Hoe sterk is het bewijs '),
			lineBreak,
			document.createTextNode('achter een gezondheidsclaim?')
		);
		articleTitle.setAttribute('aria-label', approvedTitle);
	}

	document.querySelectorAll('[data-bz-print]').forEach((button) => {
		button.addEventListener('click', () => window.print());
	});

	document.querySelectorAll('[data-bz-copy]').forEach((button) => {
		const original = Array.from(button.childNodes, (node) => node.cloneNode(true));
		let resetTimer;
		let copying = false;
		button.addEventListener('click', async () => {
			if (copying) {
				return;
			}
			copying = true;
			window.clearTimeout(resetTimer);
			let copied = false;
			try {
				await navigator.clipboard.writeText(window.location.href);
				copied = true;
			} catch (error) {
				const input = document.createElement('input');
				const previousFocus = document.activeElement;
				input.value = window.location.href;
				input.readOnly = true;
				input.style.position = 'fixed';
				input.style.opacity = '0';
				document.body.appendChild(input);
				try {
					input.select();
					copied = document.execCommand('copy');
				} catch (fallbackError) {
					copied = false;
				} finally {
					input.remove();
					if (previousFocus && typeof previousFocus.focus === 'function') {
						previousFocus.focus({ preventScroll: true });
					}
				}
			}
			copying = false;
			button.textContent = copied ? 'Link gekopieerd' : 'Kopiëren mislukt';
			resetTimer = window.setTimeout(() => {
				button.replaceChildren(...original.map((node) => node.cloneNode(true)));
			}, 1800);
		});
	});

	document.querySelectorAll('[data-bz-email]').forEach((link) => {
		link.href = `mailto:?subject=${encodeURIComponent(document.title)}&body=${encodeURIComponent(window.location.href)}`;
	});

	const toc = document.querySelector('.bz72-article-toc');
	const content = document.querySelector('.bz72-single-content');
	const tocList = toc ? toc.querySelector('ol') : null;
	const tocSections = new Map();
	if (toc && content && tocList) {
		const headings = Array.from(content.querySelectorAll('h2, h3'))
			.filter((heading) => heading.textContent.trim());
		tocList.replaceChildren();
		headings.forEach((heading, index) => {
			if (!heading.id || document.getElementById(heading.id) !== heading) {
				const base = heading.textContent.trim().normalize('NFKD')
					.replace(/[\u0300-\u036f]/g, '').toLowerCase()
					.replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'artikel-sectie';
				let id = base;
				let suffix = 2;
				while (document.getElementById(id)) {
					id = `${base}-${suffix++}`;
				}
				heading.id = id;
			}
			const item = document.createElement('li');
			const link = document.createElement('a');
			const number = document.createElement('span');
			number.textContent = String(index + 1);
			link.setAttribute('href', `#${encodeURIComponent(heading.id)}`);
			link.append(number, document.createTextNode(heading.textContent.trim()));
			item.append(link);
			tocList.append(item);
			tocSections.set(heading, link);
		});
		toc.hidden = headings.length === 0;
	}
	const tocLinks = Array.from(tocSections.values());
	if (tocLinks.length && 'IntersectionObserver' in window) {
		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}
				tocLinks.forEach((link) => link.removeAttribute('aria-current'));
				const active = tocSections.get(entry.target);
				if (active) {
					active.setAttribute('aria-current', 'location');
				}
			});
		}, { rootMargin: '-20% 0px -65% 0px' });
		tocSections.forEach((link, section) => observer.observe(section));
	}
})();
