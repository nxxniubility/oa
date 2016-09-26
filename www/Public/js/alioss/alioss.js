function create_oss_sign(key,oss_dir)
{
	  jQuery.post(webGetOssSignUrl,{'oss_dir':oss_dir},function(ret,status){
		  
		  var data=ret.data;
		  if(status='success'){
			  var obj=data;						  
			  obj.host = data['host'];
			  obj.policyBase64 = data['policy'];
			  obj.accessid = data['accessid'];
			  obj.signature = data['signature'];				  
			  obj.callbackbody = data['callback'] ;
			  obj.key=data['dir'];							  
			  window.oss_upload_policy[key]=obj;						
			 
		  }
		  
	  },'json');
	
}
function random_string(len) {
　　len = len || 32;
　　var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';   
　　var maxPos = chars.length;
　　var pwd = '';
　　for (i = 0; i < len; i++) {
    　　pwd += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function get_suffix(filename) {
    var pos = filename.lastIndexOf('.');
    var suffix = '';
    if (pos != -1) {
        suffix = filename.substring(pos);
    }
    return suffix;
}

function calculate_object_name(g_dirname,filename)
{
	var g_object_name = "";   
    var suffix = get_suffix(filename);
    g_object_name = g_dirname + random_string(32) + suffix;   
    return g_object_name ;
	
}

function  init_oss_upload(upload_policy_key,set_config,setIdArr)
{
	var policy=window.oss_upload_policy[upload_policy_key];
	if(!policy)
	{
	   alert('页面加载中，请稍后再试...');
	   return;	
	}
	var default_mime_types=[
							   {title : "Image files", extensions : "jpg,gif,png"},//允许上传的文件类型
						   ];
	var upload_config={
		browse_button : (set_config['browse_button'])?set_config['browse_button']:"",// 此元素要添加一个unique_filename属性，属性值为一个可以唯一标志用户的序列字符串
		click_btn_id:(set_config['click_btn_id'])?set_config['click_btn_id']:"",//如果希望选择文件后就直接上传不需要上传按钮则把这个click_btn_i
		progress_wrap:(set_config['progress_wrap'])?set_config['progress_wrap']:false,
		max_file_size:(set_config['max_file_size'])?set_config['max_file_size']:"3mb",
		mime_types:(set_config['mime_types'])?set_config['mime_types']:default_mime_types,
		file_base_url:(set_config['file_base_url'])?set_config['file_base_url']:"http://didazp.oss-cn-shenzhen.aliyuncs.com",
		img_base_url:(set_config['img_base_url'])?set_config['img_base_url']:"http://didazp.img-cn-shenzhen.aliyuncs.com",
		PostInit:(set_config['PostInit'])?set_config['PostInit']:function(uploader) {
		 },
		FilesAdded:(set_config['FilesAdded'])?set_config['FilesAdded']:function(uploader, files) { 
		},
		UploadProgress:(set_config['UploadProgress'])?set_config['UploadProgress']:function(up, file) {    //选择文件上传进程事件
		},						  
		BeforeUpload: (set_config['BeforeUpload'])?set_config['BeforeUpload']:function(uploader, file) {	
		},
		FileUploaded: (set_config['FileUploaded'])?set_config['FileUploaded']:function(up, file, info) {   //文件上传成功后触发的事件
		},
		Error: (set_config['Error'])?set_config['Error']:function(up, err) {
			 alert("上传发生错误：" + err.code + ": " + err.message);
	    }
	}
	
	var key= upload_config['browse_button'];
	
	if(!window.dy_oss_uploader)window.dy_oss_uploader={};
	if(($('#'+key).length>0)&&(window.dy_oss_uploader.key))
	{
		window.dy_oss_uploader.key.destroy();
	}
	var new_multipart_params = {
		'policy': policy.policy,
		'OSSAccessKeyId': policy.accessid, 
		'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
		'callback' : policy.callback,
		'signature': policy.signature
	};
	if(policy.callback)new_multipart_params['callback']=policy.callback;

	window.dy_oss_uploader.key= new plupload.Uploader({
			  runtimes : 'html5,flash,silverlight,html4',
			  browse_button :upload_config['browse_button'], 
			  flash_swf_url :"/Public/js/alioss/plupload/Moxie.swf",
			  silverlight_xap_url : "/Public/js/alioss/plupload/Moxie.xap",		
			  url: policy.host,
              progress_wrap :upload_config['progress_wrap'],			  
			  multipart_params: new_multipart_params,
			  filters : {
				  max_file_size :upload_config['max_file_size'],
				  mime_types: upload_config['mime_types']
			  },				  
			  init: {
				  PostInit: function(uploader) {							  
							
					  if(upload_config["click_btn_id"]&&(upload_config["click_btn_id"]!= ""))
					  {
						  $("#"+upload_config["click_btn_id"]).click(function() {	
								uploader.start();												  
								return false;
						  });
					  }
					  upload_config.PostInit(uploader);
							  
				  },	
				  FilesAdded: function(uploader, files) {  
					 
					  if(!upload_config["click_btn_id"]||(upload_config["click_btn_id"]== ""))
					  {
						  uploader.start();	
						  uploader.disableBrowse(true);							
					  }					     
					  upload_config.FilesAdded(uploader, files);
				  },
				  UploadProgress:function(up, file) {    //选择文件上传进程事件
				      var progress = up.getOption("progress_wrap");
					
                      if(progress){
                        setProgress(progress,file.percent);
                      }
				      upload_config.UploadProgress(up, file);
				  },						  
				  BeforeUpload: function(uploader, file) {	
				  
					  if(!window.oss_uploader_keys)
					  {
						  window.oss_uploader_keys={};								  
					  }
					  var new_multipart_params = uploader.getOption("multipart_params");							
					  new_multipart_params['key']=calculate_object_name(policy.dir,file.name);
					  window.oss_uploader_keys[file.id]=new_multipart_params['key'];
					  uploader.setOption({								  
						  'multipart_params': new_multipart_params
					  });
					  uploader.disableBrowse(true);
					  upload_config.BeforeUpload(uploader, file);
				  },
				  FileUploaded: function(up, file, info) {   //文件上传成功后触发的事件
				      up.disableBrowse(false);
					  var progress = up.getOption("progress_wrap");
					  if(!window.oss_uploader_keys)
					  {
						  window.oss_uploader_keys={};								  
					  }
					  if(!window.oss_uploader_keys[file.id])
					  {
						 alert('无法获取文件信息，上传失败！');  
						 return false;
					  }
					  file.filename="/"+window.oss_uploader_keys[file.id];
					  window.oss_uploader_keys[file.id]=null;
					  if (info.status == 200)
					  {		
						  file.src=upload_config['file_base_url']+file.filename;
						  for(var i in setIdArr)
						  {
							  if(setIdArr[i]&&setIdArr[i].id)
							  {
								  $('#'+setIdArr[i].id).attr(setIdArr[i].attr,file.src);
							  }
							  // 当图片加载完成后关闭进度框
                              setIdArr[i].attr === 'src' && $('#'+setIdArr[i].id).one('load',function(){
                                    if(progress){
                                        setProgress(progress,101);
                                    }
                              });
						  }
					  }
					  upload_config.FileUploaded(up, file, info);
				  },
				  Error: function(up, err) {    //文件上传失败触发的事件
					  upload_config.Error(up, err);
				  }
			  }
		  });
	  window.dy_oss_uploader.key.init();	 
}


window.oss_upload_policy={};

$(window).ready(function(e) {  
	if(typeof(alioss_client_config) !== "undefined")
    {
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
				  
				  create_oss_sign('oss_upload_btn_'+uploader_config['browse_button'],uploader_config['oss_dir']);
				  
				  var uploader = new plupload.Uploader({
					  runtimes : 'html5,flash,silverlight,html4',
					  browse_button : uploader_config['browse_button'], 
					  container: uploader_config['container'],
					  flash_swf_url :(alioss_client_config.plupload_path?alioss_client_config.plupload_path:"/Public/js/alioss/plupload/")+'Moxie.swf',
					  unique_names:true,
					  silverlight_xap_url : (alioss_client_config.plupload_path?alioss_client_config.plupload_path:"/Public/js/alioss/plupload/")+'Moxie.xap',
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
										
										uploader.start();												  
										return false;
										
								  });
							  }
							  uploader_config.PostInit(uploader);
							  
						  },				  
						  FilesAdded: function(uploader, files) {  						     
							  var uploader_config=uploader.settings.uploader_config;							  
							  if(!uploader_config["click_btn_id"]||(uploader_config["click_btn_id"]== ""))
							  {
								  uploader.start();	
								  uploader.disableBrowse(true);							
							  }					     
							  uploader_config.FilesAdded(uploader,files);
							  
						  },
				          BeforeUpload: function(uploader, file) {	
						      if(!window.oss_uploader_keys)
							  {
								  window.oss_uploader_keys={};								  
							  }
						      uploader.isUploadNow=true;
						      var uploader_config=uploader.settings.uploader_config;
							  var policy=window.oss_upload_policy['oss_upload_btn_'+uploader_config['browse_button']];
							
							  var key=calculate_object_name(policy.key,file.name);
							  window.oss_uploader_keys[file.id]=key;													
							  var new_multipart_params = {	
							      'key' :key,							  
								  'policy': policy.policyBase64,
								  'OSSAccessKeyId': policy.accessid, 
								  'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
								  'callback' : policy.callbackbody,
								  'signature': policy.signature
							  };
							  if(policy.callbackbody)new_multipart_params['callback']=policy.callbackbody;							
							  uploader.setOption({
								  'url': policy.host,
								  'multipart_params': new_multipart_params
							  });
							  uploader.disableBrowse(true);
						  },
						  UploadProgress:uploader_config['UploadProgress'],				  
						  FileUploaded: function(uploader, file, info) { 
							  uploader.disableBrowse(false);
						      var uploader_config=uploader.settings.uploader_config;							     
						      if(!window.oss_uploader_keys)
							  {
								  window.oss_uploader_keys={};								  
							  }
							  if(!window.oss_uploader_keys[file.id])
							  {
								 alert('无法获取文件信息，上传失败！');  
								 return false;
							  }
							  file.filename="/"+window.oss_uploader_keys[file.id];
							  window.oss_uploader_keys[file.id]=null;
						      uploader_config.FileUploaded(uploader, file, info);
						  },
						  Error: function(uploader, err){							 
							  var uploader_config=uploader.settings.uploader_config;							  
							  uploader_config.Error(uploader, err);							  
						  }
					  }
				  });
				  
				  uploader.init();
				  
			}
		}
    }
});

