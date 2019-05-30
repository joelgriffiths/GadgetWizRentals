/*js
//==>>>==>>>==>>>==>>>==>>>==>>>==>>>==>>>==>>>==>>>==>>>==>>>==>>>
//
// Ajax ContactUs Script v1.01
// Copyright (c) phpkobo.com ( http://www.phpkobo.com/ )
// Email : admin@phpkobo.com
// ID : AC201-101
// URL : http://www.phpkobo.com/ajax_contactus.php
//
// This software is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; version 2 of the
// License.
//
//==<<<==<<<==<<<==<<<==<<<==<<<==<<<==<<<==<<<==<<<==<<<==<<<==<<<
*/
(function($){

//----------------------------------------------------------------
// CWaitIcon
//----------------------------------------------------------------
function CWaitIcon( url_img )
{
	var s = '';
	s += "<img ";
	s += "src='" + url_img + "'";
	s += ">";
	this.img = $( s );

	this.img.css({
		"position":"absolute",
		"left":"-10000px",
		"top":"-10000px"
	});

	this.img.hide();
	this.img.appendTo( $( 'body' ) );
}

CWaitIcon.prototype =
{
	show : function( e )
	{
		var w = 32;
		var h = 32;
		this.img.css( { "left":(e.pageX - w/2) + "px",
			"top":(e.pageY - h/2) + "px" } );
		this.img.show();
	},

	hide : function()
	{
		this.img.hide();
	}
}

//----------------------------------------------------------------
// serializeJSON
//----------------------------------------------------------------
$.fn.serializeJSON = function()
{
	var json = {};
	jQuery.map($(this).serializeArray(), function(n, i)
	{
		json[n['name']] = n['value'];
	});
	return json;
};

//----------------------------------------------------------------
// CAjaxMail
//----------------------------------------------------------------
function CAjaxMail()
{
}

CAjaxMail.prototype =
{
	//-----------------------------------------------
	// send
	//-----------------------------------------------
	send : function( data_in )
	{
		var _this = this;
		$.post( this.url_server,
			data_in,
			function(data) {
				_this.wait_icon.hide();
				var res = eval('(' + data + ')');
				if ( res.result == 'OK' )
				{
					_this.form.hide('slow');
					_this.form.after( _this.msg_thankyou );
				}
				else
				{
                    var myCaptcha = document.getElementById('captcha');
                    myCaptcha.src = "captcha.php?different="+Math.random();
					alert( res.result );
				}
		});
	},

	//-----------------------------------------------
	// run
	//-----------------------------------------------
	run : function()
	{
		var _this = this;
		$( '.ajaxmail-send' ).click( function(e){
			var form = $(this).parents( 'form' ).eq(0);
			var action = form.attr('action');
			var pos = action.lastIndexOf("/");
			var url_server = 'contactus.php';
			var url_image = 'images/wait.gif';
			if ( pos != -1 )
			{
				var path = action.substring( 0, pos+1 );
				url_server = path + url_server;
				url_image = path + url_image;
			}
			_this.url_server = url_server;
			_this.wait_icon = new CWaitIcon( url_image );
			_this.wait_icon.show(e);
			_this.form = form;
			var data = form.serializeJSON();
			data['=cmd'] = 'send';
			_this.msg_thankyou = data['=msg_thankyou'];
			delete data['=msg_thankyou'];
			_this.send( data );
		});
	}
}

//----------------------------------------------------------------
// ready
//----------------------------------------------------------------
$(document).ready(function() {
	var obj = new CAjaxMail(); 
	obj.run();
});

}(jQuery));
