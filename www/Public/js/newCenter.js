selectbox();
selectbox2();
//selectCheckbox();
//下拉框
function selectbox() {
    $(document).bind({
        click: function() {
            $(".selectbox dt").parent().find("ul").removeClass("s");
        }
    });
    $(".select").delegate("dt", "click",
        function() {
            $(this).parent().find("dd").toggle();
            $(this).parent().find(".ddoption").toggle();
            selectStatus($(this));
            if ($(this).attr("class") == "on") {
                if ($(this).parent().find("ul").height() > 200) {
                    $(this).parent().find("ul").addClass("s");
                };
            } else {
                $(this).parent().find("ul").removeClass("s");
            }
            return false;
        });

    $(".select").delegate("dd", "click",
        function() {
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_name = $(this).text();
            $(this).parent('.ddoption').toggle();
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent().parent().find(".select_title").text(data_name);
            $(this).parents("dl").next().val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select dd").parent("dl").find("dt"));
            //return false;
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).click(function() {
        $(".select dd").hide();
        selectStatus($(".select dt"));
    });

}

function selectbox2() {
    $(document).bind({
        click: function() {
            $(".selectbox2 dt").parent().find("ul").removeClass("s");

            $(".selectbox2 dt").find(".select_title2").hide();

        }
    });
    $(".select2").delegate("dt", "click",
        function() {
            if ($(this).is(".caption")) return false;
            $(this).parent().find("dd").toggle();
            $(this).parent().find(".ddoption").toggle();
            $(this).parent().find(".select_title2").toggle();
            selectStatus($(this));
            if ($(this).attr("class") == "on") {
                if ($(this).parent().find("ul").height() > 200) {
                    $(this).parent().find("ul").addClass("s");
                };
            } else {
                $(this).parent().find("ul").removeClass("s");
            }
            return false;
        });

    $(".select2").delegate("dd", "click",
        function() {
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_name = $(this).text();
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent("dl").find(".select_title2").toggle();
            $(this).parent().parent().find(".select_title").text(data_name);
            $(this).parents("dl").next().val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select2 dd").parent("dl").find("dt"));
            //return false;
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).click(function() {
        $(".select2 dd").hide();
        selectStatus($(".select2 dt"));
    });
}

/*function selectCheckbox() {
    $(document).bind({
        click: function() {
            $(".selectCheckbox dt").parent().find("ul").removeClass("s");
        }
    });
    var selecttitle;
    var firstopen = 0;
    $(".selectcheck").delegate("dt", "click",
        function() {
            if (firstopen == 0) {
                selecttitle = $(this).find(".select_title").text();
                firstopen = 1;
            }
            var dds = $(this).parent().find("dd")
            dds.toggle();
            selectStatus($(this));
            var val = $(this).parent().next().val();
            if (!val == "") {
                var valArr = val.split(",");
                for (var i in valArr) {
                    dds.each(function() {
                        if (valArr[i] == $(this).data("value") && !$(this).hasClass("on")) {
                            $(this).addClass("on")
                        }
                    });
                }
            }
            return false;
        });
    $(".selectcheck").delegate("dd", "click",
        function() {
            if ($(this).hasClass("on")) {
                $(this).removeClass("on");
            } else {
                $(this).addClass("on");
            }
            var select_title = $(this).parent("dl").find(".select_title"),
                hideInput = $(this).parents("dl").next();
            hideInput.val(""), select_title.text(""), $(select_title).change(), select_title.attr("data-null", "0");
            //所有选择项
            var newText = "";
            var newVal = "";
            $(this).parent("dl").find("dd.on").each(function() {
                select_title.attr("data-null", "1"); //有选择项
                var data_value = $(this).attr('data-value');
                var data_name = $(this).text();
                if (newText === "") {
                    newText = data_name;
                    newVal = data_value;
                } else {
                    var oldText = select_title.text();
                    var oldVal = hideInput.val();
                    newText = oldText + "," + data_name;
                    newVal = oldVal + "," + data_value;

                }
                hideInput.val(newVal),
                    select_title.text(newText),
                    $(select_title).change();
            });
            if (newText === "") {
                select_title.text(selecttitle), $(select_title).change();
            }
            return false;
        });
    $(document).click(function() {
        $(".selectcheck dd").hide();
        selectStatus($(".selectcheck dt"));
    });

}*/
//当前下拉菜单状态
function selectStatus(obj) {
    if (obj.parent().find("dd").is(":hidden")) {
        otherSelectStatus(); //
        obj.parent().removeClass("zindex4").parent().find(".on").removeClass("on");
        obj.find(".arrow").removeClass("arrow_on");
    } else {
        otherSelectStatus(); //
        obj.parent().find("dd,.select_title2").show();
        obj.addClass("on");
        obj.parent().addClass("on zindex4");
        obj.find(".arrow").addClass("arrow_on");

    }
}
//其他下拉菜单状态
function otherSelectStatus() {
    $("[class^=select]").parent().find(".on").removeClass("on");
    $("[class^=select]").find(".arrow").removeClass("arrow_on");
    $("[class^=select]").find("dd,.select_title2").hide();
    $("[class^=select]").find("dl").removeClass("zindex4");
}