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

// (function(){

// 	const validateRequired = document.querySelectorAll('.validate-required');
// 	validateRequired.forEach(field => {
// 		const input = field.querySelector('input');
// 		if(input) input.required = 'required';
// 	});

// /*-----START TYPE TEXT-----*/
// 	const inputsText = document.querySelectorAll('.checkout input[type="text"]');
// 	inputsText.forEach( text => {
// 		text.addEventListener('input', function(e){
// 			if( this.required == false ) return;
// 			if( this.value === ' ' ) return;
// 			if( this.value[0] === ' ' ) this.value = this.value.slice(1);
// 			this.value = this.value.replace(/ {1,}/g," ");
// 			console.dir( this.minLength );
// 			if( this.value.length >= this.minLength ){
// 				this.classList.add('valid');
// 			} else {
// 				this.classList.remove('valid');
// 			}
// 		});
// 		text.addEventListener('change', function(e){
// 			if( this.required == false ) return;
// 			if( this.value[this.value.length -1] === ' ' ) this.value = this.value.substring(0, this.value.length - 1);
// 		});
// 	});
// /*-----END TYPE TEXT-----*/

// /*-----START EMAIL-----*/
// 	const inputEmail = document.getElementById('billing_email'),
// 		emailReg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
// 		inputEmail.addEventListener('keydown', function(e){
// 			if( this.required == false ) return;
// 			if( e.which == 32 ) e.preventDefault();
// 		});
// 		inputEmail.addEventListener('input', function(e){
// 			if( this.required == false ) return;
// 			if( !emailReg.test(this.value) ){
// 				this.classList.remove('valid');
// 				// this.focus();
// 			} else {
// 				this.classList.add('valid');
// 			}
// 		});
// /*-----END EMAIL-----*/

// /*-----START PHONE-----*/
// 	const inputTel = document.getElementById('billing_phone');
// 	inputTel.minLength = 14;
// 	inputTel.maxLength = 14;
// 	inputTel.dataset.mask = "true";
// 	inputTel.addEventListener('keydown', function(e){
// 		if( this.required == false ) return;
// 		if( e.which == 32 ) e.preventDefault();
// 	});
// 	inputTel.addEventListener('input', function(e) {
// 		if( this.required == false ) return;
// 		if( this.dataset.mask !== 'true' ) return;
// 		let arr = this.value.replace(/[^\dA-Z]/g, '').replace(/[\s-)(]/g, '').split('');
// 		if(arr.length >= 1) arr.splice(0, 0, '(');
// 		if(arr.length > 4) arr.splice(4, 0, ') ');
// 		if(arr.length > 8) arr.splice(8, 0, '-');
// 		this.value = arr.toString().replace(/[,]/g, '');
// 		if( this.value.length >= this.minLength ){
// 			this.classList.add('valid');
// 		} else {
// 			this.classList.remove('valid');
// 		}
// 	});
// /*-----END PHONE-----*/

// /*-----START ZIP-----*/
// 	const zip = document.getElementById('billing_postcode');
// 	zip.minLength = 5;
// 	zip.maxLength = 5;
// 	zip.addEventListener('keydown', function(e){
// 		if( e.which == 32 ) e.preventDefault();
// 	});
// 	zip.addEventListener('input', function(e) {
// 		this.value = this.value.replace(/[^\dA-Z]/g, '').replace(/[\s-)(]/g, '');
// 		if( this.value.length >= this.minLength ){
// 			this.classList.add('valid');
// 		} else {
// 			this.classList.remove('valid');
// 		}
// 	});
// /*-----END ZIP-----*/

// })();