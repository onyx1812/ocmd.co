//= partials/images.js
//= partials/header.js
//= partials/subscribe.js

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

(function(){
  let subscriptions = document.querySelectorAll('.product-total .subscription-price');
  if(subscriptions.length > 0){
    document.querySelector('.expect-list li:first-child').style.display = 'none';
    document.querySelector('.expect-list li:nth-child(2)').style.marginTop = '0';
  }
})();