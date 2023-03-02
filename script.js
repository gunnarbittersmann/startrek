document.documentElement.style.setProperty(
	'--header-height', `${document.querySelector('header').offsetHeight}px`
);

const resizeObserver = new ResizeObserver(entries => {
	document.documentElement.style.setProperty(
		'--header-height', `${entries[0].target.offsetHeight}px`
	);
});

resizeObserver.observe(document.querySelector('header'));


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
