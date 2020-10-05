//= partials/images.js
//= partials/header.js
//= partials/subscribe.js
//= partials/faq.js
//= partials/tiny-slider.js

const sliderResults = tns({
	container: '#sliderResults',
	items: 1,
	speed: 1000,
	autoplay: true,
	autoplayHoverPause: true,
	autoplayTimeout: 3500,
	arrowKeys: true,
	swipeAngle: false,
	axis: "horizontal",
	autoplayButtonOutput: false,
	nav: false,
});

const sliderReviews = tns({
	container: '#sliderReviews',
	items: 1,
	speed: 1000,
	autoplay: true,
	autoplayHoverPause: true,
	autoplayTimeout: 3500,
	arrowKeys: true,
	swipeAngle: false,
	axis: "horizontal",
	autoplayButtonOutput: false,
	nav: false,
	responsive: {
		992: {
			items: 3
		}
	}
});

const productQuantity = document.querySelectorAll('[name="p_quantity"]');
productQuantity.forEach(item => {
	item.addEventListener('change', function(e){
		e.preventDefault();
		if( this.checked ){
			let newPrice = Number(this.value) * Number(this.dataset.price);
			document.querySelector('.cart input[type="number"]').value = this.value;
		}
	});
});


const sizeVariations = document.getElementById('size');
let customVars;
for (let i = 1; i < sizeVariations.options.length; i++) {
	let o = sizeVariations.options[i];
	if(i==1){
		customVars = `<li><input type="radio" name="custom_vars" data-value="${o.value}" id="var_${i}"><label for="var_${i}">${o.text}</label></li>`;
	} else {
		customVars += `<li><input type="radio" name="custom_vars" data-value="${o.value}" id="var_${i}"><label for="var_${i}">${o.text}</label></li>`;
	}
}
document.querySelector('.mg-variation').innerHTML = customVars;

const variators = a => {
	const variations = JSON.parse( document.querySelector('.variations_form').dataset.product_variations );
	for(let i = 0; i < variations.length; i++){
		if( variations[i].attributes.attribute_size == a ) {
			return variations[i].display_price;
			break;
		}
	}
}


const customVarsoBJ = document.querySelectorAll('[name="custom_vars"]');
customVarsoBJ.forEach(item => {
	item.addEventListener('change', function(e){
		e.preventDefault();
		if( this.checked ){
			sizeVariations.value = this.dataset.value;

			const event = document.createEvent('HTMLEvents');
			event.initEvent('change', true, false);
			sizeVariations.dispatchEvent(event);

			document.querySelector('.summary .price').innerHTML = '$'+variators(this.dataset.value);
		}
	});
});

setTimeout( ()=>{
	document.getElementById('var_1').click();
}, 0);

  const galleryNav = document.querySelectorAll('.gallery-nav li');
  galleryNav.forEach(item => {
    item.addEventListener('click', e => {
      e.preventDefault();
      const imgUrl = e.target.dataset.src;
      document.querySelector('.gallery-img').src = imgUrl;
    });
  });

  const tabs = document.querySelectorAll('[data-tab]');
  tabs.forEach(tab => {
    tab.addEventListener('click', e => {
      e.preventDefault();
      const tab = '.'+e.target.dataset.tab;
      document.querySelector('.producto-tabs__tab.active').classList.remove('active');
      document.querySelector('.producto-tabs__nav li.active').classList.remove('active');
      document.querySelector(tab).classList.add('active');
      e.target.classList.add('active');
    });
  });