const headerElement = document.querySelector('header');

document.documentElement.style.setProperty(
	'--header-height', `${headerElement.offsetHeight}px`
);

const resizeObserver = new ResizeObserver(entries => {
	document.documentElement.style.setProperty(
		'--header-height', `${headerElement.offsetHeight}px`
	);
});

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
