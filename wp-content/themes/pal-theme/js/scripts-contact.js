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
(function(){
	const mainNavBtn = document.getElementById('mainNavBtn');
	mainNavBtn.addEventListener('click', function(e){
		e.preventDefault();
		document.getElementById('mainNav').classList.toggle('active');
		this.classList.toggle('active');
	});
})();
(function(){
	const subscribeForm = document.getElementById('sForm');
	subscribeForm.addEventListener('submit', e => {
		e.preventDefault();

		let formData = new FormData();

		formData.append('action', 'subscribeForm' );
		formData.append('email', document.getElementById('s_email').value );

		let request = new XMLHttpRequest();
		request.open('POST', data.ajax, true);

		request.onload = function(){
			if (this.status >= 200 && this.status < 400) {
				if( this.response == 'sucess' ){
					alert('Thank you for submitting. We will contact you shortly!');
				} else {
					alert('Something went wrong. Reload the page and try again!');
				}
			} else {
				alert('Request status: '+this.status);
			}
		}

		request.onerror = function(){
			alert('Request error!');
		}

		request.send(formData);
	});
})();


const contactForm = document.getElementById('contact');
contactForm.addEventListener('submit', e => {
	e.preventDefault();

	let formData = new FormData();

	formData.append('action', 'contactForm' );
	formData.append('name', document.getElementById('name').value );
	formData.append('email', document.getElementById('email').value );
	formData.append('phone', document.getElementById('phone').value );
	formData.append('message', document.getElementById('message').value );

	let request = new XMLHttpRequest();
	request.open('POST', data.ajax, true);

	request.onload = function(){
		if (this.status >= 200 && this.status < 400) {
			if( this.response == 'sucess' ){
				alert('Thank you for submitting. We will contact you shortly!');
			} else {
				alert('Something went wrong. Reload the page and try again!');
			}
		} else {
			alert('Request status: '+this.status);
		}
	}

	request.onerror = function(){
		alert('Request error!');
	}

	request.send(formData);
});