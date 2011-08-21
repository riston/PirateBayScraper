(function() {
	var input = $('#images');
	var formData = false;
	
	if (window.FormData) {
		formdata = new FormData();
		$('#btn').hide();
	}
	
	if (input.addEventListener) {
		input.addEventListener('change', function(event) {
			$('#response').setHtml('Loading...');
			
			for (i = 0; i < this.files.length; i++) {
				file = this.files[i];
				if (!!file.type.match(/image.*/)) {
					if (window.FileReader) {
						reader = new FileReader();
						reader.onloadend = function (e) { 
							showUploadedItem(e.target.result, file.fileName);
						};
						reader.readAsDataURL(file);
					}
					if (formdata) {
						formdata.append("images[]", file);
					}
				}
				
				if(formdata) {
					$.ajax({
						url : "upload.php",
						type : "POST",
						data : formdata,
						processData : false,
						contentType : false,
						success : function(res) {
							document.getElementById("response").innerHTML = res;
						}
					});
				}
			}
		}, false);	
	}
	

	function showUploadedItem(source) {
		var list = $('#image-list').append(
			$('<li />').append($('<img />')));
		
	}
})();
