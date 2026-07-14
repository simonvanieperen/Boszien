import { store, getContext, getElement } from '@wordpress/interactivity';

store( 'boszien/evidence-meter', {
	actions: {
		toggle() {
			const context = getContext();
			const { ref } = getElement();
			const next = ref?.dataset?.step || '';

			context.open = context.open === next ? '' : next;
		},
	},
	callbacks: {
		isOpen() {
			const context = getContext();
			const { ref } = getElement();
			const step = ref?.dataset?.step || '';

			return context.open === step;
		},
	},
} );
