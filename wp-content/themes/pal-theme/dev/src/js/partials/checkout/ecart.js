let products = [], subtotal = 0;

let shipping = {
	status: false,
	price: 9
};

let discount = {
	status: false,
	code: false,
	percent: 0
};

//-----Cookies-----
function checkCookies(){
	let productsStorage = localStorage.getItem('products'),
		shippingStorage = localStorage.getItem('shipping'),
		discountStorage = localStorage.getItem('discount');

	if( productsStorage && JSON.parse(productsStorage).length > 0 ){
		products = JSON.parse(productsStorage);
		shipping = JSON.parse(shippingStorage);
		discount = JSON.parse(discountStorage);
		eCheckout.style.display = 'block';
		updateCart(products, shipping, discount);
	} else {
		eCheckout.style.display = 'none';
	}

	let coupon_code = document.getElementById('coupon_code');
	if( discount.status === true ){
		coupon_code.value = discount.code;
	} else {
		coupon_code.value = '';
	}

	if( shipping.status === true ){
		let express_shipping = document.getElementById('express_shipping'),
			eShipping = document.getElementById('eShipping');
		express_shipping.checked = 'checked';
		eShipping.innerHTML = shipping.price;
	} else {
		eShipping.innerHTML = 0;
	}
}checkCookies();

function updateCookies(products, shipping, discount){
	localStorage.setItem('products', JSON.stringify(products) );
	localStorage.setItem('shipping', JSON.stringify(shipping) );
	localStorage.setItem('discount', JSON.stringify(discount) );
	checkCookies();
}

//-----Product-----
function addProduct(e){
	e.preventDefault();
	let item = {
			id: e.target.dataset.id,
			image: e.target.dataset.image,
			name: e.target.dataset.name,
			quantity: e.target.dataset.quantity,
			price: e.target.dataset.price
		};
	if(products.length > 0){
		let addNewProduct = true;
		for (let i in products) {
			if ( parseInt(products[i].id) === parseInt(item.id) ) {
				products[i].quantity = parseInt(products[i].quantity) + 1;
				addNewProduct = false;
				break;
			}
		}
		if(addNewProduct){
			products.push(item);
		}
	} else {
		products.push(item);
	}
	updateCookies(products, shipping, discount);
}
function removeProduct(e){
	e.preventDefault();
	for (let i in products) {
		if ( parseInt(products[i].id) === parseInt(e.target.dataset.id) ) {
			products.splice(i, 1);
			subtotal -= parseInt( e.target.dataset.quantity ) * parseInt( e.target.dataset.price );
			break;
		}
	}
	updateCookies(products, shipping, discount);
}

// Totals
function updateTotals(){
	let eSubtotal = document.getElementById('eSubtotal'),
		eDiscount = document.getElementById('eDiscount'),
		eShipping = document.getElementById('eShipping'),
		eTotal = document.getElementById('eTotal');

	if( discount.status === true ){
		eSubtotal.innerHTML = ( parseInt(subtotal) - (parseInt(subtotal) * parseInt(discount.percent) / 100) ).toFixed(2);
		eDiscount.innerHTML = parseInt(discount.percent);
		if(shipping.status === true){
			eTotal.innerHTML = ( parseInt(subtotal) - (parseInt(subtotal) * parseInt(discount.percent) / 100) + parseInt(shipping.price) ).toFixed(2);
		} else {
			eTotal.innerHTML = ( parseInt(subtotal) - (parseInt(subtotal) * parseInt(discount.percent) / 100) ).toFixed(2);
		}
	} else {
		eSubtotal.innerHTML = parseInt(subtotal).toFixed(2);
		eDiscount.innerHTML = 0;
		if(shipping.status === true){
			eTotal.innerHTML = ( parseInt(subtotal) + parseInt(shipping.price) ).toFixed(2);
		} else {
			eTotal.innerHTML = ( parseInt(subtotal) ).toFixed(2);
		}
	}
}

