//Init klaviyo user
(function(){

	const validateKlaviyoEmail = email=>{
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}

	document.getElementById('email').addEventListener('change', e =>{
		if( validateKlaviyoEmail(e.target.value) )
			_learnq.push(['identify', {
				'$email' : e.target.value
			}]);
	});

	document.getElementById('first_name').addEventListener('change', e =>{
		if(e.target.value.length > 2)
			_learnq.push(['identify', {
				'first_name' : e.target.value
			}]);
	});

	document.getElementById('last_name').addEventListener('change', e =>{
		if(e.target.value.length > 2)
			_learnq.push(['identify', {
				'last_name' : e.target.value
			}]);
	});

	//=====END INIT klaviyo user when email is changed=====

})();

const KlaviyoSC = () =>{
	var WCK = WCK || {};
	WCK.trackStartedCheckout = function () {
		_learnq.push(["track", ("Started Checkout Cream"), {
			"CurrencySymbol": "$",
			"Currency": "USD",
			"Product name": "Cream",
			"$value": document.getElementById('grandTotal').innerHTML
		}]);
	};
	WCK.trackStartedCheckout();
	console.log('Klaviyo Started Checkout');
}

const KlaviyoPO = id =>{
	var WPO = WPO || {};
	WPO.trackPlacedOrder = function () {
		_learnq.push(["track", ("Placed Order Cream"), {
			"CurrencySymbol": "$",
			"Currency": "USD",
			"Product name": "Cream",
			"ID": id,
			"$value": document.getElementById('grandTotal').innerHTML
		}]);
	};
	WPO.trackPlacedOrder();
	console.log('Klaviyo Placed Order');
}