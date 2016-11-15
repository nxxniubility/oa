//  我的今日任务， 我的统计数据 字数限制
function limitWords(){
	$(document).ready(function(){
		//限制字符个数
		$('.mission-today').each(function(){
			var maxNum = 40;
			if($(this).text().length > maxNum){
				$(this).text($(this).text().substring(0,maxNum));
				$(this).html($(this).html() + '…');
			}
		});
		$('.data').each(function(){
			var maxNum = 30;
			if($(this).text().length > maxNum){
				$(this).text($(this).text().substring(0,maxNum));
				$(this).html($(this).html() + '…');
			}
		});
	});
}
limitWords();