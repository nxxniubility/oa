/**
 * Created by Administrator on 2016/5/10.
 */


var panel=$("#panel");
var li6=$("#panel .d1>ul:first-child>li:nth-child(6)");
var li7=$("#panel .d1>ul:first-child>li:nth-child(7)");
var li8=$("#panel .d1>ul:first-child>li:nth-child(8)");
var total=$("#panel .d1>ul:nth-child(2)>li:last-child");
var end=18;
var time=document.createElement("i");
var txt=document.createElement("b");
var arr=['00:00','01:00','02:00','03:00','04:00','05:00',
    '06:00','07:00','08:00','09:00','10:00','11:00',
    '12:00', '13:00','14:00','15:00','16:00','17:00',
    '18:00','19:00','20:00','21:00','22:00','23:00','24:00'];

var select=$("#panel .d1>ul:nth-child(2)>li:nth-child(6)");
var select1=$("#panel .d1>ul:nth-child(2)>li:nth-child(7)");
var select2=$("#panel .d1>ul:nth-child(2)>li:nth-child(8)");

var s1=$("#s1");

var vacation=$("#panel .d1>ul:last-child>li:nth-child(5)>select");
var vacation1=$("#panel .d1>ul:last-child>li:nth-child(5)>select:last-child");




/*================定义点击显示弹窗====================*/
    $("#attendance").click(function(e){
        var e=e||window.event;
        var t= e.target|| e.srcElement;

        if (t.nodeName=="DD")
        {
            $(t).css("backgroundColor","#e1e6eb");
            $("#panel").css("display","block");

        }
    });


/*================定义点击关闭弹窗====================*/
    $("#panel p>b").click(function(){
        $("#panel").css("display","none");
    });



/*================定义清除选框===================*/
    function clear(){

    $(li6).html(" ");$(li7).html(" ");$(li8).html(" ");
    $(select).html(" ");$(select1).html(" ");$(select2).html(" ");
    $(vacation).css("width","330px");$(vacation1).css("display","none");

    };




/*================定义弹窗初始选择事件====================*/
    $("#s1").change(function(){

        switch (this.selectedIndex){
            case 0:break;
            case 1:leaveEarly();break;
            case 2:most();break;
            case 3:most();break;
            case 4:most();break;
            case 5:leave();break;
            case 6:forget();break;
        }
    });




/*================定义早退事件===================*/
    function leaveEarly(){

        clear();

        $(li6).css("display","block");
        $(li6).html("<span>*</span>早退时间：");

        var sel=document.createElement("select");
        sel.add(new Option("-请选择时间-"));

        for(var i=0;i<arr.length;i++){
            sel.add(new Option(arr[i]))
        }

        $(select).append(sel);

        $(sel).change(function(){

                $(li7).css("display","block");
                $(li7).html("<span>*</span>总计时间：");
                $(total).css("display","block");

                $(total).append($(time));
                var i=this.selectedIndex;
                console.log(parseInt(arr[i-1]));
                var num=end-(parseFloat(arr[i-1]))>7?7:end-parseFloat(arr[i-1]);
                num=num<0?0:num;
				time.innerHTML=num;
                $(txt).html("小时");
                $(total).append($(txt));
            }
        )
    };



/*================定义出差，旷工，加班事件====================*/
    function most(){
        clear();
        $(li6).css("display","block");
        $(li6).html("<span>*</span>开始时间：");

        var sel=document.createElement("select");
        sel.add(new Option("-请选择时间-"));
        $(select).append(sel);

        for(var i=0;i<arr.length;i++){
            sel.add(new Option(arr[i]))

        }

        $(sel).change(function(){
                $(li7).css("display","block");
                $(li7).html("<span>*</span>结束时间：");
                var sel=document.createElement("select");
                sel.add(new Option("-请选择时间-"));
                $(select1).append(sel);

                for(var i=0;i<arr.length;i++){
                    sel.add(new Option(arr[i]))
                }

                var time1=parseFloat(arr[this.selectedIndex-1]);

                $(sel).change(function(){
                        $(li8).css("display","block");
                        $(li8).html("<span>*</span>总计时间：");

                        $(total).css("display","block");

                        $(total).append(time);
                        var num=parseFloat(arr[this.selectedIndex-1])-time1;
                        if(num<0){
                            num=24-(0-num);
                        }
                        time.innerHTML=num
                        $(txt).html("小时");
                        $(total).append($(txt));
                    }
                )
            }
        )
    };




/*================定义请假事件====================*/
    function leave(){
        clear();

        var top=isIE8()==true?6:10;
        $(".d1>ul+ul .li>select+select").css("top",top);

        $(vacation).css("width","150px");

        $(vacation1).css("display","block");



        $(li6).css("display","block");
        $(li6).html("<span>*</span>开始时间：");

        var sel=document.createElement("select");
        sel.add(new Option("-请选择时间-"));
        $(select).append(sel);

        for(var i=0;i<arr.length;i++){
            sel.add(new Option(arr[i]))
        }

        $(sel).change(function(){
                $(li7).css("display","block");
                $(li7).html("<span>*</span>结束时间：");
                var sel=document.createElement("select");
                sel.add(new Option("-请选择时间-"));
                $(select1).append(sel);

                for(var i=0;i<arr.length;i++){
                    sel.add(new Option(arr[i]))
                }

                var time1=parseFloat(arr[this.selectedIndex-1]);

                $(sel).change(function(){

                        $(li8).css("display","block");
                        $(li8).html("<span>*</span>总计时间：");

                        $(total).css("display","block");

                        $(total).append(time);
                        var num=parseFloat(arr[this.selectedIndex-1])-time1;
                        if(num<0){
                            num=24-(0-num);
                        }
                        time.innerHTML=num
                        $(txt).html("小时");
                        $(total).append($(txt));
                    }
                )
            }
        )

    };



/*================定义忘记打卡事件====================*/
    function forget(){
        clear();
        $(li6).css("display","block");
        $(li6).html("<span>*</span>补卡：");

        $(select).html('<input type="radio" id="up" name="nn"/><label for="up">上班打卡</label> <input type="radio" id="down" name="nn"/><label for="down">下班打卡</label>')
    };


/*================判断IE8====================*/
function isIE8(){
    return navigator.userAgent.split(';')[1].toLowerCase().indexOf("msie 8.0")=="-1"?false:true;
}