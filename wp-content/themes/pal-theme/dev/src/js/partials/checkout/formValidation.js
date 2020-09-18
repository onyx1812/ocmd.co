(function(){

/*-----START TYPE TEXT-----*/
	const inputsText = document.querySelectorAll('input[type="text"]');
	inputsText.forEach( text => {
		text.addEventListener('input', function(e){
			if( this.required == false ) return;
			if( this.value[0] === ' ' ) this.value = this.value.slice(1);
			this.value = this.value.replace(/ {1,}/g," ");
			if( this.value.length >= this.minLength ){
				this.classList.add('valid');
			} else {
				this.classList.remove('valid');
			}
		});
		text.addEventListener('change', function(e){
			if( this.required == false ) return;
			if( this.value[this.value.length -1] === ' ' ) this.value = this.value.substring(0, this.value.length - 1);
		});
	});
/*-----END TYPE TEXT-----*/

/*-----START EMAIL-----*/
	const inputsEmail = document.querySelectorAll('input[type="email"]'),
		emailReg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	inputsEmail.forEach( email => {
		email.addEventListener('keydown', function(e){
			if( this.required == false ) return;
			if( e.which == 32 ) e.preventDefault();
		});
		email.addEventListener('input', function(e){
			if( this.required == false ) return;
			if( !emailReg.test(this.value) ){
				this.classList.remove('valid');
				// this.focus();
			} else {
				this.classList.add('valid');
			}
		});
	});
/*-----END EMAIL-----*/

/*-----START PHONE-----*/
	const inputsTel = document.querySelectorAll('input[type="tel"]');
	inputsTel.forEach( tel => {
		tel.addEventListener('keydown', function(e){
			if( this.required == false ) return;
			if( e.which == 32 ) e.preventDefault();
		});
		tel.addEventListener('input', function(e) {
			if( this.required == false ) return;
			if( this.dataset.mask !== 'true' ) return;
			let arr = this.value.replace(/[^\dA-Z]/g, '').replace(/[\s-)(]/g, '').split('');
			if(arr.length >= 1) arr.splice(0, 0, '(');
			if(arr.length > 4) arr.splice(4, 0, ') ');
			if(arr.length > 8) arr.splice(8, 0, '-');
			this.value = arr.toString().replace(/[,]/g, '');
			if( this.value.length >= this.minLength ){
				this.classList.add('valid');
			} else {
				this.classList.remove('valid');
			}
		});
	});
/*-----END PHONE-----*/

/*-----START ZIP-----*/
	// const zip = document.getElementById('zip');
	// zip.addEventListener('keydown', function(e){
	// 	if( e.which == 32 ) e.preventDefault();
	// });
	// zip.addEventListener('input', function(e) {
	// 	this.value = this.value.replace(/[^\dA-Z]/g, '').replace(/[\s-)(]/g, '');
	// 	if( this.value.length >= this.minLength ){
	// 		this.classList.add('valid');
	// 	} else {
	// 		this.classList.remove('valid');
	// 	}
	// });
/*-----END ZIP-----*/

/*-----START CARD NUMBER-----*/
	const card_number = document.getElementById('card_number');
	card_number.addEventListener('keydown', function(e){
		if( e.which == 32 ) e.preventDefault();
	});
	card_number.addEventListener('input', function(e) {
		let arr = this.value.replace(/[^\dA-Z]/g, '').replace(/[\s]/g, '').split('');
		if(arr.length > 4) arr.splice(4, 0, ' ');
		if(arr.length > 9) arr.splice(9, 0, ' ');
		if(arr.length > 14) arr.splice(14, 0, ' ');
		this.value = arr.toString().replace(/[,]/g, '');
		if( this.value.length >= this.minLength ){
			this.classList.add('valid');
		} else {
			this.classList.remove('valid');
		}
	});
/*-----END CARD NUMBER-----*/

/*-----START Expiry Date-----*/
	const expiry_date = document.getElementById('expiry_date');
	expiry_date.addEventListener('keydown', function(e){
		if( e.which == 32 ) e.preventDefault();
	});
	expiry_date.addEventListener('input', function(e) {
		let arr = this.value.replace(/[^\dA-Z]/g, '').replace(/[\s\/]/g, '').split('');
		if(arr.length > 2) arr.splice(2, 0, ' / ');
		this.value = arr.toString().replace(/[,]/g, '');
		if( this.value.length >= this.minLength ){
			this.classList.add('valid');
		} else {
			this.classList.remove('valid');
		}
	});
/*-----END Expiry Date-----*/

/*-----START CVC-----*/
	const card_code = document.getElementById('card_code');
	card_code.addEventListener('keydown', function(e){
		if( e.which == 32 ) e.preventDefault();
	});
	card_code.addEventListener('input', function(e) {
		this.value = this.value.replace(/[^\dA-Z]/g, '');
		if( this.value.length >= this.minLength ){
			this.classList.add('valid');
		} else {
			this.classList.remove('valid');
		}
	});
/*-----END CVC-----*/

})();