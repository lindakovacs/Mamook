(function(b){function x(b,c){var b=b.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]"),g=RegExp("[\\?&]"+b+"=([^&#]*)").exec(c);return null==g?"":g[1]}function W(){var b=location.href,c=b.indexOf("#"+l)+1;return c?decodeURI(b.substring(c,b.length)):!1}function X(){if(self.pageYOffset)return{scrollTop:self.pageYOffset,scrollLeft:self.pageXOffset};if(j.documentElement&&j.documentElement.scrollTop)return{scrollTop:j.documentElement.scrollTop,scrollLeft:j.documentElement.scrollLeft};if(j.body)return{scrollTop:j.body.scrollTop,
scrollLeft:j.body.scrollLeft}}var h=b.fwPopup={initialized:!1,version:"1.0.0"},l,j=document,t=b(window),f,P,Q,c,R,Y,y,z,g,D,A,B=t.height(),m=t.width(),G=null;b.fn.fwPopup=function(w){var N,O;function Z(){c.social_tools?(facebook_like_link=c.social_tools.replace("{location_href}",encodeURIComponent(location.href)),c.markup=c.markup.replace("{pp_social}",c.social_tools)):c.markup=c.markup.replace("{pp_social}","");b("body").append(c.markup);g=b(".pp_pic_holder");D=b(".ppt");A=b("div.pp_overlay");if(q&&
c.overlay_gallery){k="";for(var a=0;a<e.length;a++){var d="",f=e[a];e[a].match(/\b(jpg|jpeg|png|gif)\b/gi)||(d="default",f="");k+='<li class="'+d+'"><a href="#"><img src="'+f+'" width="50" alt=""/></a></li>'}k=c.gallery_markup.replace(/{gallery}/g,k);g.find("#pp_full_res").after(k);$pp_gallery=b(".pp_pic_holder .pp_gallery");$pp_gallery_li=$pp_gallery.find("li");$pp_gallery.find(".pp_arrow_next").click(function(){h.changeGalleryPage("next");h.stopSlideshow();return!1});$pp_gallery.find(".pp_arrow_previous").click(function(){h.changeGalleryPage("previous");
h.stopSlideshow();return!1});g.find(".pp_content").hover(function(){g.find(".pp_gallery:not(.disabled)").fadeIn()},function(){g.find(".pp_gallery:not(.disabled)").fadeOut()});$pp_gallery_li.each(function(a){b(this).find("a").click(function(){h.changePage(a);h.stopSlideshow();return!1})})}c.slideshow&&(g.find(".pp_nav").prepend('<a href="#" class="pp_play">Play</a>'),g.find(".pp_nav .pp_play").click(function(){h.startSlideshow();return!1}));g.attr("class","pp_pic_holder "+c.theme);A.css({opacity:0,
height:b(j).height(),width:t.width()}).on("click",function(){c.modal||h.close()});b("a.pp_close").on("click",function(){h.close();return!1});if(c.allow_expand)b("a.pp_expand").on("click",function(){b(this).hasClass("pp_expand")?(b(this).removeClass("pp_expand").addClass("pp_contract"),r=!1):(b(this).removeClass("pp_contract").addClass("pp_expand"),r=!0);$(function(){h.open()});return!1});g.find(".pp_previous, .pp_nav .pp_arrow_previous").on("click",function(){h.changePage("previous");h.stopSlideshow();
return!1});g.find(".pp_next, .pp_nav .pp_arrow_next").on("click",function(){h.changePage("next");h.stopSlideshow();return!1});S()}function aa(a){a=B/2+T.scrollTop-a/2;0>a&&(a=0);return a}function S(){if(r&&g){T=X();var a=g.height(),b=g.width(),c=aa(a);a>B||g.css({top:c,left:m/2+T.scrollLeft-b/2})}}function s(a,b){var d=!1;ba(a,b);imageWidth=a;imageHeight=b;if((z>m||y>B)&&r&&c.allow_resize&&!L){for(var d=!0,e=!1;!e;)z>m?(imageWidth=m-200,imageHeight=b/a*imageWidth):y>B?(imageHeight=B-200,imageWidth=
a/b*imageHeight):e=!0,y=imageHeight,z=imageWidth;(z>m||y>B)&&s(z,y);ba(imageWidth,imageHeight)}return{width:Math.floor(imageWidth),height:Math.floor(imageHeight),containerHeight:Math.floor(y),containerWidth:Math.floor(z)+2*c.horizontal_padding,contentHeight:Math.floor(R),contentWidth:Math.floor(Y),resized:d}}function ba(a,d){a=parseFloat(a);d=parseFloat(d);$pp_details=g.find(".pp_details");$pp_details.width(a);var e=parseFloat($pp_details.css("marginTop"))+parseFloat($pp_details.css("marginBottom"));
$pp_details=$pp_details.clone().addClass(c.theme).width(a).appendTo(b("body")).css({position:"absolute",top:-1E4});e+=$pp_details.height();e=34>=e?36:e;$pp_details.remove();$pp_title=g.find(".ppt");$pp_title.width(a);var f=parseFloat($pp_title.css("marginTop"))+parseFloat($pp_title.css("marginBottom"));$pp_title=$pp_title.clone().appendTo(b("body")).css({position:"absolute",top:-1E4});f+=$pp_title.height();$pp_title.remove();R=d+e;Y=a;y=R+f+g.find(".pp_top").height()+g.find(".pp_bottom").height();
z=a}function U(a){return a.match(/youtube\.com\/watch/i)||a.match(/youtu\.be/i)?"youtube":a.match(/vimeo\.com/i)?"vimeo":a.match(/\b.mov\b/i)?"quicktime":a.match(/\b.swf\b/i)?"flash":a.match(/\b.(mp3|ogg)\b/i)?(N="mpeg",O="mp3",a.match(/\b.ogg\b/i)&&(N="ogg",O="vorbis"),"audio"):a.match(/\biframe=true\b/i)?"iframe":a.match(/\bajax=true\b/i)?"ajax":a.match(/\bcustom=true\b/i)?"custom":"#"==a.substr(0,1)?"inline":"image"}function $(a){g.find("#pp_full_res object,#pp_full_res embed").css({visibility:"hidden"});
g.find(".pp_fade").fadeOut(c.animation_speed,function(){b(".pp_loaderIcon").show();a()})}function V(){b(".pp_loaderIcon").hide();var a=aa(f.containerHeight),K=c.animation_speed;D.fadeTo(K,1);g.find(".pp_content").animate({height:f.contentHeight,width:f.contentWidth},K);g.animate({top:a,left:0>m/2-f.containerWidth/2?0:m/2-f.containerWidth/2,width:f.containerWidth},K,function(){g.find(".pp_hoverContainer,#fullResImage").height(f.height).width(f.width);g.find(".pp_fade").fadeIn(K);q&&"image"==U(e[d])?
g.find(".pp_hoverContainer").show():g.find(".pp_hoverContainer").hide();c.allow_expand&&(f.resized?b("a.pp_expand,a.pp_contract").show():b("a.pp_expand").hide());c.autoplay_slideshow&&(null===G&&!Q)&&h.startSlideshow();c.changepicturecallback();Q=!0});if(q&&c.overlay_gallery&&"image"==U(e[d])){a="facebook"==c.theme||"pp_default"==c.theme?50:30;n=Math.floor((f.containerWidth-100-a)/M);n=n<e.length?n:e.length;E=Math.ceil(e.length/n)-1;0==E?(a=0,$pp_gallery.find(".pp_arrow_next,.pp_arrow_previous").hide()):
$pp_gallery.find(".pp_arrow_next,.pp_arrow_previous").show();var k=n*M,l=e.length*M,p=Math.floor(d/n)<E?Math.floor(d/n):E;$pp_gallery.css("margin-left",-(k/2+a/2)).find("div:first").width(k+5).find("ul").width(l).find("li.selected").removeClass("selected");h.changeGalleryPage(p);$pp_gallery_li.filter(":eq("+d+")").addClass("selected")}else g.find(".pp_content").off("mouseenter mouseleave");w.ajaxcallback()}var v=0,F,q=!1,n,M=57,k="",H=this,I,e,L=!1,r=!0,T=X(),d,J,E=0,w=jQuery.extend({hook:"data-popUp",
hookWord:"fwPopup",animation_speed:"fast",ajaxcallback:function(){},slideshow:5E3,autoplay_slideshow:!1,opacity:0.8,show_title:!0,allow_resize:!0,allow_expand:!0,default_width:500,default_height:344,counter_separator_label:"/",theme:"pp_default",horizontal_padding:20,hideflash:!1,wmode:"opaque",autoplay:!0,modal:!1,deeplinking:!0,overlay_gallery:!0,overlay_gallery_max:30,keyboard_shortcuts:!0,changepicturecallback:function(){},callback:function(){},ie6_fallback:!0,markup:function(){var a=[];a.push('<div class="pp_pic_holder">');
a.push('<div class="ppt">&nbsp;</div>');a.push('<div class="pp_top">');a.push('<div class="pp_left"></div>');a.push('<div class="pp_middle"></div>');a.push('<div class="pp_right"></div>');a.push("</div>");a.push('<div class="pp_content_container">');a.push('<div class="pp_left">');a.push('<div class="pp_right">');a.push('<div class="pp_content">');a.push('<div class="pp_loaderIcon"></div>');a.push('<div class="pp_fade">');a.push('<a href="javascript:void(0)" class="pp_expand" title="Expand the image">Expand</a>');
a.push('<div class="pp_hoverContainer">');a.push('<a class="pp_next" href="javascript:void(0)">next</a>');a.push('<a class="pp_previous" href="javascript:void(0)">previous</a>');a.push("</div>");a.push('<div id="pp_full_res"></div>');a.push('<div class="pp_details">');a.push('<div class="pp_nav">');a.push('<a href="javascript:void(0)" class="pp_arrow_previous">Previous</a>');a.push('<p class="currentTextHolder">0/0</p>');a.push('<a href="javascript:void(0)" class="pp_arrow_next">Next</a>');a.push("</div>");
a.push('<p class="pp_description"></p>');a.push('<div class="pp_social">{pp_social}</div>');a.push('<a class="pp_close" href="javascript:void(0)">Close</a>');a.push("</div>");a.push("</div>");a.push("</div>");a.push("</div>");a.push("</div>");a.push('<div class="pp_bottom">');a.push('<div class="pp_left"></div>');a.push('<div class="pp_middle"></div>');a.push('<div class="pp_right"></div>');a.push("</div>");a.push("</div>");a.push("</div>");a.push('<div class="pp_overlay"></div>');return a.join("")}(),
audio_markup:'{image}<audio controls autoplay class="audioPlayback"><source src="{path}" type="audio/{type}" codec="{codec}"/></audio>',custom_markup:"",flash_markup:'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',
gallery_markup:function(){var a=[];a.push('<div class="pp_gallery">');a.push('<a href="javascript:void(0)" class="pp_arrow_previous">Previous</a>');a.push("<div>");a.push("<ul>");a.push("{gallery}");a.push("</ul>");a.push("</div>");a.push('<a href="javascript:void(0)" class="pp_arrow_next">Next</a>');a.push("</div>");return a.join("")}(),iframe_markup:'<iframe src ="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',image_markup:'<img id="fullResImage" src="{path}"/>',inline_markup:'<div class="pp_inline">{content}</div>',
quicktime_markup:'<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="//www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',social_tools:'<div class="twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"><\/script></div><div class="facebook"><iframe src="//www.facebook.com/plugins/like.php?locale=en_US&href={location_href}&amp;layout=button_count&amp;show_faces=true&amp;width=500&amp;action=like&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden;width:500px;height:23px;" allowTransparency="true"></iframe></div>'},
w);l=w.hookWord;t.off("resize.fwPopup").on("resize.fwPopup",function(){S();B=t.height();m=t.width();A&&A.height(b(j).height()).width(m)});if(w.keyboard_shortcuts)b(j).off("keydown.fwPopup").on("keydown.fwPopup",function(a){if(g&&g.is(":visible"))switch(a.keyCode){case 37:h.changePage("previous");a.preventDefault();break;case 39:h.changePage("next");a.preventDefault();break;case 27:c.modal||h.close(),a.preventDefault()}});h.initialize=function(){c=w;var a=c.hook;"pp_default"==c.theme&&(c.horizontal_padding=
16);l=b(this).attr(a);e=(q=/\[(?:.*)\]/.exec(l))?jQuery.map(H,function(c){if(b(c).attr(a).indexOf(l)+1)return b(c).attr("href")}):b.makeArray(b(this).attr("href"));J=q?jQuery.map(H,function(c){if(b(c).attr(a).indexOf(l)+1)return b(valuen).find("img").attr("alt")?b(c).find("img").attr("alt"):""}):b.makeArray(b(this).find("img").attr("alt"));I=q?jQuery.map(H,function(c){if(b(c).attr(a).indexOf(l)+1)return b(c).data()?b(c).data():""}):b.makeArray(b(this).data());F=q?jQuery.map(H,function(c){if(b(c).attr(a).indexOf(l)+
1)return b(c).attr("title")?b(c).attr("title"):""}):b.makeArray(b(this).attr("title"));e.length>c.overlay_gallery_max&&(c.overlay_gallery=!1);d=jQuery.inArray(b(this).attr("href"),e);P=q?d:b("a["+a+"^='"+l+"']").index(b(this));Z(this);if(c.allow_resize)t.on("scroll.fwPopup",S);h.open();return!1};h.open=function(a,j,m,n){"undefined"==typeof c&&(c=w,e=b.makeArray(a),J=j?b.makeArray(j):b.makeArray(""),F=m?b.makeArray(m):b.makeArray(""),q=1<e.length,d=n?n:0,Z(a.target));c.hideflash&&b("object,embed,iframe[src*=youtube],iframe[src*=vimeo]").css({visibility:"hidden"});
1<b(e).size()?b(".pp_nav").show():b(".pp_nav").hide();b(".pp_loaderIcon").show();c.deeplinking&&"undefined"!=typeof l&&(location.hash=l+"/"+P+"/");c.social_tools&&(a=c.social_tools.replace("{location_href}",encodeURIComponent(location.href)),g.find(".pp_social").html(a));D.is(":hidden")&&D.css("opacity",0).show();A.show().fadeTo(c.animation_speed,c.opacity);g.find(".currentTextHolder").text(d+1+c.counter_separator_label+b(e).size());"undefined"!=typeof F[d]&&""!=F[d]?g.find(".pp_description").show().html(unescape(F[d])):
g.find(".pp_description").hide();var p=parseFloat(x("width",e[d]))?x("width",e[d]):c.default_width.toString(),u=parseFloat(x("height",e[d]))?x("height",e[d]):c.default_height.toString();L=!1;u.indexOf("%")+1&&(u=parseFloat(t.height()*parseFloat(u)/100-150),L=!0);p.indexOf("%")+1&&(p=parseFloat(t.width()*parseFloat(p)/100-150),L=!0);g.fadeIn(function(){c.show_title&&""!=J[d]&&"undefined"!=typeof J[d]?D.html(unescape(J[d])):D.addClass("no_content").html("");var a=0,l=0;switch(U(e[d])){case "ajax":r=
!1;f=s(p,u);l=r=!0;b.get(e[d],function(a){k=c.inline_markup.replace(/{content}/g,a);g.find("#pp_full_res")[0].innerHTML=k;V()});break;case "audio":f=s(I[d].width,I[d].height);var i=new Audio,j=I[d].image?'<img src="'+I[d].image+'" alt="Cover for '+F[d]+'"/>':"",m=c.audio_markup.replace(/{type}/g,N),m=m.replace(/{codec}/g,O),m=m.replace(/{image}/g,j);k=m.replace(/{path}/g,e[d]);i.setAttribute("src",e[d]);i.load();break;case "custom":f=s(p,u);k=c.custom_markup;break;case "flash":f=s(p,u);i=e[d];j=e[d];
i=i.substring(e[d].indexOf("flashvars")+10,e[d].length);j=j.substring(0,j.indexOf("?"));k=c.flash_markup.replace(/{width}/g,f.width).replace(/{height}/g,f.height).replace(/{wmode}/g,c.wmode).replace(/{path}/g,j+"?"+i);break;case "iframe":f=s(p,u);i=e[d];i=i.substr(0,i.indexOf("iframe")-1);k=c.iframe_markup.replace(/{width}/g,f.width).replace(/{height}/g,f.height).replace(/{path}/g,i);break;case "image":a=new Image;i=new Image;j=new Image;q&&d<b(e).size()-1&&(i.src=e[d+1]);q&&e[d-1]&&(j.src=e[d-1]);
g.find("#pp_full_res")[0].innerHTML=c.image_markup.replace(/{path}/g,e[d]);a.onload=function(){f=s(a.width,a.height);V()};a.onerror=function(){alert("Image cannot be loaded. Make sure the path is correct and image exist.");h.close()};a.src=e[d];break;case "inline":i=b(e[d]).clone().append('<br clear="all">').css({width:c.default_width}).wrapInner('<div id="pp_full_res"><div class="pp_inline"></div></div>').appendTo(b("body")).show();r=!1;f=s(b(i).width(),b(i).height());r=!0;b(i).remove();k=c.inline_markup.replace(/{content}/g,
b(e[d]).html());break;case "quicktime":f=s(p,u);f.height+=15;f.contentHeight+=15;f.containerHeight+=15;k=c.quicktime_markup.replace(/{width}/g,f.width).replace(/{height}/g,f.height).replace(/{wmode}/g,c.wmode).replace(/{path}/g,e[d]).replace(/{autoplay}/g,c.autoplay);break;case "vimeo":f=s(p,u);movie="//player.vimeo.com/video/"+e[d].match(/http(s?):\/\/(www\.)?vimeo.com\/(\d+)/)[3]+"?title=0&byline=0&portrait=0";c.autoplay&&(movie+="&autoplay=1;");vimeo_width=f.width+"/embed/?moog_width="+f.width;
k=c.iframe_markup.replace(/{width}/g,vimeo_width).replace(/{height}/g,f.height).replace(/{path}/g,movie);break;case "youtube":f=s(p,u),i=x("v",e[d]),""==i&&(i=e[d].split("youtu.be/"),i=i[1],0<i.indexOf("?")&&(i=i.substr(0,i.indexOf("?"))),0<i.indexOf("&")&&(i=i.substr(0,i.indexOf("&")))),movie="//www.youtube.com/embed/"+i,x("rel",e[d])?movie+="?rel="+x("rel",e[d]):movie+="?rel=1",c.autoplay&&(movie+="&autoplay=1"),k=c.iframe_markup.replace(/{width}/g,f.width).replace(/{height}/g,f.height).replace(/{wmode}/g,
c.wmode).replace(/{path}/g,movie)}!a&&!l&&(g.find("#pp_full_res")[0].innerHTML=k,V())});return!1};h.changePage=function(a){v=0;"previous"==a?(d--,0>d&&(d=b(e).size()-1)):"next"==a?(d++,d>b(e).size()-1&&(d=0)):d=a;P=d;r||(r=!0);c.allow_expand&&b(".pp_contract").removeClass("pp_contract").addClass("pp_expand");$(function(){h.open()})};h.changeGalleryPage=function(a){"next"==a?(v++,v>E&&(v=0)):"previous"==a?(v--,0>v&&(v=E)):v=a;var a="next"==a||"previous"==a?c.animation_speed:0,b=v*n*M;$pp_gallery.find("ul").animate({left:-b},
a)};h.startSlideshow=function(){null===G?(g.find(".pp_play").off("click").removeClass("pp_play").addClass("pp_pause").click(function(){h.stopSlideshow();return!1}),G=setInterval(h.startSlideshow,c.slideshow)):h.changePage("next")};h.stopSlideshow=function(){g.find(".pp_pause").off("click").removeClass("pp_pause").addClass("pp_play").click(function(){h.startSlideshow();return!1});clearInterval(G);G=null};h.close=function(){if(!A.is(":animated")){h.stopSlideshow();g.stop().find("object,embed").css({visibility:"hidden"});
var a=c.animation_speed;b("div.pp_pic_holder,div.ppt,.pp_fade").fadeOut(a,function(){b(this).remove()});A.fadeOut(a,function(){c.hideflash&&b("object,embed,iframe[src*=youtube],iframe[src*=vimeo]").css({visibility:"visible"});b(this).remove();t.off("scroll.fwPopup");location.href.indexOf("#"+l)+1&&(location.hash=l);c.callback();r=!0;Q=!1;c=void 0})}};if(!h.initialized&&W()){h.initialized=!0;var C=W(),ca=C.substring(C.indexOf("/")+1,C.length-1),C=C.substring(0,C.indexOf("/"));setTimeout(function(){b("a["+
w.hook+"^='"+C+"']:eq("+ca+")").trigger("click")},50)}return H.off("click.fwPopup").on("click.fwPopup",h.initialize)}})(jQuery);