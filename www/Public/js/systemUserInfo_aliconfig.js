//阿里oss客户端上传配置 演示

var alioss_client_config={

	default_oss_file_dir:'user_dir/', //默认文件上传目录
	plupload_path:'/Public/js/alioss/plupload/',
	plupload_unit:[
		/***********这里设置每个上传单元的配置，一个页面可以有多个上传单元**********/
	    /************上传单元1配置 begin************/
		{
			browse_button : "uploadBtn",// 此元素要添加一个unique_filename属性，属性值为一个可以唯一标志用户的序列字符串
			click_btn_id:"",//如果希望选择文件后就直接上传不需要上传按钮则把这个click_btn_id设置为""
			oss_callback:0,//是否回调
			//container: document.getElementById('container1'),//默认body
			custom_oss_file_dir:'user_image/photo/',  //自定义文件上传目录
			max_file_size : '3mb',//允许上传最大文件的大小
			mime_types: [
				{title : "Image files", extensions : "jpg,gif,png"},//允许上传的文件类型
			],
			PostInit:function(uploader){   //必须定义
			   
			},
			FilesAdded:function(up, files) {    //必须定义
			   			
			},
			UploadProgress:function(up, file) {    //选择文件上传进程事件
				layer.load(2);
				$('#uploadBtn').val("已上传"+file.percent + "%");
			},
			FileUploaded: function(up, file, info) {   //文件上传成功后触发的事件
				$('#uploadBtn').val("修改头像");
				if (info.status == 200)
				{

					img_src=up.settings.img_url+file.filename;
					//$('#faceImg').attr("src",img_src);
					//$('input.faceImg').val(img_src);	
					saveface(img_src);		
				}
			},
			Error: function(up, err) {    //文件上传失败触发的事件
			    
				$('#upload_course_img').val("上传失败，重新上传");
				alert("上传发生错误：" + err.code + ": " + err.message);
		    }
		} 
	]
}