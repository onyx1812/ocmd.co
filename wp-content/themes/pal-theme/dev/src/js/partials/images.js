(function(){
	const winPosition = ()=>{
		const images = document.querySelectorAll('img[data-src]');
		const position = window.scrollY + window.outerHeight;
		images.forEach(image => {
			if( image.src == 'data:image/jpg;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' && position > image.y){
				image.src = image.dataset.src;
			}
		});
	}
	window.addEventListener('scroll', winPosition);
	window.addEventListener('load', winPosition);
})();