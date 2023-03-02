const headerElement = document.querySelector('header');

const storeHeaderHeight = () => {
	document.documentElement.style.setProperty(
		'--header-height', `${headerElement.offsetHeight}px`
	);
};

storeHeaderHeight();

const resizeObserver = new ResizeObserver(storeHeaderHeight);

resizeObserver.observe(headerElement);


const intersectionObserver = new IntersectionObserver(entries => {
	document.body.classList.toggle(
		'scrolled', entries[0].intersectionRatio == 0
	);
}, { rootMargin: '36px' });

intersectionObserver.observe(document.head);


for (let videoDetailsElement of document.querySelectorAll('details[property="video"]')) {
	videoDetailsElement.addEventListener('toggle', event => {
		const iframeElement = event.target.querySelector('iframe');
		if (!iframeElement.src) {
			iframeElement.src = event.target.querySelector('meta').getAttribute('content');
		}
	});
}
