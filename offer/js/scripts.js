(function(){

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

  const tabs = document.querySelectorAll('[data-tab]');
  tabs.forEach(tab => {
    tab.addEventListener('click', e => {
      e.preventDefault();
      const tab = '.'+e.target.dataset.tab;
      document.querySelector('.products-tabs__tab.active').classList.remove('active');
      document.querySelector('.products-tabs__nav li.active').classList.remove('active');
      document.querySelector(tab).classList.add('active');
      e.target.classList.add('active');
    });
  });

  const resps = document.querySelectorAll('[data-resp]');
  resps.forEach(resp => {
    resp.addEventListener('click', e => {
      e.preventDefault();
      const res = '.'+e.target.dataset.resp;
      document.querySelector('.resp-tab.active').classList.remove('active');
      document.querySelector('.resp-nav li.active').classList.remove('active');
      document.querySelector(res).classList.add('active');
      e.target.classList.add('active');
    });
  });

  const galleryNav = document.querySelectorAll('.gallery-nav li');
  galleryNav.forEach(item => {
    item.addEventListener('click', e => {
      e.preventDefault();
      const imgUrl = e.target.dataset.src;
      document.querySelector('.gallery-img').src = imgUrl;
    });
  });

  const questions = document.querySelectorAll('.question');
  questions.forEach(question => {
    question.addEventListener('click', e => {
      e.preventDefault();
      e.target.parentNode.classList.toggle('active');
    });
  });

})();