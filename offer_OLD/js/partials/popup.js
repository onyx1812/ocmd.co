let openPopup = document.querySelectorAll('[data-popup]');
for (let i = 0; i < openPopup.length; i++) {
	openPopup[i].addEventListener('click', function(e){
		e.preventDefault();
		let popupID = e.target.dataset.popup,
			popupContent = document.getElementById(popupID).innerHTML,
			popupContentBox = document.getElementById('popupContentBox');
		popupContentBox.innerHTML = popupContent;
		document.body.classList.add('popup-active');
	});
}

let closePopup = document.getElementById('closePopup');
closePopup.addEventListener('click', function(e){
	e.preventDefault();
	document.body.classList.remove('popup-active');
});