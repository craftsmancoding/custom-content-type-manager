/*
  mColorPicker
  Version: 1.0 r34
  
  Copyright (c) 2010 Meta100 LLC.
  http://www.meta100.com/
  
  Licensed under the MIT license 
  http://www.opensource.org/licenses/mit-license.php 
*/

// After this script loads set:
// $.fn.mColorPicker.init.replace = '.myclass'
// to have this script apply to input.myclass,
// instead of the default input[type=color]
// To turn of automatic operation and run manually set:
// $.fn.mColorPicker.init.replace = false
// To use manually call like any other jQuery plugin
// $('input.foo').mColorPicker({options})
// options:
// imageFolder - Change to move image location.
// swatches - Initial colors in the swatch, must an array of 10 colors.
// init:
// $.fn.mColorPicker.init.enhancedSwatches - Turn of saving and loading of swatch to cookies.
// $.fn.mColorPicker.init.allowTransparency - Turn off transperancy as a color option.
// $.fn.mColorPicker.init.showLogo - Turn on/off the meta100 logo (You don't really want to turn it off, do you?).

jQuery(document).ready(add_color_controls());

function add_colorselector_instance(fieldname) {
	// Increment the instance
	cctm[fieldname] = cctm[fieldname] + 1;
	
	var data = {
	        "action" : 'get_tpl',
	        "fieldname" : fieldname,
	        "instance" : cctm[fieldname],
	        "get_tpl_nonce" : cctm.ajax_nonce
	    };

	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	//alert('cctm_instance_wrapper_'+fieldname);
	    	// Write the response to the div
			jQuery('#cctm_instance_wrapper_'+fieldname).append(response);
			add_color_controls();
	    }
	);

	return false;
}

