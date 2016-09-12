if(!alioss_client_config)
{
    alert('加载OSS配置失败！');	
}else{
    
	$(window).ready(function(e) {       

		window.alioss = {
			accessid : '',
			url:'',
			host:'',
			policyBase64:'',
			signature:'',
			callbackbody:'',
			expire:0,
			key:'',
			url:'',
			uploaders:{},
			init:function() {
				this.expire = Date.parse(new Date()) / 1000+60; 			
				return  true;
			},
			 get_signature:function(uploader_config)
			 {						  
				  if (jQuery) { 
					  jQuery.post(this.url,{'oss_dir':uploader_config['oss_dir'],'is_callback':uploader_config['oss_callback']},function(data,status,xhr){
						  //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下30分钟 做为缓冲
						  now = timestamp = Date.parse(new Date()) / 1000; 
						  if(status='success'){
							  var obj=data;						  
							  obj.host = data['host'];
							  obj.policyBase64 = data['policy'];
							  obj.accessid = data['accessid'];
							  obj.signature = data['signature'];				  
							  obj.callbackbody = data['callback'] ;
							  obj.key=data['dir'];
							  if(uploader_config["click_btn_id"]&&(uploader_config["click_btn_id"]!= ""))
						      {
							      alioss_client_config.oss_policy[uploader_config['click_btn_id']]=obj;
							  }else{
								  alioss_client_config.oss_policy[uploader_config['browse_button']]=obj;
							  }
							 
						  }else{
							
							 return false;
						  }	  
						  
					  },'json');
				  } else { 
					  alert("缺失必须的库文件！");
					  return false;			 
				  } 
			  }
		};
		
		alioss.init();
		alioss.url=alioss_client_config.oss_sign_url;
	
		for(i  in  alioss_client_config.plupload_unit )
		{
			
			var uploader_config=alioss_client_config.plupload_unit[i];
			var select_element=uploader_config["browse_button"];
			
			if(jQuery("#"+select_element).length>0)
			{
				
				  var  dir_tmp='';
				  if(uploader_config["custom_oss_file_dir"]&&(uploader_config["custom_oss_file_dir"]!=alioss_client_config["default_oss_file_dir"]))
				  {
					  dir_tmp=uploader_config["custom_oss_file_dir"];				
				  }else{
					  dir_tmp=alioss_client_config["default_oss_file_dir"];
				  }
				  
				  uploader_config['oss_dir']=dir_tmp;
				  
				  if(typeof(uploader_config['oss_callback']) == "undefined")uploader_config['oss_callback']=0;
				  
				  alioss.get_signature(uploader_config);
				  
				  var uploader = new plupload.Uploader({
					  runtimes : 'html5,flash,silverlight,html4',
					  browse_button : uploader_config['browse_button'], 
					  container: uploader_config['container'],
					  flash_swf_url : alioss_client_config.plupload_path+'Moxie.swf',
					  silverlight_xap_url : alioss_client_config.plupload_path+'Moxie.xap',
					  url :  "http://oazelin.oss-cn-shenzhen.aliyuncs.com",
					  img_url:"http://oazelin.img-cn-shenzhen.aliyuncs.com",
					  uploader_config:uploader_config,
					  filters : {
						  max_file_size : uploader_config["max_file_size"],
						  mime_types:  uploader_config["mime_types"]
					  },
				  
					  init: {
						  PostInit: function(uploader) {
							  var uploader_config=uploader.settings.uploader_config;
							  if(uploader_config["click_btn_id"]&&(uploader_config["click_btn_id"]!= ""))
							  {
								  $("#"+uploader_config["click_btn_id"]).click(function() {	
										var unique_filename=jQuery("#"+uploader_config['browse_button']).attr("unique_filename");
										(!unique_filename)&&(unique_filename='');
									  
										now = timestamp = Date.parse(new Date()) / 1000; 
										var  policy=alioss_client_config.oss_policy[uploader_config['click_btn_id']];
										if(policy&&(policy.expire>now))
										{						
											new_multipart_params = {
												'key' : policy.key + unique_filename+'${filename}',
												'policy': policy.policyBase64,
												'OSSAccessKeyId': policy.accessid, 
												'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
												'callback' : policy.callbackbody,
												'signature': policy.signature
											};
											if(policy.callbackbody)new_multipart_params['callback']=policy.callbackbody;
											var up=uploader;
											uploader.setOption({
												'url': policy.host,
												'multipart_params': new_multipart_params
											});
											uploader.start();
										}else{
											alert('请求超时，请3秒后再试...');
											alioss.get_signature(uploader_config);									 
										}							  
										return false;
								  });
							  }else{
								  
							  }
							  uploader_config.PostInit(uploader);
							  
						  },
				  
						  FilesAdded: function(uploader, files) {  
							  var uploader_config=uploader.settings.uploader_config;
							  
							  if(!uploader_config["click_btn_id"]||(uploader_config["click_btn_id"]== ""))
							  {
								   
								  var unique_filename=jQuery("#"+uploader_config['browse_button']).attr("unique_filename");
								  (!unique_filename)&&(unique_filename='');
								  now = timestamp = Date.parse(new Date()) / 1000; 
								  var  policy=alioss_client_config.oss_policy[uploader_config['browse_button']];
								  if(policy&&(policy.expire>now))
								  {						
									  new_multipart_params = {
										  'key' : policy.key + unique_filename+'${filename}',
										  'policy': policy.policyBase64,
										  'OSSAccessKeyId': policy.accessid, 
										  'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
										  'callback' : policy.callbackbody,
										  'signature': policy.signature
									  };
									  if(policy.callbackbody)new_multipart_params['callback']=policy.callbackbody;
									  var up=uploader;
									  uploader.setOption({
										  'url': policy.host,
										  'multipart_params': new_multipart_params
									  });
									  uploader.start();
								  }else{
									  alert('请求超时，请3秒后再试...');
									  alioss.get_signature(uploader_config);
									 
								  }							  
								 
								  
							  }					     
							  uploader_config.FilesAdded(uploader,files);
						  },
				  
						  UploadProgress:uploader_config['UploadProgress'],
				  
						  FileUploaded: uploader_config['FileUploaded'],
				  
						  Error: uploader_config['Error']
					  }
				  });
				  
				  uploader.init();
			}
		}
	});
}
