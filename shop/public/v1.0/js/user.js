(function($){
	// Login
	$.fn.loginUser = function(arg){
		var settings = $.extend({
			form : 'login',
			dialog: $('div.dialogBox')
		},arg);
		
		var form = $('form#'+settings.form);
		
		this.click(function(){
			var hasError = false;
			
			$.each(form.serializeArray(),function(i,v){
				var isRequired 	= (typeof form.find('#'+v.name).attr('required') !== 'undefined') ? true : false;
				var elem 		= form.find('#'+v.name);
								
				// Required check
				if(isRequired && v.value == ''){
					hasError = 'Adja meg a bejelentkezési adatait!';
					$(form[0][i]).css({
						border : '1px solid #f50'	
					})
					.attr('placeholder','kötelező kitölteni');
				}else{
					$(form[0][i]).css({
						border : '1px solid #009900'	
					});
				}
					
			});
			
						
			if(hasError){
				var msg = '<div id="errorMsg" style="color:red; padding:5px;">'+hasError+'</div>';
				if($('#errorMsg').length == 0){
					$(msg).insertAfter($(this));
				}else{
					$('#errorMsg').html(hasError);
				}
			}
			
			if(!hasError){
				form.find('#errorMsg').remove();
				
				$(this)
					.html('<i class="fa fa-spinner fa-spin"></i> folyamatban...')
					.attr('disabled','disabled')
					.removeClass('btn-warning')
					.addClass('btn-success');
					
				var $parent = $(this);
				
				$.post('/ajax/post',{
					type : 'user',
					mode : 'login',
					data : form.serializeArray()
				},function(d){
					var c = $.parseJSON(d);
					console.log(c);
						if(c.success == 0){
							$parent
								.html('Bejelentkezés <i class="fa fa-arrow-circle-right"></i>')
								.removeAttr('disabled')
								.removeClass('btn-success')
								.addClass('btn-warning');
							var msg = '<div id="errorMsg" style="color:red; padding:5px;">'+c.msg+'</div>';
							
							if($('#errorMsg').length == 0){
								$(msg).insertAfter($parent);
							}else{
								$('#errorMsg').html(c.msg);
							}
							
							form.find('input[excCode='+c.errorCode+']').css({
								border : '1px solid #f50'	
							});
						}else{
							var successLoginMsg = '<div align="center" style="padding:15px;">';
							successLoginMsg += "<h2><i style=\"color:green; font-size:75px;\" class=\"fa fa-check-circle\"></i><br>Sikeresen bejelentkezett!</h2>";
							successLoginMsg += "Az oldal 5 másodperc múlva frissülni fog!";
							successLoginMsg += "<br><br><a href=\"javascript:document.location.reload(true);\">Frissítés most</a>";
							successLoginMsg += "</div>";
							
							setTimeout(function(){
								document.location.reload(true);	
							},5000);
							
							settings.dialog.find('.content .c').html(successLoginMsg);
						}
				},"html");
			}
		});
	}
	
	$.fn.resetPassword = function(arg){
		var settings = $.extend({
			form : 'resetPassword',
			dialog: $('div.dialogBox')
		},arg);
		
		var form = $('form#'+settings.form);
		
		this.closeDialog = function(){
			settings.dialog.remove();
		}
		
		this.click(function(){
			var hasError = false;
			
			$.each(form.serializeArray(),function(i,v){
				var isRequired 	= (typeof form.find('#'+v.name).attr('required') !== 'undefined') ? true : false;
				var elem 		= form.find('#'+v.name);
								
				// Required check
				if(isRequired && v.value == ''){
					hasError = 'Adja meg az e-mail címét!';
					$(form[0][i]).css({
						border : '1px solid #f50'	
					})
					.attr('placeholder','kötelező kitölteni');
				}else{
					$(form[0][i]).css({
						border : '1px solid #009900'	
					});
				}
					
			});
			
						
			if(hasError){
				var msg = '<div id="errorMsg" style="color:red; padding:5px;">'+hasError+'</div>';
				if($('#errorMsg').length == 0){
					$(msg).insertAfter($(this));
				}else{
					$('#errorMsg').html(hasError);
				}
			}
			
			if(!hasError){
				form.find('#errorMsg').remove();
				
				$(this)
					.html('<i class="fa fa-spinner fa-spin"></i> folyamatban...')
					.attr('disabled','disabled')
					.removeClass('btn-warning')
					.addClass('btn-success');
					
				var $parent = $(this);
				
				$.post('/ajax/post',{
					type : 'user',
					mode : 'resetPassword',
					data : form.serializeArray()
				},function(d){
					var c = $.parseJSON(d);
					console.log(form.serializeArray());
						if(c.success == 0){
							$parent
								.html('Bejelentkezés <i class="fa fa-arrow-circle-right"></i>')
								.removeAttr('disabled')
								.removeClass('btn-success')
								.addClass('btn-warning');
							var msg = '<div id="errorMsg" style="color:red; padding:5px;">'+c.msg+'</div>';
							
							if($('#errorMsg').length == 0){
								$(msg).insertAfter($parent);
							}else{
								$('#errorMsg').html(c.msg);
							}
							
							form.find('input[excCode='+c.errorCode+']').css({
								border : '1px solid #f50'	
							});
						}else{
							var successLoginMsg = '<div align="center" style="padding:15px;">';
							successLoginMsg += "<h2><i style=\"color:green; font-size:75px;\" class=\"fa fa-check-circle\"></i><br>Jelszó sikeresen újragenerálva!</h2>";
							successLoginMsg += "E-mail címére elküldtük az új jelszavát! Bejelentkezés után változtassa meg.";
							successLoginMsg += "<br><br><a href=\"javascript:document.location.reload(true);\">Rendben</a>";
							successLoginMsg += "</div>";
							
							setTimeout(function(){
								document.location.reload(true);	
							},5000);
							
							settings.dialog.find('.content .c').html(successLoginMsg);
						}
				},"html");
			}
		});
		
		return this;
	}
	
	// Reg user handler
	$.fn.registerUser = function(arg){
		var settings = $.extend({
			form : 'register',
			dialog: $('div.dialogBox')
		},arg);
		
		var form = $('form#'+settings.form);
		
		this.closeDialog = function(){
			settings.dialog.remove();
		}
		
		this.click(function(){
			var hasError = false;
			var pw1 = false;
			var pw2 = false;
			
			$.each(form.serializeArray(),function(i,v){
				var isRequired = (typeof form.find('#'+v.name).attr('required') !== 'undefined') ? true : false;
				var elem = form.find('#'+v.name);
				
				// Required check
				if(isRequired && v.value == ''){
					hasError = 'Hiányzó adatok! Kérjük pótolja.';
					$(form[0][i]).css({
						border : '1px solid #f50'	
					})
					.attr('placeholder','kötelező kitölteni');
				}else{
					$(form[0][i]).css({
						border : '1px solid #009900'	
					});
				}
				
				// Password compare
				
				if(elem.attr('type') == 'password'){
					if(elem.attr('name') == 'pw1' && elem.val() != ''){
						pw1 = elem.val();	
					}
					if(elem.attr('name') == 'pw2' && elem.val() != ''){
						pw2 = elem.val();	
					}
				}
				
			});
						
			if(hasError){
				var msg = '<div id="errorMsg" style="color:red; padding:5px;">'+hasError+'</div>';
				if($('#errorMsg').length == 0){
					$(msg).insertAfter($(this));
				}else{
					$('#errorMsg').html(hasError);
				}
			}
			
			if(!hasError && (pw1 !== pw2)){
				hasError = true;
				var m 	= 'A megadott jelszavak nem egyeznek.';
				var msg = '<div id="errorMsg" style="color:red; padding:5px;">'+m+'</div>';
				form.find('#pw1,#pw2').css({
					border : '1px solid #f50'	
				});
				if($('#errorMsg').length == 0){
					$(msg).insertAfter($(this));
				}else{
					$('#errorMsg').html(m);
				}
			}
			
			if(!hasError && !form.find('#aszfOk').is(':checked')){
				hasError = true;
				var m 	= 'Az ÁSZF elfogadása kötelező!';
				var msg = '<div id="errorMsg" style="color:red; padding:5px;">'+m+'</div>';
				form.find('#aszfOk + label').css({
					color : '#f50'	
				});
				if($('#errorMsg').length == 0){
					$(msg).insertAfter($(this));
				}else{
					$('#errorMsg').html(m);
				}
			}
			
			
			if(!hasError){
				form.find('#errorMsg').remove();
				
				$(this)
					.html('<i class="fa fa-spinner fa-spin"></i> folyamatban...')
					.attr('disabled','disabled')
					.removeClass('btn-warning')
					.addClass('btn-success');
				var $parent = $(this);
				
				$.post('/ajax/post',{
					type : 'user',
					mode : 'add',
					data : form.serializeArray()
				},function(d){
					var c = $.parseJSON(d);
					console.log(c);
					if(c.success == 0){
						$parent
							.html('Regisztráció <i class="fa fa-check"></i>')
							.removeAttr('disabled')
							.removeClass('btn-success')
							.addClass('btn-warning');
						var msg = '<div id="errorMsg" style="color:red; padding:5px;">'+c.msg+'</div>';
						
						if($('#errorMsg').length == 0){
							$(msg).insertAfter($parent);
						}else{
							$('#errorMsg').html(c.msg);
						}
						
						form.find('input[excCode='+c.errorCode+']').css({
							border : '1px solid #f50'	
						});
					}else{
						var successLoginMsg = '<div align="center" style="padding:15px;">';
						successLoginMsg += "<h2><i style=\"color:green; font-size:75px;\" class=\"fa fa-check-circle\"></i><br>Sikeresen regisztált rendszerünkbe!</h2>";
						successLoginMsg += "Kellemes időtöltést és vásárlást kívánunk!";
						successLoginMsg += "<br><a href=\"/p/kedvezmeny\"><strong>Kedvezmények regisztrált tagoknak, részletek =></strong></a>";
						successLoginMsg += "<br><br><a href=\"javascript:document.location.reload(true);\">Frissítés most</a>";
						successLoginMsg += " | <a href=\"javascript:void(0);\" jOpen=\"login\">Belépés</a>";
						successLoginMsg += "</div>";
						
						settings.dialog.find('.content .c').html(successLoginMsg);
					}
				},"html");
			}
			
			return false;
		});
		
		return this;
	}
}(jQuery));