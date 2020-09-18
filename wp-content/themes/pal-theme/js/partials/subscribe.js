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