//-----Cart-----
function updateCart(products, shipping, discount) {
	let eCheckout = document.getElementById('eCheckout');

	if(products){
		let cartBody = document.querySelector('#eCart .tbody');
		subtotal = 0;

		cartBody.innerHTML = '';
		products.forEach(function(e) {
			cartBody.innerHTML += `<div id="${e.id}" class="cart-item">
				<button class="eRemove" data-id="${e.id}" data-price="${e.price}" data-quantity="${e.quantity}">+</button>
				<input class="eQuantity" type="number" value="${e.quantity}" data-id="${e.id}" data-price="${e.price}" >
				<span class="ePrice before-dollar">${e.price}</span>
				<img class="eImage" src="${e.image}" alt="">
				${e.name}
			</div>`;
			subtotal += parseInt(e.quantity) * parseInt(e.price);
		});
	}

	if(shipping.status === true){
		let eShipping = document.getElementById('eShipping');
		eShipping.innerHTML = shipping.price;
	}

	if(discount.status === true){
		let eDiscount = document.getElementById('eDiscount');
		eDiscount.innerHTML = discount.percent;
	}

	updateTotals(subtotal);

	removeProductItem();

	updateCartButton();

	setTimeout(()=>{
		eCheckout.scrollIntoView({block: "start", behavior: "smooth"});
	}, 500);
}
// Update cart table
function updateCartQuantity(){
	let eQuantity = document.querySelectorAll('.eQuantity');
	for (let j = 0; j < eQuantity.length; j++) {
		for (let i in products) {
			if ( parseInt(products[i].id) === parseInt(eQuantity[j].dataset.id) ) {
				products[i].quantity = parseInt(eQuantity[j].value);
				break;
			}
		}
	}
	updateCookies(products, shipping, discount);
}


//-----ACTIONS-----
let addProductBTN = document.querySelectorAll('.addProduct');
for (let i = 0; i < addProductBTN.length; i++){
	addProductBTN[i].addEventListener('click', addProduct);
}

function removeProductItem(){
	let eRemove = document.querySelectorAll('.eRemove');
	for(let i = 0; i < eRemove.length; i++){
		eRemove[i].addEventListener('click', removeProduct);
	}
}

function updateCartButton(){
	let eUpdate = document.getElementById('eUpdate');
	eUpdate.addEventListener('click', updateCartQuantity);
}

// let couponForm = document.getElementById('couponForm');
// couponForm.addEventListener('submit', function(e){
// 	e.preventDefault();
// 	let coupon_code = document.getElementById('coupon_code');
// 	for (let i in COUPONES) {
// 		if ( COUPONES[i].code == coupon_code.value ) {
// 			discount.status = true;
// 			discount.code = COUPONES[i].code;
// 			discount.percent = parseInt( COUPONES[i].percent );
// 			updateCookies(products, shipping, discount);
// 			break;
// 		}
// 	}
// });

const couponForm = document.getElementById('couponForm');
couponForm.addEventListener('submit', function(e){
	e.preventDefault();

	const coupon_code = document.getElementById('coupon_code');
	if (coupon_code.value.length > 0 ){

		const requestCoupon = [{
			action: 'couponCode',
			coupon: coupon_code.value
		}];

		requestAction(requestCoupon, function(result){
			if(result == 'error'){
				console.log(result);
				coupon_code.style.boxShadow = '0 0 8px red inset';
				setTimeout(function(){
					coupon_code.style.boxShadow = 'none';
				}, 2000);
			} else {
				let couponPercent = JSON.parse(result);
				console.log(couponPercent);

				discount.status = true;
				discount.code = coupon_code.value;
				discount.percent = parseInt( couponPercent );
				updateCookies(products, shipping, discount);
				document.getElementById('couponForm').classList.add('hide');
			}
		});
	} else{
		coupon_code.classList.add('error');
		setTimeout(function(){
			coupon_code.classList.remove('error');
		}, 1000);
	}
});

let openCouponCode = document.getElementById('openCouponCode');
openCouponCode.addEventListener('click', function(e){
	e.preventDefault();
	couponForm.classList.toggle('hide');
});

let express_shipping = document.getElementById('express_shipping');
express_shipping.addEventListener('change', function(e){
	let eShipping = document.getElementById('eShipping');
	if( express_shipping.checked ){
		shipping.status = true;
		eShipping.innerHTML = shipping.price;
	} else {
		shipping.status = false;
		eShipping.innerHTML = 0;
	}
	updateCookies(products, shipping, discount);
});