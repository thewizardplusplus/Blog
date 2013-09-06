var yiiFileManagerFilePickerWidget = function(widget, mode){
	var opts = widget.data('options');
	var me = $(widget).find(opts.body_selector);
	var _upload_callback = null;
	var _current_xhr;

	var _send = function(data, callback){
		jQuery.ajax({
			url: opts.ajax_handler, type: 'post', data: data, async: true,
			success: function(resp){
				callback(true, resp);
			},error: function(e){
				callback(false, e.responseText);
			}
		});
	}
	var _sendasync = function(data){
		var _ok=null;
		var callback = function(ok, resp){
			if(ok==true)
				_ok = resp;
		}
		jQuery.ajax({
			url: opts.ajax_handler, type: 'post', data: data, async: false,
			success: function(resp){
				callback(true, resp);
			},error: function(e){
				callback(false, e.responseText);
			}
		});
		return _ok;
	}
	var _get_selected_items = function(){
		var list = new Array;
		var i=0;
		me.find("li.yiifileman-file-selected").each(function(){
			list[i++] = $(this).data('file_id');
		});
		if(i==0)
			return null;
		return list;
	}

	var _find_item = function(file_id){
		var found=null;
		me.find("li").each(function(){
			var li = $(this);
			if(li.data('file_id')==file_id)
				found=li;
		});
		return found;
	}

	var _get_extra_info = function(file_ids){
		//for a given file_id array this method return
		//an extended array having the URL for viewing this file
		// [{ file_id: bla, url: bla2 },...{..}]
		var list = new Array;
		var n=0;
		$.each(file_ids, function(i, file_id){
			var li = _find_item(file_id);
			if(li != null){
				list[n++] = { file_id: file_id, url: li.find('img').attr('src') };
			}else{
				// paranoid ?, maybe..
			}
		});
		return list;
	}

	var _cancel_method = function(w){
		if(opts.dialog_mode==true)
			w.hide('fast');
	}

	var _do_method = function(w, action, file_ids){
		var opts = widget.data('options');
		if((opts.allow_delete_files != true) && (action=='delete'))
			return;
		if(file_ids == null){
			if(opts.no_selection_message != "")
				alert(opts.no_selection_message);
			return;
		}else{
			var message = (action=='select') ? opts.select_confirm_message :
				opts.delete_confirm_message;
			if(message != '')
				if(!confirm(message))
					return;
		}
		var me = $(w).find(opts.body_selector);
		var info = _get_extra_info(file_ids);
		if(opts.onBeforeAction(w,action, info)){
			// on success this call will launch the yiifileman_on_action() event
			_send({ action: action , file_ids: file_ids }, function(ok,resp){
				opts.onAfterAction(w,action, info, ok, resp);
				if(ok){
					if(action=='select'){
						_cancel_method(w); // ok hide the dialog
					}else
					if(action=='delete'){
						// remove the selected files from the view
						$.each(file_ids,function(i, file_id){
							var li = _find_item(file_id);				
							if(li) li.remove();
						});
					}
				}
			});	
		}
	}

	var _query_file_attr = function(file, attr){
		var v='';
		try{
			if(attr=='name') v = file.name;
			if(attr=='type') v = file.type;
			if(attr=='size') v = file.size;
		}catch(e){
			//it always throws an exception..dont know why.
		}
		return v;
	}

	var _check_upload = function(name, size, mime){
		if(name != ""){
			//fires: yiifileman_on_pre_uploaded_file($post);
			// return array: result (bool), reason (string)
			var _data = _sendasync({ canupload: "canupload", 
				filename: name, filesize: size, filemimetype: mime });
			if(_data.result == true)
				return null;
			return name+": "+_data.reason;
		}
		else
			return null;
	}

	var _get_form_data = function(formid){
		var domform = document.getElementById(formid);
		var formdata = new FormData(domform);
		var nup=0;
		var errors=new Array;
		var nerrors=0;
		var reason="";
		for(nup=0; nup < opts.file_uploaders_count;nup++){
			var _id = formid+'_'+nup;
			var _name = _id;
			var fileInput = document.getElementById(_id);
			var _file = fileInput.files[0];
			var file_name = _query_file_attr(_file,"name");
			var file_size = _query_file_attr(_file,"size");
			var file_type = _query_file_attr(_file,"type");
			if((reason=_check_upload(file_name, file_size, file_type)) != null){
				errors[nerrors] = reason;
				nerrors++;
			}
			formdata.append(_name,_file);
		}
		if(nerrors > 0){
			opts.onClientSideUploaderError(errors);
			return null;
		}
		else
			return formdata;
	};//submit_function

	var _clear_the_file_uploader_form = function(){
		var up = $(widget).find(opts.file_uploader_selector);



	}

	// progress
	var _monitor_pro = function(oEvent) {
	  if (oEvent.lengthComputable) {
		var percentComplete = oEvent.loaded / oEvent.total;
		_upload_callback('pro', (percentComplete * 100));
	  } else {
	  }
	}

	// completed
	var _monitor_tco = function(){
		_upload_callback('tco');	
		_upload_callback('pro',100);	
	}
	
	// failed
	var _monitor_tfa = function(){
		_upload_callback('tfa');	
	}

	//cancelled
	var _monitor_tca = function(){
		_upload_callback('tca');	
	}

	var _the_ajax_thing = function(action, postdata, callback){
		var xhr = new XMLHttpRequest();
		_current_xhr = xhr;
		_upload_callback = callback;
		// monitor events:
		//
		xhr.upload.addEventListener("progress",_monitor_pro, false);
		xhr.addEventListener("load", _monitor_tco, false);
		xhr.addEventListener("error", _monitor_tfa, false);
		xhr.addEventListener("abort", _monitor_tca, false);
		//
		xhr.open('POST',action,("async"=="async"));           	
		xhr.send(postdata);                                                	
	}

	var _build_uploader_form = function(uploader_selector){
		// basic layout:
		//
		//	[uploader_selector] { 
		//		[label]
		//		[.files]
		//		[.progressbar] {
		//			[.progress]
		//			[.canceljob]
		//		} 
		//	}
		//
		var id = 'yiifileman-uploader-form-id';
		var progressbar = uploader_selector.find('.progressbar');
		progressbar.hide();
		uploader_selector.find('.files').html(
		"<form enctype='multipart/form-data'>"
			+"<div class='uploaders'></div>"
			+"<input type='button' name='submit' value='"
				+opts.upload_file_button_text+"'/>"
		+"</form>"
		);
		var form = uploader_selector.find("form");
		//
		form.attr('id',id);
		form.attr('action',opts.ajax_file_uploader_handler);
		// insert N file uploaders, depending on main opts.file_uploaders_count
		var nup=0;
		for(nup=0; nup < opts.file_uploaders_count;nup++)
			form.find('.uploaders').append("<input type='file' />");
		form.find('input[type=file]').each(function(i,k){ 
			$(this).attr('id',id+'_'+i); 
			$(this).attr('name',id+'_'+i);
		});
		form.find('input[name=submit]').click(function(){
			if(form.data('busy')==true)
				return;
			form.data('busy',true);
			// submit the uploader form
			var formdata = _get_form_data(id);
			if(formdata != null){
				// ok, send the data via ajax
				_the_ajax_thing(form.attr('action'),formdata,function(eventname, progress){
					if(eventname != 'pro'){
						// upload has finished
						// tco (completed), tfa (failed), tca (cancelled)
						setTimeout(function(){ progressbar.hide(); },2000);
						yiiFileManagerFilePickerWidget(widget, 'refresh');
						form.data('busy',false);
					}else{
						// show progress
						progressbar.show();
						progressbar.find('.progress').html(
							Math.round(progress)+"%");
					}
					opts.onClientUploaderProgress(eventname, progress);
				});
				//clear form
				form.find('input[type=file]').each(
					function(){ $(this).val(''); });
			}
		});
		//cancel job
		uploader_selector.find('.canceljob').click(function(){ 
			try{ _current_xhr.abort(); }catch(e){}});
	}

	if(mode=='init'){
		me.addClass("yiifileman-viewer");
		if(opts.dialog_mode == false){
			mode='update'; // to perform update at startup..
			$(widget).find(opts.cancel_button_selector).hide();
		}else{
			
		}
		$.fn.yiiFilemanDialog_select = function(){
			_do_method(widget, "select", _get_selected_items());
		}
		$.fn.yiiFilemanDialog_delete = function(){
			_do_method(widget, "delete", _get_selected_items());
		}
		$.fn.yiiFilemanDialog_cancel = function(){
			_cancel_method(widget);
		}
		$(widget).find(opts.ok_button_selector).click(function(){
			_do_method(widget, "select", _get_selected_items());
		});
		$(widget).find(opts.delete_button_selector).click(function(){
			_do_method(widget, "delete", _get_selected_items());
		});
		$(widget).find(opts.cancel_button_selector).click(function(){
			_cancel_method(widget);
		});
		if(opts.allow_delete_files != true)
			$(widget).find(opts.delete_button_selector).remove();
		// build the file uploader
		var uploader = $(widget).find(opts.uploader_selector);
		var uploader_label = uploader.find('label');
		//
		uploader.addClass('yiifileman-uploader');
		if(opts.allow_file_uploads == false){
			uploader.hide();
		}else{
			_build_uploader_form(uploader);
			var label = uploader.find('label');
			var is_pin = (label.attr('rel')=='pin') ? true : false;
			if(is_pin == true){
				uploader.find('.files').hide();
				label.click(function(){
					uploader.find('.files').toggle();
				});
			}else{
				label.click(function(){
					uploader.find('.files').toggle();
				});                                  			
			}
		}
		//end file uploader
	}//init

	if((mode=='update') || (mode == 'refresh')){
		// slightly different modes:
		//	update: full refresh. redraw display.
		//	refresh: add/remove items depending on data received from server
		var _sanitize_input_string = function(s) {
			return jQuery.trim(s.replace(/([^a-z0-9 .,-_])+/gi, " ")
			.replace(/[\/\\\[\]\?\¿\:]/g," ")
			.replace(/\s+/g," ")
			 );
		}
		var _clear_rename = function(rename_cmd){
			var li = rename_cmd.data('li');
			var filedata = li.data('file_data');
			var label = li.find(".fileinfo label");
			label.html(filedata.filename);
			label.attr('for','fileid_'+filedata.file_id);
			rename_cmd.data('busy',false);
		}
		var _do_rename = function(rename_cmd){
			var li = rename_cmd.data('li');
			var filedata = li.data('file_data');
			var input = li.find(".fileinfo input[type=text]");
			var value = _sanitize_input_string(jQuery.trim(input.val()));
			if(value == filedata['filename'])
				return true;
			if(value != ''){
				filedata.filename = value;// for temporary use it will be refreshed after ajax update
				return (null != _sendasync({ rename: true, file_id: filedata.file_id, name: value }));
			}else
			return false;
		}

		me.show();
		if(mode == 'update') {
			me.html("<div class='loading'></div>");
			me.append("<ul></ul>");
		}
		var list = me.find("ul");
		_send({ giveme: 'list_files' },function(ok, resp){
			if(ok==true)
			$.each(resp, function(file_id, file_data){
				var can_insert_new = false;
				if(mode == 'refresh'){
					// only add if it is not a currently existing item
					if(!_find_item(file_id))
						can_insert_new = true;
				}else
				can_insert_new=true;
			if(can_insert_new == true){
				// file_data format, see also: 
				// YiiFileManagaerFilePicker::ajax_list_files	
				list.append("<li></li>");
				var li = list.find("li:last");
				li.data('file_id',file_id);
				li.data('file_data',file_data);
				li.html("<label class='img'><img /></label>");
				var img = li.find("img");
				img.attr('data',file_id);
				img.attr('src',file_data.url);
				li.append("<div class='fileinfo'><input type='checkbox' />"
					+"<div class='rename' title='click to rename'></div><label ></label></div>");
				li.find(".fileinfo label").html(file_data.filename);
				// rename
				var rename = li.find(".rename");
				rename.data('li',li);
				if(opts.allow_rename_files != true){
					rename.remove();
				}else{
					rename.click(function(){
						if($(this).data('busy')==true){
							// similar to press <enter> over the input box
							if(_do_rename($(this)))
								_clear_rename($(this));
						}else{
						//renaming..
						$(this).data('busy',true);
						var _li = $(this).data('li');
						var label = _li.find('.fileinfo label');
						label.attr('for','');
						var file_data = _li.data('file_data');
						label.html("");
						label.html("<input type='text' />");
						var input = label.find("input[type=text]");
						input.val(file_data.filename);
						input.data('_parent',$(this));
						input.keyup(function(e){
								var _parent = input.data('_parent');
								if(e.which == 13)
									if(_do_rename(_parent))
										_clear_rename(_parent);
							});
						}
					});		
				}	
				// link the labels to the checkbox
				var checkbox_id = "fileid_"+file_id;
				var checkbox = li.find("input[type=checkbox]");
				checkbox.attr('id',checkbox_id);
				checkbox.data('li',li);
				li.find(".fileinfo label").attr('for',checkbox_id);
				li.find("label.img").attr('for',checkbox_id);
				// click on a checkbox. now, when clicking over any label it will fire
				// the checkbox click event automatically (due to the "for" attribute linkage)
				checkbox.click(function(){
					var chkbox = $(this);
					var _li = chkbox.data('li');
					var chk = chkbox.attr('checked')=='checked';
					if((chk==true) && (opts.allow_multiple_selection==false)){
						// unckeck the others
						me.find("li").each(function(){
							var __li = $(this);
							if(__li.data('file_id') != _li.data('file_id')){
								__li.find('input[type=checkbox]').attr('checked',null);
								__li.removeClass("yiifileman-file-selected");
							}else{
								_li.removeClass("yiifileman-file-selected");
								_li.addClass("yiifileman-file-selected");
							}
						});
					}else 
					if((chk==true) && (opts.allow_multiple_selection==true)){
						_li.removeClass("yiifileman-file-selected");
						_li.addClass("yiifileman-file-selected");
					}else
					if((chk==false) && (opts.allow_multiple_selection==false)){
						_li.removeClass("yiifileman-file-selected");
					}else
					if((chk==false) && (opts.allow_multiple_selection==true)){
						_li.removeClass("yiifileman-file-selected");
					}
				});//end click for checkbox
			}// if can_insert_new
			});// main update/refresh loop
			me.find(".loading").remove();
		});
		widget.show();
	}// show
}

$.fn.yiiFileManagerFilePickerViewer_init = function(options) {
	$(this).each(function(){
		var widget = $(this);
		widget.data('options',options);
		yiiFileManagerFilePickerWidget(widget,'init');
	});
}

$.fn.yiiFileManagerFilePickerViewer_update = function() {
	$(this).each(function(){
		var widget = $(this);
		yiiFileManagerFilePickerWidget(widget,'update');
	});
}
