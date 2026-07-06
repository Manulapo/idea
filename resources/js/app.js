//
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();


const scrollPositionKey = 'idea:form-scroll-position';

const restoreScrollPosition = () => {
	const storedScrollPosition = sessionStorage.getItem(scrollPositionKey);

	if (!storedScrollPosition) {
		return;
	}

	try {
		const { pathname, search, scrollY } = JSON.parse(storedScrollPosition);

		if (pathname === window.location.pathname && search === window.location.search) {
			window.scrollTo({ top: scrollY, behavior: 'auto' });
		}
	} catch {
		// Ignore invalid stored values.
	} finally {
		sessionStorage.removeItem(scrollPositionKey);
	}
};

document.addEventListener(
	'submit',
	() => {
		sessionStorage.setItem(
			scrollPositionKey,
			JSON.stringify({
				pathname: window.location.pathname,
				search: window.location.search,
				scrollY: window.scrollY,
			}),
		);
	},
	true,
);

window.addEventListener('DOMContentLoaded', restoreScrollPosition);
