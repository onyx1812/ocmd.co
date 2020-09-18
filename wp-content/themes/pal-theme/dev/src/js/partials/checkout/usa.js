(function(){

		const country = document.querySelector('#country');

		const removeDuplicates = array=>{
			return [...new Set(array)]
		}

		const changeState = ()=>{
			const stateBox = document.querySelector('#stateBox');
			if( country.value === 'US' ){
				stateBox.innerHTML = '<select name="state" id="state" required><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District Of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option></select><label for="state">State</label>';
			} else {
				stateBox.innerHTML = '<input type="text" name="state" id="state" placeholder="State" minlength="2" required><label for="state">State</label>';
			}
			changeCity();
		}

		const changeCity = ()=>{
			const cityeBox = document.querySelector('#cityBox');
			if( country.value === 'US' ){
				let citiesAll = [];
				usaData.forEach(item=>{
					if( item.state === document.querySelector('#state').value ){
						citiesAll.push(item.city);
					}
				});
				const cities = removeDuplicates(citiesAll);
				let citySelect = '<select name="city" id="city" required>';
				cities.forEach(item=>{
					citySelect += `<option value="${item}">${item}</option>`;
				});
				citySelect += '</select><label for="city">City</label>';
				cityeBox.innerHTML = citySelect;
				document.querySelector('#state').addEventListener('change', changeCity);
			} else {
				cityeBox.innerHTML = '<input type="text" name="city" id="city" placeholder="City" minlength="2" required><label for="city">City</label>';
			}
			changeZip();
		}

		const changeZip = ()=>{
			const zipeBox = document.querySelector('#zipBox');
			if( country.value === 'US' ){
				let zipsAll = [];
				usaData.forEach(item=>{
					if( item.city === document.querySelector('#city').value && item.state === document.querySelector('#state').value){
						zipsAll.push(item.zip_code);
					}
				});
				const zips = removeDuplicates(zipsAll);
				let zipSelect = '<select name="zip" id="zip" required>';
				zips.forEach(item=>{
					zipSelect += `<option value="${item}">${item}</option>`;
				});
				zipSelect += '</select><label for="zip">Zip</label>';
				zipBox.innerHTML = zipSelect;
				document.querySelector('#city').addEventListener('change', changeZip);
			} else {
				zipeBox.innerHTML = '<input type="text" name="zip" id="zip" placeholder="ZIP" minlength="5" maxlength="5" required><label for="zip">Zip</label>';
			}
		}

		document.addEventListener('DOMContentLoaded', changeState);
		document.querySelector('#country').addEventListener('change', changeState);

})();