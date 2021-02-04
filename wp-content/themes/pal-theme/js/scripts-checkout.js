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

  const validEmail = email => {const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; return re.test(email);},
    subscribeForm = document.getElementById('sForm'),
    s_email = document.getElementById('s_email');

  subscribeForm.addEventListener('submit', e => {
    e.preventDefault();

    if( validEmail(s_email.value) ){

      let formData = new FormData();

      formData.append('action', 'subscribeForm' );
      formData.append('email', s_email.value );

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
    } else {
      s_email.style.boxShadow = '0 0 16px red inset';
      setTimeout(()=>{
        s_email.style.boxShadow = 'none';
      }, 1000);
    }
  });
})();

(function(){
  let subscriptions = document.querySelectorAll('.product-total .subscription-price');
  if(subscriptions.length > 0){
    document.querySelector('.expect-list li:first-child').style.display = 'none';
    document.querySelector('.expect-list li:nth-child(2)').style.marginTop = '0';
  }
})();