/*------------------------------------------------------------------------------

------------------------------------------------------------------------------*/
function add_color_controls() {

  var $o;

  jQuery.fn.mColorPicker = function(options) {

    $o = jQuery.extend(jQuery.fn.mColorPicker.defaults, options);  

    if ($o.swatches.length < 10) $o.swatches = jQuery.fn.mColorPicker.defaults.swatches
    if (jQuery("div#mColorPicker").length < 1) jQuery.fn.mColorPicker.drawPicker();

    if (jQuery('#css_disabled_color_picker').length < 1) jQuery('head').prepend('<style id="css_disabled_color_picker" type="text/css">.mColorPicker[disabled] + span, .mColorPicker[disabled="disabled"] + span, .mColorPicker[disabled="true"] + span {filter:alpha(opacity=50);-moz-opacity:0.5;-webkit-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5;}</style>');

    jQuery('.mColorPicker').live('keyup', function () {

      try {
  
        jQuery(this).css({
          'background-color': jQuery(this).val()
        }).css({
          'color': jQuery.fn.mColorPicker.textColor(jQuery(this).css('background-color'))
        }).trigger('change');
      } catch (r) {}
    });

    jQuery('.mColorPickerTrigger').live('click', function () {

      jQuery.fn.mColorPicker.colorShow(jQuery(this).attr('id').replace('icp_', ''));
    });

    this.each(function () {

      jQuery.fn.mColorPicker.drawPickerTriggers(jQuery(this));
    });

    return this;
  };

  jQuery.fn.mColorPicker.currentColor = false;
  jQuery.fn.mColorPicker.currentValue = false;
  jQuery.fn.mColorPicker.color = false;

  jQuery.fn.mColorPicker.init = {
    replace: '[type=color]',
    index: 0,
    enhancedSwatches: true,
    allowTransparency: true,
  	checkRedraw: 'DOMUpdated', // Change to 'ajaxSuccess' for ajax only or false if not needed
  	liveEvents: false,
    showLogo: true
  };

  jQuery.fn.mColorPicker.defaults = {
    imageFolder: '../wp-content/plugins/custom-content-type-manager/js/images/',
    swatches: [
      "#ffffff",
      "#ffff00",
      "#00ff00",
      "#00ffff",
      "#0000ff",
      "#ff00ff",
      "#ff0000",
      "#4c2b11",
      "#3b3b3b",
      "#000000"
    ]
  };

  jQuery.fn.mColorPicker.liveEvents = function() {

    jQuery.fn.mColorPicker.init.liveEvents = true;

    if (jQuery.fn.mColorPicker.init.checkRedraw && jQuery.fn.mColorPicker.init.replace) {

      jQuery(document).bind(jQuery.fn.mColorPicker.init.checkRedraw + '.mColorPicker', function () {

        jQuery('input[data-mcolorpicker!="true"]').filter(function() {
    
          return (jQuery.fn.mColorPicker.init.replace == '[type=color]')? this.getAttribute("type") == 'color': jQuery(this).is(jQuery.fn.mColorPicker.init.replace);
        }).mColorPicker();
      });
    }
  };

  jQuery.fn.mColorPicker.drawPickerTriggers = function ($t) {

    if ($t[0].nodeName.toLowerCase() != 'input') return false;

    var id = $t.attr('id') || 'color_' + jQuery.fn.mColorPicker.init.index++,
        hidden = false;

    $t.attr('id', id);
  
    if ($t.attr('text') == 'hidden' || $t.attr('data-text') == 'hidden') hidden = true;

    var color = $t.val(),
        width = ($t.width() > 0)? $t.width(): parseInt($t.css('width'), 10),
        height = ($t.height())? $t.height(): parseInt($t.css('height'), 10),
        flt = $t.css('float'),
        image = (color == 'transparent')? "url('" + $o.imageFolder + "/grid.gif')": '',
        colorPicker = '';

    jQuery('body').append('<span id="color_work_area"></span>');
    jQuery('span#color_work_area').append($t.clone(true));
    colorPicker = jQuery('span#color_work_area').html().replace(/type="color"/gi, '').replace(/input /gi, (hidden)? 'input type="hidden"': 'input type="text"');
    jQuery('span#color_work_area').html('').remove();
    $t.after(
      (hidden)? '<span style="cursor:pointer;border:1px solid black;float:' + flt + ';width:' + width + 'px;height:' + height + 'px;" id="icp_' + id + '">&nbsp;</span>': ''
    ).after(colorPicker).remove();   

    if (hidden) {

      jQuery('#icp_' + id).css({
        'background-color': color,
        'background-image': image,
        'display': 'inline-block'
      }).attr(
        'class', jQuery('#' + id).attr('class')
      ).addClass(
        'mColorPickerTrigger'
      );
    } else {

      jQuery('#' + id).css({
        'background-color': color,
        'background-image': image
      }).css({
        'color': jQuery.fn.mColorPicker.textColor(jQuery('#' + id).css('background-color'))
      }).after(
        '<span style="cursor:pointer;" id="icp_' + id + '" class="mColorPickerTrigger"><img src="' + $o.imageFolder + 'color.png" style="border:0;margin:0 0 0 3px" align="absmiddle"></span>'
      ).addClass('mColorPickerInput');
    }

    jQuery('#icp_' + id).attr('data-mcolorpicker', 'true');

    jQuery('#' + id).addClass('mColorPicker');

    return jQuery('#' + id);
  };

  jQuery.fn.mColorPicker.drawPicker = function () {

    jQuery(document.createElement("div")).attr(
      "id","mColorPicker"
    ).css(
      'display','none'
    ).html(
      '<div id="mColorPickerWrapper"><div id="mColorPickerImg" class="mColor"></div><div id="mColorPickerImgGray" class="mColor"></div><div id="mColorPickerSwatches"><div class="mClear"></div></div><div id="mColorPickerFooter"><input type="text" size="8" id="mColorPickerInput"/></div></div>'
    ).appendTo("body");

    jQuery(document.createElement("div")).attr("id","mColorPickerBg").css({
      'display': 'none'
    }).appendTo("body");

    for (n = 9; n > -1; n--) {

      jQuery(document.createElement("div")).attr({
        'id': 'cell' + n,
        'class': "mPastColor" + ((n > 0)? ' mNoLeftBorder': '')
      }).html(
        '&nbsp;'
      ).prependTo("#mColorPickerSwatches");
    }

    jQuery('#mColorPicker').css({
      'border':'1px solid #ccc',
      'color':'#fff',
      'z-index':999998,
      'width':'194px',
      'height':'184px',
      'font-size':'12px',
      'font-family':'times'
    });

    jQuery('.mPastColor').css({
      'height':'18px',
      'width':'18px',
      'border':'1px solid #000',
      'float':'left'
    });

    jQuery('#colorPreview').css({
      'height':'50px'
    });

    jQuery('.mNoLeftBorder').css({
      'border-left':0
    });

    jQuery('.mClear').css({
      'clear':'both'
    });

    jQuery('#mColorPickerWrapper').css({
      'position':'relative',
      'border':'solid 1px gray',
      'z-index':999999
    });
    
    jQuery('#mColorPickerImg').css({
      'height':'128px',
      'width':'192px',
      'border':0,
      'cursor':'crosshair',
      'background-image':"url('" + $o.imageFolder + "colorpicker.png')"
    });
    
    jQuery('#mColorPickerImgGray').css({
      'height':'8px',
      'width':'192px',
      'border':0,
      'cursor':'crosshair',
      'background-image':"url('" + $o.imageFolder + "graybar.jpg')"
    });
    
    jQuery('#mColorPickerInput').css({
      'border':'solid 1px gray',
      'font-size':'10pt',
      'margin':'3px',
      'width':'80px'
    });
    
    jQuery('#mColorPickerImgGrid').css({
      'border':0,
      'height':'20px',
      'width':'20px',
      'vertical-align':'text-bottom'
    });
    
    jQuery('#mColorPickerSwatches').css({
      'border-right':'1px solid #000'
    });
    
    jQuery('#mColorPickerFooter').css({
      'background-image':"url('" + $o.imageFolder + "grid.gif')",
      'position': 'relative',
      'height':'26px'
    });

    if (jQuery.fn.mColorPicker.init.allowTransparency) jQuery('#mColorPickerFooter').prepend('<span id="mColorPickerTransparent" class="mColor" style="font-size:16px;color:#000;padding-right:30px;padding-top:3px;cursor:pointer;overflow:hidden;float:right;">transparent</span>');
    if (jQuery.fn.mColorPicker.init.showLogo) jQuery('#mColorPickerFooter').prepend('<a href="http://meta100.com/" title="Meta100 - Designing Fun" alt="Meta100 - Designing Fun" style="float:right;" target="_blank"><img src="' +  $o.imageFolder + 'meta100.png" title="Meta100 - Designing Fun" alt="Meta100 - Designing Fun" style="border:0;border-left:1px solid #aaa;right:0;position:absolute;"/></a>');

    jQuery("#mColorPickerBg").click(jQuery.fn.mColorPicker.closePicker);
  
    var swatch = jQuery.fn.mColorPicker.getCookie('swatches'),
        i = 0;

    if (typeof swatch == 'string') swatch = swatch.split('||');
    if (swatch == null || jQuery.fn.mColorPicker.init.enhancedSwatches || swatch.length < 10) swatch = $o.swatches;

    jQuery(".mPastColor").each(function() {

      jQuery(this).css('background-color', swatch[i++].toLowerCase());
    });
  };

  jQuery.fn.mColorPicker.closePicker = function () {

    jQuery(".mColor, .mPastColor, #mColorPickerInput, #mColorPickerWrapper").unbind();
    jQuery("#mColorPickerBg").hide();
    jQuery("#mColorPicker").fadeOut()
  };

  jQuery.fn.mColorPicker.colorShow = function (id) {

    var $e = jQuery("#icp_" + id);
        pos = $e.offset(),
        $i = jQuery("#" + id);
        hex = $i.attr('data-hex') || $i.attr('hex'),
        pickerTop = pos.top + $e.outerHeight(),
        pickerLeft = pos.left,
        $d = jQuery(document),
        $m = jQuery("#mColorPicker");

    if ($i.attr('disabled')) return false;

                // KEEP COLOR PICKER IN VIEWPORT
                if (pickerTop + $m.height() > $d.height()) pickerTop = pos.top - $m.height();
                if (pickerLeft + $m.width() > $d.width()) pickerLeft = pos.left - $m.width() + $e.outerWidth();
  
    $m.css({
      'top':(pickerTop) + "px",
      'left':(pickerLeft) + "px",
      'position':'absolute'
    }).fadeIn("fast");
  
    jQuery("#mColorPickerBg").css({
      'z-index':999990,
      'background':'black',
      'opacity': .01,
      'position':'absolute',
      'top':0,
      'left':0,
      'width': parseInt($d.width(), 10) + 'px',
      'height': parseInt($d.height(), 10) + 'px'
    }).show();
  
    var def = $i.val();
  
    jQuery('#colorPreview span').text(def);
    jQuery('#colorPreview').css('background', def);
    jQuery('#color').val(def);
  
    if (jQuery('#' + id).attr('data-text')) jQuery.fn.mColorPicker.currentColor = $e.css('background-color');
    else jQuery.fn.mColorPicker.currentColor = $i.css('background-color');

    if (hex == 'true') jQuery.fn.mColorPicker.currentColor = jQuery.fn.mColorPicker.RGBtoHex(jQuery.fn.mColorPicker.currentColor);

    jQuery("#mColorPickerInput").val(jQuery.fn.mColorPicker.currentColor);
  
    jQuery('.mColor, .mPastColor').bind('mousemove', function(e) {
  
      var offset = jQuery(this).offset();

      jQuery.fn.mColorPicker.color = jQuery(this).css("background-color");

      if (jQuery(this).hasClass('mPastColor') && hex == 'true') jQuery.fn.mColorPicker.color = jQuery.fn.mColorPicker.RGBtoHex(jQuery.fn.mColorPicker.color);
      else if (jQuery(this).hasClass('mPastColor') && hex != 'true') jQuery.fn.mColorPicker.color = jQuery.fn.mColorPicker.hexToRGB($.fn.mColorPicker.color);
      else if (jQuery(this).attr('id') == 'mColorPickerTransparent') jQuery.fn.mColorPicker.color = 'transparent';
      else if (!jQuery(this).hasClass('mPastColor')) jQuery.fn.mColorPicker.color = jQuery.fn.mColorPicker.whichColor(e.pageX - offset.left, e.pageY - offset.top + ((jQuery(this).attr('id') == 'mColorPickerImgGray')? 128: 0), hex);

      jQuery.fn.mColorPicker.setInputColor(id, jQuery.fn.mColorPicker.color);
    }).click(function() {
  
      jQuery.fn.mColorPicker.colorPicked(id);
    });
  
    jQuery('#mColorPickerInput').bind('keyup', function (e) {
  
      try {
  
        jQuery.fn.mColorPicker.color = jQuery('#mColorPickerInput').val();
        jQuery.fn.mColorPicker.setInputColor(id, jQuery.fn.mColorPicker.color);
    
        if (e.which == 13) $.fn.mColorPicker.colorPicked(id);
      } catch (r) {}

    }).bind('blur', function () {
  
      jQuery.fn.mColorPicker.setInputColor(id, jQuery.fn.mColorPicker.currentColor);
    });
  
    jQuery('#mColorPickerWrapper').bind('mouseleave', function () {
  
      jQuery.fn.mColorPicker.setInputColor(id, jQuery.fn.mColorPicker.currentColor);
    });
  };

  jQuery.fn.mColorPicker.setInputColor = function (id, color) {
  
    var image = (color == 'transparent')? "url('" + $o.imageFolder + "grid.gif')": '',
        textColor = jQuery.fn.mColorPicker.textColor(color);
  
    if (jQuery('#' + id).attr('data-text') || jQuery('#' + id).attr('text')) jQuery("#icp_" + id).css({'background-color': color, 'background-image': image});
    jQuery("#" + id).val(color).css({'background-color': color, 'background-image': image, 'color' : textColor}).trigger('change');
    jQuery("#mColorPickerInput").val(color);
  };

  jQuery.fn.mColorPicker.textColor = function (val) {
  
    if (typeof val == 'undefined' || val == 'transparent') return "black";
    val = jQuery.fn.mColorPicker.RGBtoHex(val);
    return (parseInt(val.substr(1, 2), 16) + parseInt(val.substr(3, 2), 16) + parseInt(val.substr(5, 2), 16) < 400)? 'white': 'black';
  };

  jQuery.fn.mColorPicker.setCookie = function (name, value, days) {
  
    var cookie_string = name + "=" + escape(value),
      expires = new Date();
      expires.setDate(expires.getDate() + days);
    cookie_string += "; expires=" + expires.toGMTString();
   
    document.cookie = cookie_string;
  };

  jQuery.fn.mColorPicker.getCookie = function (name) {
  
    var results = document.cookie.match ( '(^|;) ?' + name + '=([^;]*)(;|$)' );
  
    if (results) return (unescape(results[2]));
    else return null;
  };

  jQuery.fn.mColorPicker.colorPicked = function (id) {
  
    jQuery.fn.mColorPicker.closePicker();
  
    if (jQuery.fn.mColorPicker.init.enhancedSwatches) jQuery.fn.mColorPicker.addToSwatch();
  
    jQuery("#" + id).trigger('colorpicked');
  };

  jQuery.fn.mColorPicker.addToSwatch = function (color) {
  
    var swatch = []
        i = 0;
 
    if (typeof color == 'string') jQuery.fn.mColorPicker.color = color.toLowerCase();
  
    jQuery.fn.mColorPicker.currentValue = jQuery.fn.mColorPicker.currentColor = jQuery.fn.mColorPicker.color;
  
    if (jQuery.fn.mColorPicker.color != 'transparent') swatch[0] = jQuery.fn.mColorPicker.color.toLowerCase();
  
    jQuery('.mPastColor').each(function() {
  
      jQuery.fn.mColorPicker.color = jQuery(this).css('background-color').toLowerCase();

      if (jQuery.fn.mColorPicker.color != swatch[0] && jQuery.fn.mColorPicker.RGBtoHex(jQuery.fn.mColorPicker.color) != swatch[0] && jQuery.fn.mColorPicker.hexToRGB(jQuery.fn.mColorPicker.color) != swatch[0] && swatch.length < 10) swatch[swatch.length] = jQuery.fn.mColorPicker.color;
  
      jQuery(this).css('background-color', swatch[i++])
    });

    if (jQuery.fn.mColorPicker.init.enhancedSwatches) jQuery.fn.mColorPicker.setCookie('swatches', swatch.join('||'), 365);
  };

  jQuery.fn.mColorPicker.whichColor = function (x, y, hex) {
  
    var colorR = colorG = colorB = 255;
    
    if (x < 32) {
  
      colorG = x * 8;
      colorB = 0;
    } else if (x < 64) {
  
      colorR = 256 - (x - 32 ) * 8;
      colorB = 0;
    } else if (x < 96) {
  
      colorR = 0;
      colorB = (x - 64) * 8;
    } else if (x < 128) {
  
      colorR = 0;
      colorG = 256 - (x - 96) * 8;
    } else if (x < 160) {
  
      colorR = (x - 128) * 8;
      colorG = 0;
    } else {
  
      colorG = 0;
      colorB = 256 - (x - 160) * 8;
    }
  
    if (y < 64) {
  
      colorR += (256 - colorR) * (64 - y) / 64;
      colorG += (256 - colorG) * (64 - y) / 64;
      colorB += (256 - colorB) * (64 - y) / 64;
    } else if (y <= 128) {
  
      colorR -= colorR * (y - 64) / 64;
      colorG -= colorG * (y - 64) / 64;
      colorB -= colorB * (y - 64) / 64;
    } else if (y > 128) {
  
      colorR = colorG = colorB = 256 - ( x / 192 * 256 );
    }

    colorR = Math.round(Math.min(colorR, 255));
    colorG = Math.round(Math.min(colorG, 255));
    colorB = Math.round(Math.min(colorB, 255));

    if (hex == 'true') {

      colorR = colorR.toString(16);
      colorG = colorG.toString(16);
      colorB = colorB.toString(16);
      
      if (colorR.length < 2) colorR = 0 + colorR;
      if (colorG.length < 2) colorG = 0 + colorG;
      if (colorB.length < 2) colorB = 0 + colorB;

      return "#" + colorR + colorG + colorB;
    }
    
    return "rgb(" + colorR + ', ' + colorG + ', ' + colorB + ')';
  };

  jQuery.fn.mColorPicker.RGBtoHex = function (color) {

    color = color.toLowerCase();

    if (typeof color == 'undefined') return '';
    if (color.indexOf('#') > -1 && color.length > 6) return color;
    if (color.indexOf('rgb') < 0) return color;

    if (color.indexOf('#') > -1) {

      return '#' + color.substr(1, 1) + color.substr(1, 1) + color.substr(2, 1) + color.substr(2, 1) + color.substr(3, 1) + color.substr(3, 1);
    }

    var hexArray = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f"],
        decToHex = "#",
        code1 = 0;
  
    color = color.replace(/[^0-9,]/g, '').split(",");

    for (var n = 0; n < color.length; n++) {

      code1 = Math.floor(color[n] / 16);
      decToHex += hexArray[code1] + hexArray[color[n] - code1 * 16];
    }
  
    return decToHex;
  };

  jQuery.fn.mColorPicker.hexToRGB = function (color) {

    color = color.toLowerCase();
  
    if (typeof color == 'undefined') return '';
    if (color.indexOf('rgb') > -1) return color;
    if (color.indexOf('#') < 0) return color;

    var c = color.replace('#', '');

    if (c.length < 6) c = c.substr(0, 1) + c.substr(0, 1) + c.substr(1, 1) + c.substr(1, 1) + c.substr(2, 1) + c.substr(2, 1);

    return 'rgb(' + parseInt(c.substr(0, 2), 16) + ', ' + parseInt(c.substr(2, 2), 16) + ', ' + parseInt(c.substr(4, 2), 16) + ')';
  };

  jQuery(document).ready(function () {

    if (jQuery.fn.mColorPicker.init.replace) {

      jQuery('input[data-mcolorpicker!="true"]').filter(function() {
    
        return (jQuery.fn.mColorPicker.init.replace == '[type=color]')? this.getAttribute("type") == 'color': jQuery(this).is(jQuery.fn.mColorPicker.init.replace);
      }).mColorPicker();

      jQuery.fn.mColorPicker.liveEvents();
    }
  });
}