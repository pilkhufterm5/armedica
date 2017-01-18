/*
 * SimpleModal Contact Form
 * http://www.ericmmartin.com/projects/simplemodal/
 * http://code.google.com/p/simplemodal/
 *
 * Copyright (c) 2009 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Revision: $Id: contact.js 212 2009-09-03 05:33:44Z emartin24 $
 *
 */

$(document).ready(function () {
	$('#contact-form input.contact, #contact-form a.contact').click(function (e) {
		e.preventDefault();
		// load the contact form using ajax
		$.get("tetris/data/contact.php",{'id':this.id},function(data){
			// create a modal dialog with the data
			$(data).modal({
				closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
				position: ["15%",],
				overlayId: 'contact-overlay',
				containerId: 'contact-container',
				onOpen: contact.open,
				onShow: contact.show,
				onClose: contact.close
			});
		});
	});
	
	$(".draggable").draggable({helper:'clone',revert: 'invalid'});
	$(".droppable").droppable({
	  accept: function(draggable){
		  //alert($(draggable).attr("id"));
		  if(this.id == $(draggable).attr("value"))
			return true;
		  else
			return false;
	  },
	  hoverClass: 'ui-state-active', 
      	  drop: function(event,ui) {
		   /*for(aaa in ui){
		   alert(aaa); 
		   }*/
		   /*for(aaa in ui.helper){
			   alert(aaa + ': ' + ui.draggable[aaa]);
		   }*/
		   //alert($(ui.draggable).attr("value"));
		   //alert(this.id + ' = ' + $(ui.draggable).attr("value"));
		   //var myDiv = $(ui.helper).clone();
		   //$(myDiv).prependTo(this);
		   var fecha = $(this).attr('fecha');
		   var top = $(this).position().top;
		   var left = $(this).position().left;
		   var hgt = $(this).height();
		   var wdt = $(this).width();
		   var myDiv = $(ui.helper).clone();
		   $(myDiv).prependTo(this);
		   $(myDiv).css('height',(hgt - 18)).css('width',(wdt - 19)).css('top',top).css('left',left);
		   //$(myDiv).css('top',top).css('left',left);
		   $(myDiv).draggable({helper:'clone',revert: 'invalid'});
		   //$(myDiv).css('width',wdt);
		   //$(myDiv).css('top',top);
		   //$(myDiv).css('left',left);
		   //$(myDiv).addClass('dropped');
		   // 32 - 143
		   
		   $.ajax({
				async: true,
				type: "POST",
				url: "tetris/data/clone_event.php",
				datatype: "json",
				data: "id_reg=" + $(myDiv).attr("id_reg") + "&id_div_drag=" + $(myDiv).attr("id") + "&fecha=" + fecha,
				success: function(responseText){
					var json = eval("(" + responseText + ")");
					//alert( "id del registro: " + json.class );
					var elem = json.elem;
					//alert( elem + '=' + $(myDiv).attr("id"));
					//$('#'+elem).addClass('colch');
					//var div = document.getElementById(elem);
					//$(div).addClass('colch');
				}
			});
	  }
    });
	
	$(".tam").click(function (){
		if($(this).hasClass("tent")){
			$(this).text("");
			$(this).removeClass("tent");
		}
		else{
			$(this).addClass("tent");
			$(this).addClass("draggable");
		}
			
		/*$.ajax({
			async: true,
			type: "POST",
			url: "update.php",
			datatype: "json",
			data: "data=" + this.id,
			success: function(responseText){
				var json = eval("(" + responseText + ")");
				//alert( "Data Saved: " + json.class );
			}
		});*/
		
		if($(this).hasClass("tent")){
			$.get("tetris/data/client.php",{'id':this.id},function(data){
				// create a modal dialog with the data
				$(data).modal({
					closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
					position: ["15%",],
					overlayId: 'contact-overlay',
					containerId: 'contact-container',
					onOpen: contact2.open,
					onShow: contact2.show,
					onClose: contact2.close
				});
			});
		}
	});

	// preload images
	var img = ['cancel.png', 'form_bottom.gif', 'form_top.gif', 'loading.gif', 'send.png'];
	$(img).each(function () {
		var i = new Image();
		i.src = 'tetris/img/contact/' + this;
	});

	$('#layer1').Draggable(
		{
			zIndex: 20,
			ghosting:false,
			opacity: 0.7,
			handle:	'#layer1_handle'
		}
	);	
	$('#layer1_form').ajaxForm({
		target: '#content',
		success: function() 
		{
			$("#layer1").hide();
		}				
	});			
	$("#layer1").hide();
				
	$('#preferences').click(function()
	{
		$("#layer1").show();
	});
	
	$('#close').click(function()
	{
		$("#layer1").hide();
	});
});

