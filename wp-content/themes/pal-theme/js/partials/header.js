(function(){
	const mainNavBtn = document.getElementById('mainNavBtn');
	mainNavBtn.addEventListener('click', function(e){
		e.preventDefault();
		document.getElementById('mainNav').classList.toggle('active');
		this.classList.toggle('active');
	});
})();