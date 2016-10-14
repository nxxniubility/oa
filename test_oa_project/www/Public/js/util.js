var Util = {
        trim: function (str) {
            if (typeof str !== "string") {
                return str;
            }
            if (typeof str.trim === "function") {
                return str.trim();
            } else {
                return str.replace(/^(\u3000|\s|\t|\u00A0)*|(\u3000|\s|\t|\u00A0)*$/g, "");
            }
        },
        isEmpty: function (obj) {
            if (obj === undefined) {
                return true;
            } else if (obj == null) {
                return true;
            } else if (typeof obj === "string") {
                if (this.trim(obj) == "") {
                    return true;
                }
            }
            return false;
        },
        isNotEmpty: function (obj) {
            return !this.isEmpty(obj);
        },
        currentTime: function () {
            return this.formatDate(new Date());
        },
        calcPercent: function (value, total) {
            if (isNaN(value) || Number(value) == 0)return "0";
            if (isNaN(total) || Number(total) == 0)return "0";
            return Math.round(Number(value) * 100 / Number(total));
        },
        round: function (number, fractionDigits) {
            fractionDigits = fractionDigits || 2;
            with (Math) {
                return round(number * pow(10, fractionDigits)) / pow(10, fractionDigits);
            }
        },
        timeDuration: function (second) {
            if (!second || isNaN(second))return;
            second = parseInt(second);
            var time = '';
            var hour = second / 3600 | 0;
            if (hour != 0) {
                time += checkTime(hour) + ':';
            } else {
                time += '00:';
            }
            var min = (second % 3600) / 60 | 0;
            time += checkTime(min) + ':';
            var sec = (second - hour * 3600 - min * 60) | 0;
            time += checkTime(sec);
            return time;
        },
        formatDate: function (date) {
            var h = date.getHours();
            var m = date.getMinutes();
            var s = date.getSeconds();
            return checkTime(h) + ":" + checkTime(m) + ":" + checkTime(s);
        },
        formatTime: function (time) {
            var date = new Date();
            date.setTime(time);
            var h = date.getHours();
            var m = date.getMinutes();
            var s = date.getSeconds();
            return checkTime(h) + ":" + checkTime(m) + ":" + checkTime(s);
        },
        formatText: function (text) {
            text = text.replace(" ", "&nbsp;");
            text = text.replace(/\n/g, "<br/>");
            return text;
        },
        formatUrl: function (content) {
            var reg = /(?:<img.+?>)|(http[s]?|(www\.)){1}[\w\.\/\?=%&@:#;\*\$\[\]\(\){}'"\-]+([0-9a-zA-Z\/#])+?/ig,
                content = content.replace(reg, function (content) {
                    if (/<img.+?/ig.test(content)) {
                        return content;
                    } else {
                        return '<a class="msg-url" target="_blank" href="' + content.replace(/^www\./, function (content) {
                                return "http://" + content;
                            }) + '">' + content + '</a>'
                    }

                });
            return content;
        },
        //占位符替换
        replaceholder: function (str, values) {
            return str.replace(/\{(\d+)\}/g, function (m, i) {
                return values[i];
            });
        },
        pasteHtmlAtCaret: function (html) {
            var sel, range;
            if (window.getSelection) {
                sel = window.getSelection();
                if (sel.getRangeAt && sel.rangeCount) {
                    range = sel.getRangeAt(0);
                    range.deleteContents();
                    var el = document.createElement("div");
                    el.innerHTML = html;
                    var frag = document.createDocumentFragment(), node, lastNode;
                    while ((node = el.firstChild)) {
                        lastNode = frag.appendChild(node);
                    }
                    range.insertNode(frag);
                    // Preserve the selection
                    if (lastNode) {
                        range = range.cloneRange();
                        range.setStartAfter(lastNode);
                        range.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                }
            } else if (document.selection && document.selection.type != "Control") {
                // IE < 9
                document.selection.createRange().pasteHTML(html);
            }
        },
		addToFavorite:function (obj) {
			  var url = window.location.href;
			  var title = $("title").text();
			  var ua = navigator.userAgent.toLowerCase();
			  if (ua.indexOf("360se") > -1) {
				  alert("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！");
			  }
			  else if (ua.indexOf("msie 8") > -1) {
				  window.external.addFavorite(url, title);
				  //window.external.AddToFavoritesBar(url, title); //IE8
			  }
			  else if (document.all) {
				  try {
					  window.external.addFavorite(url, title);
				  } catch (e) {
					  alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
				  }
			  }
			  else if (window.sidebar) {
				  try {
					  window.sidebar.addPanel(title, url, "");
				  } catch (e) {
					  alert("抱歉，您所使用的浏览器无法完成此操作。\n\n加入收藏失败，请进入新网站后使用Ctrl+D进行添加");
				  }
			  }
			  else {
				  alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
			  }
		  }

    };