var contact = {
	message: null,
	open: function (dialog) {
		// add padding to the buttons in firefox/mozilla
		if ($.browser.mozilla) {
			$('#contact-container .contact-button').css({
				'padding-bottom': '2px'
			});
		}
		// input field font size
		if ($.browser.safari) {
			$('#contact-container .contact-input').css({
				'font-size': '.9em'
			});
		}

		// dynamically determine height
		var h = 280;
		if ($('#contact-subject').length) {
			h += 26;
		}
		if ($('#contact-cc').length) {
			h += 22;
		}

		var title = $('#contact-container .contact-title').html();
		$('#contact-container .contact-title').html('Cargando formulario...');
		dialog.overlay.fadeIn(200, function () {
			dialog.container.fadeIn(200, function () {
				dialog.data.fadeIn(200, function () {
					$('#contact-container .contact-content').animate({
						height: h
					}, function () {
						$('#contact-container .contact-title').html(title);
						$('#contact-container form').fadeIn(200, function () {
							$('#contact-container #contact-name').focus();

							$('#contact-container .contact-cc').click(function () {
								var cc = $('#contact-container #contact-cc');
								cc.is(':checked') ? cc.attr('checked', '') : cc.attr('checked', 'checked');
							});

							// fix png's for IE 6
							if ($.browser.msie && $.browser.version < 7) {
								$('#contact-container .contact-button').each(function () {
									if ($(this).css('backgroundImage').match(/^url[("']+(.*\.png)[)"']+$/i)) {
										var src = RegExp.$1;
										$(this).css({
											backgroundImage: 'none',
											filter: 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' +  src + '", sizingMethod="crop")'
										});
									}
								});
							}
						});
					});
				});
			});
		});
	},
	show: function (dialog) {
		$('#contact-container .contact-send').click(function (e) {
			e.preventDefault();
			// validate form
			if (contact.validate()) {
				var msg = $('#contact-container .contact-message');
				msg.fadeOut(function () {
					msg.removeClass('contact-error').empty();
				});
				$('#contact-container .contact-title').html('Enviando...');
				$('#contact-container form').fadeOut(200);
				$('#contact-container .contact-content').animate({
					height: '80px'
				}, function () {
					$('#contact-container .contact-loading').fadeIn(200, function () {
						$.ajax({
							url: 'tetris/data/contact.php',
							data: $('#contact-container form').serialize() + '&action=send',
							type: 'post',
							cache: false,
							dataType: 'html',
							success: function (data) {
								$('#contact-container .contact-loading').fadeOut(200, function () {
									$('#contact-container .contact-title').html('Thank you!');
									msg.html(data).fadeIn(200);
								});
							},
							error: contact.error
						});
					});
				});
			}
			else {
				if ($('#contact-container .contact-message:visible').length > 0) {
					var msg = $('#contact-container .contact-message div');
					msg.fadeOut(200, function () {
						msg.empty();
						contact.showError();
						msg.fadeIn(200);
					});
				}
				else {
					$('#contact-container .contact-message').animate({
						height: '30px'
					}, contact.showError);
				}
				
			}
		});
	},
	close: function (dialog) {
		$('#contact-container .contact-message').fadeOut();
		$('#contact-container .contact-title').html('Gracias! Cerrando aplicaci&oacute;n...');
		$('#contact-container form').fadeOut(200);
		$('#contact-container .contact-content').animate({
			height: 40
		}, function () {
			dialog.data.fadeOut(200, function () {
				dialog.container.fadeOut(200, function () {
					dialog.overlay.fadeOut(200, function () {
						$.modal.close();
					});
				});
			});
		});
	},
	error: function (xhr) {
		alert(xhr.statusText);
	},
	validate: function () {
		/*contact.message = '';
		if (!$('#contact-container #contact-name').val()) {
			contact.message += 'Falta: Nombre. ';
		}

		var email = $('#contact-container #contact-email').val();
		if (!email) {
			contact.message += 'Falta: E-mail. ';
		}
		else {
			if (!contact.validateEmail(email)) {
				contact.message += 'E-mail inv&aacute;lido. ';
			}
		}

		if (!$('#contact-container #contact-message').val()) {
			contact.message += 'Falta: Mensaje.';
		}

		if (contact.message.length > 0) {
			return false;
		}
		else {
			return true;
		}*/
		return true;
	},
	validateEmail: function (email) {
		var at = email.lastIndexOf("@");

		// Make sure the at (@) sybmol exists and  
		// it is not the first or last character
		if (at < 1 || (at + 1) === email.length)
			return false;

		// Make sure there aren't multiple periods together
		if (/(\.{2,})/.test(email))
			return false;

		// Break up the local and domain portions
		var local = email.substring(0, at);
		var domain = email.substring(at + 1);

		// Check lengths
		if (local.length < 1 || local.length > 64 || domain.length < 4 || domain.length > 255)
			return false;

		// Make sure local and domain don't start with or end with a period
		if (/(^\.|\.$)/.test(local) || /(^\.|\.$)/.test(domain))
			return false;

		// Check for quoted-string addresses
		// Since almost anything is allowed in a quoted-string address,
		// we're just going to let them go through
		if (!/^"(.+)"$/.test(local)) {
			// It's a dot-string address...check for valid characters
			if (!/^[-a-zA-Z0-9!#$%*\/?|^{}`~&'+=_\.]*$/.test(local))
				return false;
		}

		// Make sure domain contains only valid characters and at least one period
		if (!/^[-a-zA-Z0-9\.]*$/.test(domain) || domain.indexOf(".") === -1)
			return false;	

		return true;
	},
	showError: function () {
		$('#contact-container .contact-message')
			.html($('<div class="contact-error"></div>').append(contact.message))
			.fadeIn(200);
	}
};



var contact2 = {
	message: null,
	open: function (dialog) {
		// add padding to the buttons in firefox/mozilla
		if ($.browser.mozilla) {
			$('#contact-container .contact-button').css({
				'padding-bottom': '2px'
			});
		}
		// input field font size
		if ($.browser.safari) {
			$('#contact-container .contact-input').css({
				'font-size': '.9em'
			});
		}

		// dynamically determine height
		var h = 280;
		if ($('#contact-subject').length) {
			h += 26;
		}
		if ($('#contact-cc').length) {
			h += 22;
		}

		var title = $('#contact-container .contact-title').html();
		$('#contact-container .contact-title').html('Cargando formulario...');
		dialog.overlay.fadeIn(200, function () {
			dialog.container.fadeIn(200, function () {
				dialog.data.fadeIn(200, function () {
					$('#contact-container .contact-content').animate({
						height: h
					}, function () {
						$('#contact-container .contact-title').html(title);
						$('#contact-container form').fadeIn(200, function () {
							$('#contact-container #contact-name').focus();

							$('#contact-container .contact-cc').click(function () {
								var cc = $('#contact-container #contact-cc');
								cc.is(':checked') ? cc.attr('checked', '') : cc.attr('checked', 'checked');
							});

							// fix png's for IE 6
							if ($.browser.msie && $.browser.version < 7) {
								$('#contact-container .contact-button').each(function () {
									if ($(this).css('backgroundImage').match(/^url[("']+(.*\.png)[)"']+$/i)) {
										var src = RegExp.$1;
										$(this).css({
											backgroundImage: 'none',
											filter: 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' +  src + '", sizingMethod="crop")'
										});
									}
								});
							}
						});
					});
				});
			});
		});
	},
	show: function (dialog) {
		$('#contact-container .contact-send').click(function (e) {
			e.preventDefault();
			// validate form
			if (contact2.validate()) {
				var msg = $('#contact-container .contact-message');
				msg.fadeOut(function () {
					msg.removeClass('contact-error').empty();
				});
				$('#contact-container .contact-title').html('Enviando...');
				$('#contact-container form').fadeOut(200);
				$('#contact-container .contact-content').animate({
					height: '80px'
				}, function () {
					$('#contact-container .contact-loading').fadeIn(200, function () {
						$.ajax({
							url: 'tetris/data/client.php',
							data: $('#contact-container form').serialize() + '&action=send',
							type: 'post',
							cache: false,
							dataType: 'html',
							success: function (data) {
								$('#contact-container .contact-loading').fadeOut(200, function () {
									$('#contact-container .contact-title').html('Thank you!');
									msg.html(data).fadeIn(200);
								});
							},
							error: contact2.error
						});
					});
				});
			}
			else {
				if ($('#contact-container .contact-message:visible').length > 0) {
					var msg = $('#contact-container .contact-message div');
					msg.fadeOut(200, function () {
						msg.empty();
						contact2.showError();
						msg.fadeIn(200);
					});
				}
				else {
					$('#contact-container .contact-message').animate({
						height: '30px'
					}, contact2.showError);
				}
				
			}
		});
	},
	close: function (dialog) {
		$('#contact-container .contact-message').fadeOut();
		$('#contact-container .contact-title').html('Gracias! Cerrando aplicaci&oacute;n...');
		$('#contact-container form').fadeOut(200);
		$('#contact-container .contact-content').animate({
			height: 40
		}, function () {
			dialog.data.fadeOut(200, function () {
				dialog.container.fadeOut(200, function () {
					dialog.overlay.fadeOut(200, function () {
						$.modal.close();
					});
				});
			});
		});
	},
	error: function (xhr) {
		alert(xhr.statusText);
	},
	validate: function () {
		contact2.message = '';
		/*if (!$('#contact-container #contact-name').val()) {
			contact.message += 'Falta: Nombre. ';
		}

		var email = $('#contact-container #contact-email').val();
		if (!email) {
			contact.message += 'Falta: E-mail. ';
		}
		else {
			if (!contact.validateEmail(email)) {
				contact.message += 'E-mail inv&aacute;lido. ';
			}
		}

		if (!$('#contact-container #contact-message').val()) {
			contact.message += 'Falta: Mensaje.';
		}

		if (contact.message.length > 0) {
			return false;
		}
		else {
			return true;
		}*/
		if (!$('#cmbclt').val()){
			contact2.message += 'Seleccione cliente. ';
		}
		if(contact2.message.length > 0){
			return false;
		}else{
			return true;
		}
	},
	showError: function () {
		$('#contact-container .contact-message')
			.html($('<div class="contact-error"></div>').append(contact2.message))
			.fadeIn(200);
	}
};
