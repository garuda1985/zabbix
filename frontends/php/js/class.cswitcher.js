/*
** ZABBIX
** Copyright (C) 2000-2009 SIA Zabbix
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
**/

var CSwitcher = Class.create();

CSwitcher.prototype = {
initProc: null,		// on if init method is running
switchers_name : '',
switchers : {},
classOpened : 'filteropened',
classClosed : 'filterclosed',

initialize : function(name){
	this.init = true;
	this.switchers_name = name;

	var element = $(this.switchers_name);

	if(!is_null(element))
		addListener(element, 'click', this.showHide.bindAsEventListener(this));
	
	var divs = $$('div[data-switcherid]');
	for(var i=0; i<divs.length; i++){
		if(!isset(i, divs)) continue;
		addListener(divs[i], 'click', this.showHide.bindAsEventListener(this));
		
		var switcherid = divs[i].getAttribute('data-switcherid');
		this.switchers[switcherid] = {};
		this.switchers[switcherid]['object'] = divs[i];

	}
		
	if((to_change = cookie.readArray(this.switchers_name)) != null){
		for(var i=0; i<to_change.length; i++){
			if(!isset(i, to_change)) continue;

			this.open(to_change[i]);
		}	
	}
	
	this.init = false;
},

open : function(switcherid){
	if(isset(switcherid, this.switchers)){
		$(this.switchers[switcherid]['object']).className = this.classOpened;
		var elements = $$('tr[data-parentid='+switcherid+']');
		for(var i=0; i<elements.length; i++){
			if(!isset(i, elements)) continue;
			elements[i].style.display = '';
		}
		
		this.switchers[switcherid]['state'] = 1;

		if(this.init === false) this.storeCookie();
	}
},

showHide : function(e){
	PageRefresh.restart();
	
	var obj = eventTarget(e);
	var switcherid = obj.getAttribute('data-switcherid');

	if(obj.getAttribute('class') == this.classClosed){
		var state = 1;
		var newClassName = this.classOpened;
		var oldClassName = this.classClosed;
	}
	else{
		var state = 0;
		var newClassName = this.classClosed;
		var oldClassName = this.classOpened;
	}
	obj.className = newClassName;
	
	if(empty(switcherid)){

		var divs = $$('div.'+oldClassName);
		for(var i=0; i < divs.length; i++){
			if(empty(divs[i])) continue;
			divs[i].className = newClassName;
		}
	}
	
	var elements = $$('tr[data-parentid]');

	for(var i=0; i<elements.length; i++){
		if(empty(elements[i])) continue;
		
		if(empty(switcherid) || elements[i].getAttribute('data-parentid') == switcherid){
			if(state){
				elements[i].style.display = '';
			}
			else{
				elements[i].style.display = 'none';
			}
		}
	}
	
	if(empty(switcherid)){
		for(var i in this.switchers){
			this.switchers[i]['state'] = state;
		}
	}
	else{
		this.switchers[switcherid]['state'] = state;
	}
	this.storeCookie();
},

storeCookie : function(){
//	cookie.erase(this.switchers_name);
	
	var storeArray = new Array();
	
	for(var i in this.switchers){
		if(this.switchers[i]['state'] == 1){
			storeArray.push(i);
		}
	}

	cookie.createArray(this.switchers_name, storeArray);
}
}