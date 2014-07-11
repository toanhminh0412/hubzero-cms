/*
 * noVNC: HTML5 VNC client
 * Copyright (C) 2012 Joel Martin
 * Licensed under LGPL-3 (see LICENSE.txt)
 *
 * See README.md for usage and integration instructions.
 */

"use strict";
/*jslint white: false, browser: true */
/*global window, $D, Util, WebUtil, RFB, Display */

var UI = {

rfb_state : 'loaded',
settingsOpen : false,
connSettingsOpen : false,
clipboardOpen: false,
keyboardVisible: false,
clipSetting: false,
clipSettingLocked: false,

// Render default UI and initialize settings menu
load: function(){
		var html = '';
		UI.rfb = new RFB({'target':    $D('theapp'),
							'encrypt':      encrypt,
							'true_color':   true,
							'local_cursor': true,
							'shared':       true,
							'view_only':    false,
							//'updateState':  UI.updateState,
							'onUpdateState': UI.updateState,
							'onClipboard': UI.clipReceive,
							'onPasswordRequired':  function(){ alert('Connection failed: bad password'); }});
							
		UI.updateVisualState();
		
		// Unfocus clipboard when over the VNC area
		//$D('VNC_screen').onmousemove = function () {
		//         var keyboard = UI.rfb.get_keyboard();
		//        if ((! keyboard) || (! keyboard.get_focused())) {
		//            $D('VNC_clipboard_text').blur();
		//         }
		//    };

		// Show mouse selector buttons on touch screen devices
		if ('ontouchstart' in document.documentElement) {
			// Show mobile buttons
			$D('noVNC_mobile_buttons').style.display = "inline";
			UI.setMouseButton();
			// Remove the address bar
			setTimeout(function() { window.scrollTo(0, 1); }, 100);
			UI.clipSetting = true;
			UI.clipSettingLocked = true;
		}
		else {
			UI.clipSetting = false;
		}

		//iOS Safari does not support CSS position:fixed.
		//This detects iOS devices and enables javascript workaround.
		if ((navigator.userAgent.match(/iPhone/i)) ||
			(navigator.userAgent.match(/iPod/i)) ||
			(navigator.userAgent.match(/iPad/i))) {
			//UI.setOnscroll();
			//UI.setResize();
		}

		UI.setViewClip(UI.clipSetting);
		Util.addEvent(window, 'resize', UI.setViewClip);

		Util.addEvent(window, 'beforeunload', function () {
			if (UI.rfb_state === 'normal') {
				return "You are currently connected.";
			}
		} );
		
		//Finally, connect with globals
		UI.rfb.connect(host, port, password, connectPath);
},

// Show the clipboard panel
toggleClipboardPanel: function() {
    //Toggle Clipboard Panel
    if (UI.clipboardOpen === true) {
        $D('noVNC_clipboard').style.display = "none";
        $D('clipboardButton').className = "noVNC_status_button";
        UI.clipboardOpen = false;
    } else {
        $D('noVNC_clipboard').style.display = "block";
        $D('clipboardButton').className = "noVNC_status_button_selected";
        UI.clipboardOpen = true;
    }
},

sendCtrlAltDel: function() {
    UI.rfb.sendCtrlAltDel();
},

setMouseButton: function(num) {
    var b, blist = [0, 1,2,4], button;

    if (typeof num === 'undefined') {
        // Disable mouse buttons
        num = -1;
    }
    if (UI.rfb) {
        UI.rfb.get_mouse().set_touchButton(num);
    }

    for (b = 0; b < blist.length; b++) {
        button = $D('noVNC_mouse_button' + blist[b]);
        if (blist[b] === num) {
            button.style.display = "";
        } else {
            button.style.display = "none";
            /*
            button.style.backgroundColor = "black";
            button.style.color = "lightgray";
            button.style.backgroundColor = "";
            button.style.color = "";
            */
        }
    }
},

updateState: function(rfb, state, oldstate, msg) {
    var s, sb, c, d, cad, vd, klass;
    UI.rfb_state = state;
    s = $D('noVNC_status');
    sb = $D('noVNC_status_bar');
    switch (state) {
        case 'failed':
        case 'fatal':
            klass = "noVNC_status_error";
            break;
        case 'normal':
            klass = "noVNC_status_normal";
			UI.normalStateAchieved();
            break;
        case 'disconnected':
            $D('noVNC_logo').style.display = "block";
            // Fall through
        case 'loaded':
            klass = "noVNC_status_normal";
            break;
        case 'password':
            // UI.toggleConnectPanel();

            // $D('noVNC_connect_button').value = "Send Password";
            // $D('noVNC_connect_button').onclick = UI.setPassword;
            // $D('noVNC_password').focus();

            klass = "noVNC_status_warn";
            break;
        default:
            klass = "noVNC_status_warn";
            break;
    }

    if (typeof(msg) !== 'undefined') {
        s.setAttribute("class", klass);
        sb.setAttribute("class", klass);
        s.innerHTML = msg;
    }

    UI.updateVisualState();
},

normalStateAchieved: function() {
	//Override me
},

// Disable/enable controls depending on connection state
updateVisualState: function() {
    var connected = UI.rfb_state === 'normal' ? true : false;

    //Util.Debug(">> updateVisualState");
    // $D('noVNC_encrypt').disabled = connected;
    // $D('noVNC_true_color').disabled = connected;
    if (UI.rfb && UI.rfb.get_display() &&
        UI.rfb.get_display().get_cursor_uri()) {
        // $D('noVNC_cursor').disabled = connected;
    } else {
        // UI.updateSetting('cursor', false);
        // $D('noVNC_cursor').disabled = true;
    }
    // $D('noVNC_shared').disabled = connected;
    // $D('noVNC_view_only').disabled = connected;
    // $D('noVNC_connectTimeout').disabled = connected;
    // $D('noVNC_path').disabled = connected;

    if (connected) {
        UI.setViewClip(UI.clipSetting);
        UI.setMouseButton(1);
        $D('clipboardButton').style.display = "none"; //HACK: disabled for now
        $D('showKeyboard').style.display = "inline";
        $D('sendCtrlAltDelButton').style.display = "inline";
    } else {
        UI.setMouseButton();
        $D('clipboardButton').style.display = "none";
        $D('showKeyboard').style.display = "none";
        $D('sendCtrlAltDelButton').style.display = "none";
    }
    // State change disables viewport dragging.
    // It is enabled (toggled) by direct click on the button
    UI.setViewDrag(false);

    switch (UI.rfb_state) {
        case 'fatal':
        case 'failed':
        case 'loaded':
        case 'disconnected':
            //$D('connectButton').style.display = "";
            //$D('disconnectButton').style.display = "none";
            break;
        default:
            //$D('connectButton').style.display = "none";
            //$D('disconnectButton').style.display = "";
            break;
    }

    //Util.Debug("<< updateVisualState");
},


clipReceive: function(rfb, text) {
    Util.Debug(">> UI.clipReceive: " + text.substr(0,40) + "...");
    $D('noVNC_clipboard_text').value = text;
    Util.Debug("<< UI.clipReceive");
},

displayBlur: function() {
    UI.rfb.get_keyboard().set_focused(false);
    UI.rfb.get_mouse().set_focused(false);
},

displayFocus: function() {
    UI.rfb.get_keyboard().set_focused(true);
    UI.rfb.get_mouse().set_focused(true);
},

clipClear: function() {
    $D('noVNC_clipboard_text').value = "";
    UI.rfb.clipboardPasteFrom("");
},

clipSend: function() {
    var text = $D('noVNC_clipboard_text').value;
    Util.Debug(">> UI.clipSend: " + text.substr(0,40) + "...");
    UI.rfb.clipboardPasteFrom(text);
    Util.Debug("<< UI.clipSend");
},

//HUBzero addition for resizing
requestResize: function(w, h) {
    Util.Debug(">> UI.requestResize: " + w + ", " + h);
    UI.rfb.requestResize(w, h);
    Util.Debug("<< UI.requestResize");
},

// Enable/disable and configure viewport clipping
setViewClip: function(clip) {
    var display, cur_clip, pos, new_w, new_h;

    if (UI.rfb) {
        display = UI.rfb.get_display();
    } else {
        return;
    }

    cur_clip = display.get_viewport();

    if (typeof(clip) !== 'boolean') {
        // Use default setting
        clip = false;
    }

    if (clip && !cur_clip && !UI.clipSettingLocked) {
        // Turn clipping on
		UI.clipSetting = true;
    } else if (!clip && cur_clip && !UI.clipSettingLocked) {
        // Turn clipping off
		UI.clipSetting = false;
        display.set_viewport(false);
        $D('theapp').style.position = 'static';
        display.viewportChange();
    }
    if (UI.clipSetting) {
        // If clipping, update clipping settings
        $D('theapp').style.position = 'absolute';
        pos = Util.getPosition($D('theapp'));
        new_w = window.innerWidth - pos.x;
        new_h = window.innerHeight - pos.y;
        display.set_viewport(true);
        display.viewportChange(0, 0, new_w, new_h);
    }
},

// Toggle/set/unset the viewport drag/move button
setViewDrag: function(drag) {
    var vmb = $D('noVNC_view_drag_button');
    if (!UI.rfb) { return; }

    if (UI.rfb_state === 'normal' &&
        UI.rfb.get_display().get_viewport()) {
        vmb.style.display = "inline";
    } else {
        vmb.style.display = "none";
    }

    if (typeof(drag) === "undefined") {
        // If not specified, then toggle
        drag = !UI.rfb.get_viewportDrag();
    }
    if (drag) {
        vmb.className = "noVNC_status_button_selected";
        UI.rfb.set_viewportDrag(true);
    } else {
        vmb.className = "noVNC_status_button";
        UI.rfb.set_viewportDrag(false);
    }
},

// On touch devices, show the OS keyboard
showKeyboard: function() {
    if(UI.keyboardVisible === false) {
        $D('keyboardinput').focus();
        UI.keyboardVisible = true;
        $D('showKeyboard').className = "noVNC_status_button_selected";
    } else if(UI.keyboardVisible === true) {
        $D('keyboardinput').blur();
        $D('showKeyboard').className = "noVNC_status_button";
        UI.keyboardVisible = false;
    }
},

keyInputBlur: function() {
    $D('showKeyboard').className = "noVNC_status_button";
    //Weird bug in iOS if you change keyboardVisible
    //here it does not actually occur so next time
    //you click keyboard icon it doesnt work.
    setTimeout(function() { UI.setKeyboard(); },100);
},

setKeyboard: function() {
    UI.keyboardVisible = false;
},

// iOS < Version 5 does not support position fixed. Javascript workaround:
setOnscroll: function() {
    window.onscroll = function() {
        UI.setBarPosition();
    };
},

setResize: function () {
    window.onResize = function() {
        UI.setBarPosition();
    };
},

//Helper to add options to dropdown.
addOption: function(selectbox,text,value )
{
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;
    selectbox.options.add(optn);
},

setBarPosition: function() {
    $D('noVNC-control-bar').style.top = (window.pageYOffset) + 'px';
    $D('noVNC_mobile_buttons').style.left = (window.pageXOffset) + 'px';

    var vncwidth = $D('noVNC_screen').style.offsetWidth;
    $D('noVNC-control-bar').style.width = vncwidth + 'px';
}

};
