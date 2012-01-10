var BP={};var Sha1={};
Sha1.hash=function(a,c){(typeof c=="undefined"||c)&&(a=Utf8.encode(a));var b=[1518500249,1859775393,2400959708,3395469782];a+=String.fromCharCode(128);for(var d=Math.ceil((a.length/4+2)/16),k=Array(d),j=0;j<d;j++){k[j]=Array(16);for(var l=0;l<16;l++)k[j][l]=a.charCodeAt(j*64+l*4)<<24|a.charCodeAt(j*64+l*4+1)<<16|a.charCodeAt(j*64+l*4+2)<<8|a.charCodeAt(j*64+l*4+3)}k[d-1][14]=(a.length-1)*8/Math.pow(2,32);k[d-1][14]=Math.floor(k[d-1][14]);k[d-1][15]=(a.length-1)*8&4294967295;for(var l=1732584193,i=
4023233417,n=2562383102,q=271733878,p=3285377520,m=Array(80),e,h,f,g,r,j=0;j<d;j++){for(var o=0;o<16;o++)m[o]=k[j][o];for(o=16;o<80;o++)m[o]=Sha1.ROTL(m[o-3]^m[o-8]^m[o-14]^m[o-16],1);e=l;h=i;f=n;g=q;r=p;for(o=0;o<80;o++){var s=Math.floor(o/20),s=Sha1.ROTL(e,5)+Sha1.f(s,h,f,g)+r+b[s]+m[o]&4294967295;r=g;g=f;f=Sha1.ROTL(h,30);h=e;e=s}l=l+e&4294967295;i=i+h&4294967295;n=n+f&4294967295;q=q+g&4294967295;p=p+r&4294967295}return Sha1.toHexStr(l)+Sha1.toHexStr(i)+Sha1.toHexStr(n)+Sha1.toHexStr(q)+Sha1.toHexStr(p)};
Sha1.f=function(a,c,b,d){switch(a){case 0:return c&b^~c&d;case 1:return c^b^d;case 2:return c&b^c&d^b&d;case 3:return c^b^d}};Sha1.ROTL=function(a,c){return a<<c|a>>>32-c};Sha1.toHexStr=function(a){for(var c="",b,d=7;d>=0;d--)b=a>>>d*4&15,c+=b.toString(16);return c};
var Utf8={encode:function(a){a=a.replace(/[\u0080-\u07ff]/g,function(a){a=a.charCodeAt(0);return String.fromCharCode(192|a>>6,128|a&63)});return a=a.replace(/[\u0800-\uffff]/g,function(a){a=a.charCodeAt(0);return String.fromCharCode(224|a>>12,128|a>>6&63,128|a&63)})},decode:function(a){a=a.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,function(a){a=(a.charCodeAt(0)&15)<<12|(a.charCodeAt(1)&63)<<6|a.charCodeAt(2)&63;return String.fromCharCode(a)});return a=a.replace(/[\u00c0-\u00df][\u0080-\u00bf]/g,
function(a){a=(a.charCodeAt(0)&31)<<6|a.charCodeAt(1)&63;return String.fromCharCode(a)})}},Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(a){for(var c="",b,d,k,j,l,i,n=0,a=Base64._utf8_encode(a);n<a.length;)b=a.charCodeAt(n++),d=a.charCodeAt(n++),k=a.charCodeAt(n++),j=b>>2,b=(b&3)<<4|d>>4,l=(d&15)<<2|k>>6,i=k&63,isNaN(d)?l=i=64:isNaN(k)&&(i=64),c=c+this._keyStr.charAt(j)+this._keyStr.charAt(b)+this._keyStr.charAt(l)+this._keyStr.charAt(i);return c},
decode:function(a){for(var c="",b,d,k,j,l,i=0,a=a.replace(/[^A-Za-z0-9\+\/\=]/g,"");i<a.length;)b=this._keyStr.indexOf(a.charAt(i++)),d=this._keyStr.indexOf(a.charAt(i++)),j=this._keyStr.indexOf(a.charAt(i++)),l=this._keyStr.indexOf(a.charAt(i++)),b=b<<2|d>>4,d=(d&15)<<4|j>>2,k=(j&3)<<6|l,c+=String.fromCharCode(b),j!=64&&(c+=String.fromCharCode(d)),l!=64&&(c+=String.fromCharCode(k));return c=Base64._utf8_decode(c)},_utf8_encode:function(a){for(var a=a.replace(/\r\n/g,"\n"),c="",b=0;b<a.length;b++){var d=
a.charCodeAt(b);d<128?c+=String.fromCharCode(d):(d>127&&d<2048?c+=String.fromCharCode(d>>6|192):(c+=String.fromCharCode(d>>12|224),c+=String.fromCharCode(d>>6&63|128)),c+=String.fromCharCode(d&63|128))}return c},_utf8_decode:function(a){for(var c="",b=0,d=c1=c2=0;b<a.length;)d=a.charCodeAt(b),d<128?(c+=String.fromCharCode(d),b++):d>191&&d<224?(c2=a.charCodeAt(b+1),c+=String.fromCharCode((d&31)<<6|c2&63),b+=2):(c2=a.charCodeAt(b+1),c3=a.charCodeAt(b+2),c+=String.fromCharCode((d&15)<<12|(c2&63)<<6|
c3&63),b+=3);return c}},MD5=function(a){function c(a,c){var b,f,g,d,e;g=a&2147483648;d=c&2147483648;b=a&1073741824;f=c&1073741824;e=(a&1073741823)+(c&1073741823);return b&f?e^2147483648^g^d:b|f?e&1073741824?e^3221225472^g^d:e^1073741824^g^d:e^g^d}function b(a,b,f,g,e,d,h){a=c(a,c(c(b&f|~b&g,e),h));return c(a<<d|a>>>32-d,b)}function d(a,b,f,g,d,e,h){a=c(a,c(c(b&g|f&~g,d),h));return c(a<<e|a>>>32-e,b)}function k(a,b,f,g,e,d,h){a=c(a,c(c(b^f^g,e),h));return c(a<<d|a>>>32-d,b)}function j(a,b,f,g,d,e,
h){a=c(a,c(c(f^(b|~g),d),h));return c(a<<e|a>>>32-e,b)}function l(a){var c="",b="",f;for(f=0;f<=3;f++)b=a>>>f*8&255,b="0"+b.toString(16),c+=b.substr(b.length-2,2);return c}var i=[],n,q,p,m,e,h,f,g,a=function(a){for(var a=a.replace(/\r\n/g,"\n"),c="",b=0;b<a.length;b++){var f=a.charCodeAt(b);f<128?c+=String.fromCharCode(f):(f>127&&f<2048?c+=String.fromCharCode(f>>6|192):(c+=String.fromCharCode(f>>12|224),c+=String.fromCharCode(f>>6&63|128)),c+=String.fromCharCode(f&63|128))}return c}(a),i=function(a){var c,
b=a.length;c=b+8;for(var f=((c-c%64)/64+1)*16,g=Array(f-1),e=0,d=0;d<b;)c=(d-d%4)/4,e=d%4*8,g[c]|=a.charCodeAt(d)<<e,d++;g[(d-d%4)/4]|=128<<d%4*8;g[f-2]=b<<3;g[f-1]=b>>>29;return g}(a);e=1732584193;h=4023233417;f=2562383102;g=271733878;for(a=0;a<i.length;a+=16)n=e,q=h,p=f,m=g,e=b(e,h,f,g,i[a+0],7,3614090360),g=b(g,e,h,f,i[a+1],12,3905402710),f=b(f,g,e,h,i[a+2],17,606105819),h=b(h,f,g,e,i[a+3],22,3250441966),e=b(e,h,f,g,i[a+4],7,4118548399),g=b(g,e,h,f,i[a+5],12,1200080426),f=b(f,g,e,h,i[a+6],17,2821735955),
h=b(h,f,g,e,i[a+7],22,4249261313),e=b(e,h,f,g,i[a+8],7,1770035416),g=b(g,e,h,f,i[a+9],12,2336552879),f=b(f,g,e,h,i[a+10],17,4294925233),h=b(h,f,g,e,i[a+11],22,2304563134),e=b(e,h,f,g,i[a+12],7,1804603682),g=b(g,e,h,f,i[a+13],12,4254626195),f=b(f,g,e,h,i[a+14],17,2792965006),h=b(h,f,g,e,i[a+15],22,1236535329),e=d(e,h,f,g,i[a+1],5,4129170786),g=d(g,e,h,f,i[a+6],9,3225465664),f=d(f,g,e,h,i[a+11],14,643717713),h=d(h,f,g,e,i[a+0],20,3921069994),e=d(e,h,f,g,i[a+5],5,3593408605),g=d(g,e,h,f,i[a+10],9,38016083),
f=d(f,g,e,h,i[a+15],14,3634488961),h=d(h,f,g,e,i[a+4],20,3889429448),e=d(e,h,f,g,i[a+9],5,568446438),g=d(g,e,h,f,i[a+14],9,3275163606),f=d(f,g,e,h,i[a+3],14,4107603335),h=d(h,f,g,e,i[a+8],20,1163531501),e=d(e,h,f,g,i[a+13],5,2850285829),g=d(g,e,h,f,i[a+2],9,4243563512),f=d(f,g,e,h,i[a+7],14,1735328473),h=d(h,f,g,e,i[a+12],20,2368359562),e=k(e,h,f,g,i[a+5],4,4294588738),g=k(g,e,h,f,i[a+8],11,2272392833),f=k(f,g,e,h,i[a+11],16,1839030562),h=k(h,f,g,e,i[a+14],23,4259657740),e=k(e,h,f,g,i[a+1],4,2763975236),
g=k(g,e,h,f,i[a+4],11,1272893353),f=k(f,g,e,h,i[a+7],16,4139469664),h=k(h,f,g,e,i[a+10],23,3200236656),e=k(e,h,f,g,i[a+13],4,681279174),g=k(g,e,h,f,i[a+0],11,3936430074),f=k(f,g,e,h,i[a+3],16,3572445317),h=k(h,f,g,e,i[a+6],23,76029189),e=k(e,h,f,g,i[a+9],4,3654602809),g=k(g,e,h,f,i[a+12],11,3873151461),f=k(f,g,e,h,i[a+15],16,530742520),h=k(h,f,g,e,i[a+2],23,3299628645),e=j(e,h,f,g,i[a+0],6,4096336452),g=j(g,e,h,f,i[a+7],10,1126891415),f=j(f,g,e,h,i[a+14],15,2878612391),h=j(h,f,g,e,i[a+5],21,4237533241),
e=j(e,h,f,g,i[a+12],6,1700485571),g=j(g,e,h,f,i[a+3],10,2399980690),f=j(f,g,e,h,i[a+10],15,4293915773),h=j(h,f,g,e,i[a+1],21,2240044497),e=j(e,h,f,g,i[a+8],6,1873313359),g=j(g,e,h,f,i[a+15],10,4264355552),f=j(f,g,e,h,i[a+6],15,2734768916),h=j(h,f,g,e,i[a+13],21,1309151649),e=j(e,h,f,g,i[a+4],6,4149444226),g=j(g,e,h,f,i[a+11],10,3174756917),f=j(f,g,e,h,i[a+2],15,718787259),h=j(h,f,g,e,i[a+9],21,3951481745),e=c(e,n),h=c(h,q),f=c(f,p),g=c(g,m);return(l(e)+l(h)+l(f)+l(g)).toLowerCase()};var Controller=Cairngorm.FrontController.extend({init:function(){this._super();this.addCommand(ViewChangeEvent.VIEW_HOME_MODULE,ViewHomeModuleCommand);this.addCommand(ViewChangeEvent.VIEW_EXERCISE_MODULE,ViewExerciseModuleCommand);this.addCommand(ViewChangeEvent.VIEW_EVALUATION_MODULE,ViewEvaluationModuleCommand);this.addCommand(ViewChangeEvent.VIEW_SUBTITLE_MODULE,ViewSubtitleModuleCommand);this.addCommand(ViewChangeEvent.VIEW_ABOUT_MODULE,ViewAboutModuleCommand);this.addCommand(ViewChangeEvent.VIEW_CONFIG_MODULE,
ViewConfigModuleCommand);this.addCommand(ViewChangeEvent.VIEW_LOGIN_POPUP,ToggleLoginPopupCommand);this.addCommand(ViewChangeEvent.VIEW_POPSTATE,ViewPopStateCommand);this.addCommand(LoginEvent.PROCESS_LOGIN,ProcessLoginCommand);this.addCommand(LoginEvent.SIGN_OUT,SignOutCommand);this.addCommand(HomepageEvent.LATEST_USER_UPLOADED_VIDEOS,LatestUploadedVideosCommand);this.addCommand(HomepageEvent.BEST_RATED_VIDEOS_SIGNED_IN,SignedBestVideosCommand);this.addCommand(HomepageEvent.LATEST_USER_ACTIVITY,
LatestUserActivityCommand);this.addCommand(ExerciseEvent.EXERCISE_SELECTED,ExerciseSelectedCommand);this.addCommand(ExerciseEvent.GET_RECORDABLE_EXERCISES,GetRecordableExercisesCommand);this.addCommand(ExerciseEvent.REC_START,StartRecordingCommand);this.addCommand(ExerciseEvent.RECORDING_ABORTED,RecordingAbortedCommand);this.addCommand(ExerciseEvent.SAVE_RESPONSE,SaveResponseCommand);this.addCommand(ExerciseEvent.WATCH_RESPONSE,WatchResponseCommand);this.addCommand(ExerciseEvent.RECORD_AGAIN,RecordAgainCommand)}});function ExerciseManager(){this.bpPlayer=null;this.bpPlayerStates={PLAY_STATE:0,PLAY_BOTH_STATE:1,RECORD_MIC_STATE:2,RECORD_BOTH_STATE:3};this.selectedLocale=this.selectedRole=this.currentExercise=this.exerciseId=this.exerciseTitle=this.exerciseName=null;this.exerciseStartedPlaying=false;this.cueManager=null;this.cueManagerReady=false;this.recordedFilename=null;var a=this;this.loadExercise=function(a,b){this.bpPlayer=a;this.cueManager=new cuePointManager;this.setupVideoPlayer();this.onExerciseSelected(b)};
this.loadExerciseFromContent=function(a){var b=$("section.exerciseInfo"),d=b.data("id"),b=b.data("name");BP.selectedExercise=new ExerciseVO(d,b,null);this.loadExercise(a,BP.selectedExercise)};this.setupVideoPlayer=function(){this.bpPlayer.addEventListener("onRecordingAborted","BP.EM.recordingAbortedListener");this.bpPlayer.addEventListener("onRecordingFinished","BP.EM.recordingFinishedListener")};this.onExerciseSelected=function(a){this.exerciseName=a.name;this.exerciseTitle=a.title;this.exerciseId=
a.id;this.currentExercise=a;this.cueManagerReady=false;this.prepareExercise();this.resetCueManager();this.prepareCueManager()};this.prepareExercise=function(){this.bpPlayer&&(this.bpPlayer.stopVideo(),this.bpPlayer.state(this.bpPlayerStates.PLAY_STATE),this.bpPlayer.videoSource(this.exerciseName))};this.resetComp=function(){this.bpPlayer&&(this.bpPlayer.endVideo(),this.bpPlayer.setSubtitle(""),this.bpPlayer.videoSource(""),this.bpPlayer.state(this.bpPlayerStates.PLAY_STATE))};this.showArrows=function(){this.bpPlayer&&
(this.bpPlayer.arrows(true),this.bpPlayer.setArrows(this.cueManager.cues2rolearray(),this.selectedRole))};this.hideArrows=function(){this.bpPlayer&&(this.bpPlayer.arrows(false),this.bpPlayer.removeArrows())};this.setupRecording=function(){if(this.bpPlayer)this.selectedRole=$("select#recRole > option:selected").val(),this.selectedLocale=$("select#recLocale > option:selected").val(),this.setupRecordingCommands(),$("input[name=recordingMethod]:checked").val()=="micOnly"?this.bpPlayer.state(this.bpPlayerStates.RECORD_MIC_STATE):
this.bpPlayer.state(this.bpPlayerStates.RECORD_BOTH_STATE),this.showArrows(),this.showRecordingOptions()};this.resetCueManager=function(){this.cueManager.reset();this.bpPlayer.removeEventListener("onEnterFrame","BP.EM.enterFrameListener")};this.prepareCueManager=function(){this.cueManager.setVideo(this.exerciseId);this.cueManager.addEventListener("onSubtitlesRetrieved",a.onSubtitlesRetrieved);this.selectedLocale=$("select#recLocale > option:selected").val();this.cueManager.setCuesFromSubtitleUsingLocale(this.selectedLocale);
this.bpPlayer.removeEventListener("onEnterFrame","BP.EM.enterFrameListener");this.bpPlayer.addEventListener("onEnterFrame","BP.EM.enterFrameListener")};this.enterFrameListener=function(a){this.cueManager.monitorCuePoints(a)};this.showRecordingOptions=function(){$("article.exerciseInfo").fadeOut("fast",function(){var a=$("article.recordingEndOptions");a.find("button:lt(3)").attr("disabled","disabled");a.fadeIn()})};this.hideRecordingOptions=function(){$("article.recordingEndOptions").fadeOut("fast",
function(){$("article.exerciseInfo").fadeIn()})};this.onSubtitlesRetrieved=function(){a.currentResponse==void 0?a.setupPlayCommands():(a.bpPlayer.state(a.bpPlayerStates.PLAY_BOTH_STATE),a.bpPlayer.videoSource(a.currentResponse.name),a.bpPlayer.secondSource(a.currentResponse.file_identifier),a.selectedRole=a.currentResponse.character_name,a.setupRecordingCommands(),a.bpPlayer.addEventListener("onMetadataRetrieved","BP.EM.onMetadataRetrieved"))};this.setupPlayCommands=function(){var a=this.cueManager.getCuelist();
if(!(a.length<=0)){for(var b in a)a[b].setStartCommand(new onPlaybackCuePoint(a[b],this.bpPlayer)),a[b].setEndCommand(new onPlaybackCuePoint(null,this.bpPlayer));this.cueManagerReady=true;this.videoStartedPlayingListener(null)}};this.setupRecordingCommands=function(){var a=this.cueManager.getCuelist();if(!(a.length<=0)){for(var b in a)a[b].role!=this.selectedRole?(a[b].setStartCommand(new onRecordingOtherRoleCuePoint(a[b],this.bpPlayer)),a[b].setEndCommand(new onPlaybackCuePoint(null,this.bpPlayer))):
(a[b].setStartCommand(new onRecordingSelectedRoleStartCuePoint(a[b],this.bpPlayer)),a[b].setEndCommand(new onRecordingSelectedRoleStopCuePoint(this.bpPlayer)));this.bpPlayer.seek(false);this.cueManagerReady=true}};this.watchResponse=function(){a.showArrows();a.setupRecordingCommands();a.bpPlayer.videoSource(a.exerciseName);a.bpPlayer.state(a.bpPlayerStates.PLAY_BOTH_STATE);a.bpPlayer.secondSource(a.recordedFilename);a.bpPlayer.seek(false)};this.setupReplayCommands=function(){var a=this.cueManager.getCuelist();
if(!(a.length<=0)){for(var b in a)a[b].setStartCommand(new onReplayRecordingCuePoint(a[b],this.bpPlayer)),a[b].setEndCommand(new onReplayRecordingCuePoint(null,this.bpPlayer));this.cueManagerReady=true}};this.onMetadataRetrieved=function(){this.showArrows()};this.videoStartedPlayingListener=function(){};this.recordingAbortedListener=function(){alert("Devices not working");this.recordingError();this.prepareExercise();this.resetCueManager()};this.recordingFinishedListener=function(a){this.recordedFilename=
a;this.setupRecordingCommands();this.bpPlayer.videoSource(this.exerciseName);this.bpPlayer.state(this.bpPlayerStates.PLAY_BOTH_STATE);this.bpPlayer.secondSource(this.recordedFilename);this.bpPlayer.seek(false);$("article.recordingEndOptions > button:lt(3)").removeAttr("disabled")};this.recordingError=function(){this.hideArrows();this.bpPlayer.unattachUserDevices();this.bpPlayer.state(this.bpPlayerStates.PLAY_STATE);this.bpPlayer.removeEventListener("onEnterFrame","bpExercises.onEnterFrameListener");
this.hideRecordingOptions()};this.saveResponse=function(){var c=this.cueManager.currentSubtitle(),b=this.bpPlayer.duration();BP.Services.send(false,"saveResponse",{userId:null,exerciseId:a.exerciseId,fileIdentifier:a.recordedFilename,isPrivate:true,thumbnailUri:"nothumb.png",source:"Red5",duration:b,addingDate:null,ratingAmount:0,characterName:a.selectedRole,transcriptionId:0,subtitleId:c},this.saveResponseCallback)};this.saveResponseCallback=function(a){a==void 0||a.response==void 0?alert("Error while saving the response. Please try again later"):
BP.Services.send(false,"makePublic",responseId,this.publishResponseCallback)};this.publishResponseCallback=function(a){a==void 0||a.response==void 0||a.response.creditCount==void 0?alert("Error updating response's status"):(a=a.response,$("span#creditCount").text(a.creditCount),alert("Your response has been published. Thanks for your collaboration."))}};BP.CMS=function(){function a(){q.find("ul > li > a").not(":last").hover(function(){$(this).parent().css("background","url(themes/babelium/images/separator.png) no-repeat center right, url(themes/babelium/images/button_nav_highlight_"+$(this).attr("class")+".png) no-repeat 50% 58%")},function(){$(this).parent().css("background","url(themes/babelium/images/separator.png) no-repeat center right")});q.find("ul > li > a:last").hover(function(){$(this).parent().css("background"," url(themes/babelium/images/button_nav_highlight_"+
$(this).attr("class")+".png) no-repeat 50% 58%")},function(){$(this).parent().css("background","none")})}function c(){var a=$("select#localebox").css("display","none");a.parent().append("<div class='localebox'></div>");var c=$(".localebox");c.append("<div class='selectBox'></div>");c.append("<ul class='dropDown'></ul>");var b=$(".selectBox"),d=$(".dropDown").hide();a.find("option").each(function(){var c=$(this),g=$(this).text(),g="<img src='"+$(this).data("icon")+"' width='16' height='16' align='left' hspace='3' vspace='3' alt='"+
g+"' />"+g;$(this).is(":selected")&&b.html(g+"<img src='themes/babelium/images/arrow-down.png'alignt='left' style='float:right; margin-right: 3px;' />");g=$("<li>",{html:g});d.append(g);g.click(function(){b.html($(this).html()+"<img src='themes/babelium/images/arrow-down.png'alignt='left' style='float:right; margin-right: 3px;' />");d.slideUp();a.val(c.val())})});b.click(function(){d.slideToggle()});$(document).click(function(){if(d.is(":animated"))return false;d.slideUp()})}function b(){BP.at("home")&&
!BP.action()&&n("#motdmessageshelper","#motdmessages")}function d(){BP.at("home")&&BP.action("activity")&&(l("#assesmentsReceived"),l("#assesmentsGiven"))}function k(){if(BP.at("practice")){var a=void 0;typeof a=="undefined"&&(a=10);$("section.exerciseList").jplist({filter:{title:".exerciseTitle",description:"p.exerciseDescription"},filter_path:".paginationFilter",pagingbox:".paginationButtons",pageinfo:".paginationInfo",paging_dd_path:".paginationPage-by",items_box:".exerciseContainer",item_path:".exercise",
items_on_page:a,redraw_callback:i})}}function j(a,c){c==true?$(a).each(function(){$(this).raty({path:"themes/babelium/images/raty",readOnly:$(this).data("readonly"),half:true,start:$(this).data("rating")/2,size:24,starHalf:"star-half-big.png",starOff:"star-off-big.png",starOn:"star-on-big.png"})}):$(a).each(function(){$(this).raty({path:"themes/babelium/images/raty",readOnly:$(this).data("readonly"),half:true,start:$(this).data("rating")/2})})}function l(a){a=$(a).dataTable({bJQueryUI:true,sPaginationType:"full_numbers"});
j($(a.fnGetNodes()).find(".raty"))}function i(){j(".raty");var a=$("#exerciseVideoContainer");a.length!=0&&a.find("article").each(function(){var a=$(this);a.click(function(){(new ExerciseEvent(ExerciseEvent.EXERCISE_SELECTED,new ExerciseVO(a.data("id"),a.data("name"),a.data("title")))).dispatch()})})}function n(a,c){if(!($(a).length==0||$(c).length==0))return function(){function b(a){a<1||a>h||($(c+" > *:nth-child("+d+")").fadeOut(500,function(){$(c+" > *:nth-child("+a+")").fadeIn(500)}),d=a)}var d=
1,e=c,h=$(a+" > *").length;$(e+" > *").css("display","none");$(e+" > *:first-child").css("display","block");$(a+" > *").each(function(a){$(this).click(function(){b(a+1)})});return{next:function(){var a=d+1;a>h?b(1):b(a)},prev:function(){var a=d-1;a>1?b(h):b(a)},show:function(a){b(a)}}}()}var q,p,m,e=false,h=false;return{init:function(){e||($("#usernav"),q=$("#mainnav"),$("#searchnav"),p=$("section#maincontent"),m=$("aside#loader > div"),a(),c(),b(),d(),k(),BP.at("home")&&!BP.action("activity")&&j(".raty"),
BP.at("practice")&&BP.action("view")&&j(".ratyPreview",true),e=true)},prepareMainContent:function(a,c,b){if(!h&&e){h=true;var d=$("aside#motd"),i=p.find("header"),j=p.offset().top;m.css("top",j);m.find("p").html("Loading <strong>"+a+"</strong>");m.slideDown();$("html, body").scrollTop()>j&&$("html, body").animate({scrollTop:j},"slow");$("#maincontent > section").length>0?(d.length>0&&!b&&(d.fadeOut(500,function(){d.remove()}),i.slideDown(500)),b&&i.slideUp(500),$.when($("#maincontent > section").fadeOut()).done(function(){$("#maincontent > section").remove();
c()})):c()}},prepareExerciseView:function(a){if(!h&&e){h=true;var c=p.offset().top;m.css("top",c+p.find("header").outerHeight());m.find("p").text("Retrieving exercise information");m.slideDown(500);$("html, body").animate({scrollTop:c},"slow",function(){a()})}},viewStack:function(a,c){return!e?void 0:n(a,c)},reloadViewStacks:function(){e&&b()},reloadDataTables:function(){e&&d()},reloadPaginations:function(){e&&k()},reloadRatings:function(){e&&(BP.at("home")&&!BP.action("activity")&&j(".raty"),BP.at("practice")&&
BP.action("view")&&j(".ratyPreview",true))},innerMainContent:function(a){h&&(a=$.parseJSON(a),$("#maincontent > header > h1").text(a.title),$(a.content).hide().appendTo("#maincontent").fadeIn(),m.slideUp(),this.reloadViewStacks(),this.reloadDataTables(),this.reloadPaginations(),this.reloadRatings(),h=false)},innerExerciseView:function(a){if(h){var c=this,a=$.parseJSON(a),b=$(a.content).hide();$("section.exerciseInfo").length>0?$("section.exerciseInfo").fadeOut("fast",function(){$("section.exerciseInfo").remove();
b.insertAfter("aside#loader").fadeIn();c.reloadRatings();m.slideUp(500)}):(b.insertAfter("aside#loader").fadeIn(),c.reloadRatings(),m.slideUp(500));h=false}},toggleLoginPopup:function(){e&&($("aside#popup").is(":visible")?this.hideLoginPopup():this.showLoginPopup())},hideLoginPopup:function(){e&&$("aside#popup").fadeOut("fast",function(){document.getElementById("loginForm").reset()})},showLoginPopup:function(){e&&$("aside#popup").fadeIn()}}}();var ExerciseEvent=Cairngorm.Event.extend({init:function(a,c,b,d){this._super(a,{exercise:c,report:b,score:d})}});ExerciseEvent.EXERCISE_SELECTED="exerciseSelected";ExerciseEvent.GET_RECORDABLE_EXERCISES="getRecordableExercises";ExerciseEvent.REC_START="exerciseRecStart";ExerciseEvent.RECORDING_ABORTED="exerciseRecAborted";ExerciseEvent.SAVE_RESPONSE="exerciseSaveResponse";ExerciseEvent.WATCH_RESPONSE="exerciseWatchResponse";ExerciseEvent.RECORD_AGAIN="exerciseRecordAgain";var HomepageEvent=Cairngorm.Event.extend({init:function(a){this._super(a)}});HomepageEvent.BEST_RATED_VIDEOS_SIGNED_IN="bestRatedVideosSignedIn";HomepageEvent.LATEST_USER_UPLOADED_VIDEOS="latestUserUploadedVideos";HomepageEvent.LATEST_USER_ACTIVITY="latestUserActivity";var LoginEvent=Cairngorm.Event.extend({init:function(a,c){this._super(a,c)}});LoginEvent.PROCESS_LOGIN="processLogin";LoginEvent.SIGN_OUT="signOut";var ViewChangeEvent=Cairngorm.Event.extend({init:function(a,c){this._super(a,c)}});ViewChangeEvent.VIEW_HOME_MODULE="viewHomeModule";ViewChangeEvent.VIEW_EXERCISE_MODULE="viewExerciseModule";ViewChangeEvent.VIEW_EVALUATION_MODULE="viewEvaluationModule";ViewChangeEvent.VIEW_SUBTITLE_MODULE="viewSubtitleModule";ViewChangeEvent.VIEW_ABOUT_MODULE="viewAboutModule";ViewChangeEvent.VIEW_CONFIG_MODULE="viewConfigModule";ViewChangeEvent.VIEW_LOGIN_POPUP="viewLoginPopup";ViewChangeEvent.VIEW_POPSTATE="viewPopState";var ViewPopStateCommand=Cairngorm.Command.extend({execute:function(){var a=this,c=this.data.module,b=typeof this.data.action=="undefined"?"":this.data.action,d=typeof this.data.params=="undefined"?"":this.data.params,k=new Cairngorm.HTTPService({target:"modules/bridge.php?module=",method:"get"},c);BP.CMS.prepareMainContent(c,function(){k.call("action="+b+"&params="+d,a);BP.state=a.data},c=="home")},onResult:function(a){BP.CMS.innerMainContent(a)},onFault:function(){alert("Error loading last module")}});var ToggleLoginPopupCommand=Cairngorm.Command.extend({execute:function(){BP.CMS.toggleLoginPopup()}});var ViewHomeModuleCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("home",function(){BP.HomeDelegate.viewHomeModule(a,a.data===true)},true)},onResult:function(a){BP.pushState({module:"home"},"Home - Babelium Project","?module=home");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error loading home module")}});var LatestUploadedVideosCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("latest videos",function(){BP.HomeDelegate.latestAvailableVideos(a)},true)},onResult:function(a){BP.pushState({module:"home",action:"uploaded"},"Home :: Latest uploaded videos - Babelium Project","?module=home&action=uploaded");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error retrieving latest videos")}});var SignedBestVideosCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("best videos",function(){BP.HomeDelegate.topScoreMostViewedVideos(a)},true)},onResult:function(a){BP.pushState({module:"home",action:"rated"},"Home :: Best rated videos - Babelium Project","?module=home&action=rated");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error retrieving best rated videos")}});var LatestUserActivityCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("latest activity",function(){BP.HomeDelegate.latestUserActivity(a)},true)},onResult:function(a){BP.pushState({module:"home",action:"activity"},"Home :: Best rated videos - Babelium Project","?module=home&action=activity");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error retrieving latest user activity")}});var ExerciseSelectedCommand=Cairngorm.Command.extend({execute:function(){var a=this;if(this.data!=null)BP.selectedExercise=this.data.exercise,BP.CMS.prepareExerciseView(function(){BP.PracticeDelegate.viewExerciseByName(a,BP.selectedExercise.name)})},onResult:function(a){var c=BP.selectedExercise.name;BP.pushState({module:"practice",action:"view",params:c},BP.selectedExercise.title+" - Practice - Babelium Project","?module=practice&action=view&params="+c);BP.CMS.innerExerciseView(a)},onFault:function(){alert("Error loading exercise")}});var GetRecordableExercisesCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("practice module",function(){BP.PracticeDelegate.getRecordableExercises(a)})},onResult:function(a){BP.pushState({module:"practice"},"Practice - Babelium Project","?module=practice");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error loading practice module")}});var RecordAgainCommand=Cairngorm.Command.extend({execute:function(){BP.selectedExercise!=null&&(BP.EM.bpPlayer.videoSource(BP.EM.exerciseName),BP.EM.setupRecording())}});var RecordingAbortedCommand=Cairngorm.Command.extend({execute:function(){BP.selectedExercise!=null&&(BP.EM.recordingError(),BP.EM.prepareExercise(),BP.EM.resetCueManager())}});var SaveResponseCommand=Cairngorm.Command.extend({execute:function(){BP.selectedExercise!=null&&BP.EM.saveResponse()}});var StartRecordingCommand=Cairngorm.Command.extend({execute:function(){BP.selectedExercise!=null&&BP.EM.setupRecording()},onResult:function(a){var c=BP.selectedExercise.name;BP.pushState({module:"practice",action:"rec",params:c},BP.selectedExercise.title+" - Practice - Babelium Project","?module=practice&action=rec&params="+c);BP.CMS.innerExerciseView(a)}});var ViewExerciseModuleCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.selectedExercise=null;BP.CMS.prepareMainContent("practice module",function(){BP.PracticeDelegate.viewPracticeModule(a)})},onResult:function(a){BP.pushState({module:"practice"},"Practice - Babelium Project","?module=practice");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error loading practice module")}});var WatchResponseCommand=Cairngorm.Command.extend({execute:function(){BP.selectedExercise!=null&&BP.EM.watchResponse()}});var ViewEvaluationModuleCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("evaluation module",function(){BP.EvaluationDelegate.viewEvaluationModule(a)})},onResult:function(a){BP.pushState({module:"evaluation"},"Evaluation - Babelium Project","?module=evaluation");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error loading evaluation module")}});var ViewSubtitleModuleCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("subtitle module",function(){BP.SubtitleDelegate.viewSubtitleModule(a)})},onResult:function(a){BP.pushState({module:"subtitles"},"Subtitles - Babelium Project","?module=subtitles");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error loading subtitle module")}});var ViewConfigModuleCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("config module",function(){BP.ConfigDelegate.viewConfigModule(a)})},onResult:function(a){BP.pushState({module:"config"},"Configuration - Babelium Project","?module=config");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error loading config module")}});var ViewAboutModuleCommand=Cairngorm.Command.extend({execute:function(){var a=this;BP.CMS.prepareMainContent("about",function(){BP.AboutDelegate.viewAboutModule(a)})},onResult:function(a){BP.pushState({module:"about"},"About - Babelium Project","?module=about");BP.CMS.innerMainContent(a)},onFault:function(){alert("Error loading about module")}});var ProcessLoginCommand=Cairngorm.Command.extend({execute:function(){this.data!=null&&($("li#loginhelper").html("<img src='themes/babelium/images/loading.gif' alt='Loading..' width='16' height='16' />"),BP.AuthDelegate.processLogin(this,this.data))},onResult:function(a){a=$.parseJSON(a);a.content.indexOf("<li>")!=-1?($("li#loginhelper").html(""),$("ul#usernav").html(a.content),BP.CMS.hideLoginPopup(),BP.at("home")?($("aside#motd").fadeOut(500,function(){$(this).remove()}),(new ViewChangeEvent(ViewChangeEvent.VIEW_HOME_MODULE,
true)).dispatch()):(new ViewChangeEvent(ViewChangeEvent.VIEW_POPSTATE,BP.state)).dispatch()):$("li#loginhelper").html(a.content)},onFault:function(){alert("Error trying to connect to the login server")}});var SignOutCommand=Cairngorm.Command.extend({execute:function(){BP.AuthDelegate.signOut(this)},onResult:function(a){a=$.parseJSON(a);a.content.indexOf("<li>")!=-1&&($("li#loginhelper").html(""),$("ul#usernav").html(a.content),BP.at("home")?($("aside#motd").fadeOut(500,function(){$(this).remove()}),(new ViewChangeEvent(ViewChangeEvent.VIEW_HOME_MODULE,true)).dispatch()):(new ViewChangeEvent(ViewChangeEvent.VIEW_POPSTATE,BP.state)).dispatch())},onFault:function(){alert("Error trying to connect to the login server")}});function onPlaybackCuePoint(a,c){this.VP=c;this.cue=a;this.execute=function(){this.cue?this.VP.setSubtitle(this.cue.text,this.cue.textColor):this.VP.setSubtitle("",0)}};function onRecordingOtherRoleCuePoint(a,c){this.VP=c;this.cue=a;this.execute=function(){var a=this.cue.endTime-this.cue.startTime;this.VP.setSubtitle(this.cue.text,this.cue.textColor);this.VP.startTalking(this.cue.role,a);this.VP.highlight(false)}};function onRecordingSelectedRoleStartCuePoint(a,c){this.VP=c;this.cue=a;this.execute=function(){var a=this.cue.endTime-this.cue.startTime;this.VP.muteVideo(true);this.VP.muteRecording(false);this.VP.setSubtitle(this.cue.text,this.cue.textColor);this.VP.startTalking(this.cue.role,a);this.VP.highlight(true)}};function onRecordingSelectedRoleStopCuePoint(a){this.VP=a;this.execute=function(){this.VP.muteRecording(true);this.VP.muteVideo(false);this.VP.setSubtitle("",0);this.VP.highlight(false)}};function onReplayRecordingCuePoint(a,c){this.VP=c;this.cue=a;this.execute=function(){this.cue?(this.VP.setSubtitle(this.cue.text,this.cue.textColor),this.VP.startTalking(this.cue.role,this.cue.endTime-this.cue.startTime)):this.VP.setSubtitle("",0)}};function cueObject(a,c,b,d,k,j,l,i,n){this.defaultParamValues=[0,-1,-1,null,0,null,null,null,16777215];this.subtitleId=a;this.startTime=c;this.endTime=b;this.text=d;this.roleId=k;this.role=j;this.startCommand=l;this.endCommand=i;this.textColor=n==null?defaultParamValues[9]:n;this.executeStartCommand=function(){this.startCommand.execute()};this.executeEndCommand=function(){this.endCommand.execute()};this.setStartCommand=function(a){this.startCommand=a};this.setEndCommand=function(a){this.endCommand=
a}};function cuePointManager(){this.cpm_cuelist=[];this.cpm_subtitleId=this.cpm_exerciseId=-1;this.roleColors=[16777215,16776482,6945792,16609792,355577,16715531,12784841,16739301];this.colorDictionary=[];var a=this;this.reset=function(){cpm_subtitleId=cpm_exerciseId=-1;cpm_cuelist=[]};this.setVideo=function(a){this.cpm_exerciseId=a};this.currentSubtitle=function(){return this.cpm_subtitleId};this.addCue=function(a){this.cpm_cuelist.push(a);this.cpm_cuelist.sort(this.sortByStartTime)};this.setCueAt=function(a,
b){this.cpm_cuelist.setItemAt(a,b)};this.getCueAt=function(a){return this.cpm_cuelist[a]};this.removeCueAt=function(a){return this.cpm_cuelist.removeItemAt(a)};this.getCueIndex=function(a){return this.cpm_cuelist.getItemIndex(a)};this.removeAllCue=function(){this.cpm_cuelist=[]};this.setCueList=function(a){this.cpm_cuelist=a};this.getCuelist=function(){return this.cpm_cuelist};this.sortByStartTime=function(a,b){return a.startTime>b.startTime?1:a.startTime<b.startTime?-1:0};this.sortByEndTime=function(a,
b){return a.endTime>b.endTime?1:a.endTime<b.endTime?-1:0};this.setCueListStartCommand=function(a){for(var b in this.cpm_cuelist)this.cpm_cuelist[b].setStartCommand(a)};this.setCueListEndCommand=function(a){for(var b in this.cpm_cuelist)this.cpm_cuelist[b].setEndCommand(a)};this.monitorCuePoints=function(a){for(var b in this.cpm_cuelist){if(a-0.08<this.cpm_cuelist[b].startTime&&this.cpm_cuelist[b].startTime<a+0.08){this.cpm_cuelist[b].executeStartCommand();break}if(a-0.08<this.cpm_cuelist[b].endTime&&
this.cpm_cuelist[b].endTime<a+0.08){this.cpm_cuelist[b].executeEndCommand();break}}};this.setCuesFromSubtitleUsingLocale=function(c){BP.Services.send(false,"getSubtitleLines",{id:0,exerciseId:this.cpm_exerciseId,language:c},a.subtitlesRetrievedCallback)};this.setCuesFromSubtitleUsingId=function(){};this.subtitlesRetrievedCallback=function(c){c=c.response;a.colorDictionary=[];for(var b in c)typeof c[b]=="object"&&a.addCueFromSubtitleLine(c[b]);for(b in c){a.cpm_subtitleId=c[b].subtitleId;break}a.subtitlesRetrievedListener()};
this.addCueFromSubtitleLine=function(a){for(var b=false,d=this.roleColors[0],k=0;k<this.colorDictionary.length;k++)if(this.colorDictionary[k]==a.exerciseRoleId){b=true;d=this.roleColors[k];break}b||(this.colorDictionary.push(a.exerciseRoleId),d=this.roleColors[this.colorDictionary.length-1]);this.addCue(new cueObject(a.subtitleId,a.showTime,a.hideTime,a.text,a.exerciseRoleId,a.exerciseRoleName,null,null,d))};this.cues2rolearray=function(){var a=[],b=this.getCuelist(),d;for(d in b)a.push({startTime:b[d].startTime,
endTime:b[d].endTime,role:b[d].role});return a};this.addEventListener=function(a,b){switch(a){case "onSubtitlesRetrieved":if(typeof b=="function")this.subtitlesRetrievedListener=b}}};function services(){this.protocol="http://";this.host="babelium/html5/api/";this.endpoint="rest.php";this.lastRandomizer="";this.statToken="myMusicFightsAgainstTheSystemThatTeachesToLiveAndDie";this.token=this.authToken=this.commToken="";var a=this;this.send=function(c,b,d,k){var c=(c?"https://":"http://")+this.host+this.endpoint+"?"+b,j={};j.method=b;if(d!=null)j.parameters=d;if(k==null)k=a.onServiceSuccess;this.token=this.generateToken(b);j.header={token:this.token,session:BP.getSessionID,uuid:BP.getUUID};
$.support.cors=true;$.ajax({type:"POST",url:c,data:j,success:k,error:function(b,c,d){a.onServiceError(b,c,d)},xhrFields:{withCredentials:true},crossDomain:true})};this.getCommunicationToken=function(){var c=this.protocol+this.host+this.endpoint+"?getCommunicationToken",b={method:"getCommunicationToken"};b.parameters={secretKey:MD5(BP.getSessionID)};b.header={session:BP.getSessionID,uuid:BP.getUUID};$.post(c,b,BP.Services.onCommunicationTokenSuccess,"json").error(function(b){a.onServiceError(b)})};
this.onCommunicationTokenSuccess=function(c){a.commToken=c.response;BP.onCommunicationReady()};this.onServiceSuccess=function(){};this.onServiceError=function(a){jQuery.parseJSON(a.responseText);console.log("Request error: ".errorObj.response.message)};this.createRandomSalt=function(){for(var a="",b=0;b<6;)a+=Math.floor(Math.random()*16).toString(16),b++;return a!==this.lastRandomizer?a:createRandomSalt()};this.generateToken=function(a){var b=this.createRandomSalt(),a=Sha1.hash(a+":"+this.commToken+
":"+this.statToken+":"+b);return b+a}};BP.HomeDelegate=function(){return{viewHomeModule:function(a,c){var b=Cairngorm.ServiceLocator.getHttpService("homeMOD"),d=!c&&BP.at("home")?"state=min":null;b.call(d,a)},latestAvailableVideos:function(a){Cairngorm.ServiceLocator.getHttpService("homeMOD").call("action=latest&state=min",a)},topScoreMostViewedVideos:function(a){Cairngorm.ServiceLocator.getHttpService("homeMOD").call("action=rated&state=min",a)},latestUserActivity:function(a){Cairngorm.ServiceLocator.getHttpService("homeMOD").call("action=activity&state=min",
a)}}}();BP.PracticeDelegate=function(){return{viewPracticeModule:function(a){Cairngorm.ServiceLocator.getHttpService("pracMOD").call(null,a)},getRecordableExercises:function(a){Cairngorm.ServiceLocator.getHttpService("pracMOD").call(null,a)},viewExerciseByName:function(a,c){c&&Cairngorm.ServiceLocator.getHttpService("pracMOD").call("action=view&state=min&params="+c,a)}}}();BP.EvaluationDelegate=function(){return{viewEvaluationModule:function(a){Cairngorm.ServiceLocator.getHttpService("evalMOD").call(null,a)}}}();BP.SubtitleDelegate=function(){return{viewSubtitleModule:function(a){Cairngorm.ServiceLocator.getHttpService("subsMOD").call(null,a)}}}();BP.ConfigDelegate=function(){return{viewConfigModule:function(a){Cairngorm.ServiceLocator.getHttpService("confMOD").call(null,a)}}}();BP.AboutDelegate=function(){return{viewAboutModule:function(a){Cairngorm.ServiceLocator.getHttpService("aboutMOD").call(null,a)}}}();BP.UserDelegate=function(){return{viewUserModule:function(){}}}();BP.AuthDelegate=function(){return{processLogin:function(a,c){if(c&&typeof c.toBase64=="function"){var b=Cairngorm.ServiceLocator.getHttpService("authMOD"),d="action=login&params="+c.toBase64();b.call(d,a)}},signOut:function(a){Cairngorm.ServiceLocator.getHttpService("authMOD").call("action=logout",a)}}}();var LoginVO=Cairngorm.VO.extend({init:function(a,c,b){this.name=a;this.pass=c;this.remember=b?1:0}});var ExerciseVO=Cairngorm.VO.extend({init:function(a,c,b){this.id=a;this.name=c;this.title=b}});BP.control=new Controller;BP.state={};BP.selectedExercise=null;BP.bpPlayer=null;BP.pushState=function(a,c,b){window.history.pushState(a,c,b);BP.state=a};BP.at=function(a){return typeof a=="undefined"?typeof BP.state.module=="undefined"?null:BP.state.module:typeof BP.state.module=="undefined"?false:BP.state.module==a};BP.action=function(a){return typeof a=="undefined"?typeof BP.state.action=="undefined"?null:BP.state.action:typeof BP.state.action=="undefined"?false:BP.state.action==a};
BP.params=function(a){return typeof actionName=="undefined"?typeof BP.state.params=="undefined"?null:BP.state.params:typeof BP.state.params=="undefined"?false:BP.state.params==a};BP.getUUID=function(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(a){var c=Math.random()*16|0;return(a=="x"?c:c&3|8).toString(16)}).toUpperCase()}();BP.getSessionID=function(){var a=document.cookie.match("(^|;) ?PHPSESSID=([^;]*)(;|$)");return a?unescape(a[2]):null}();BP.Services=new services;BP.Services.getCommunicationToken();
BP.onCommunicationReady=function(){BP.EM=new ExerciseManager};$.get("themes/babelium/js/services.xml",null,function(a){var c={};$(a).find("gateway").each(function(){var a=$(this);a.attr("type")=="http"&&(c[a.attr("id")]={target:a.attr("target"),method:a.attr("method")})});$(a).find("service").each(function(){var a=$(this),d=c[a.attr("destination")];d!=null&&(d=new Cairngorm.HTTPService(d,a.attr("class")),Cairngorm.ServiceLocator.registerHttpService(a.attr("id"),d))})});
$(document).ready(function(){var a=(/module=(.+?)(&|$)/.exec(location.search)||[,"home"])[1],c=(/action=(.+?)(&|$)/.exec(location.search)||[,void 0])[1],b=(/params=(.+?)(&|$)/.exec(location.search)||[,void 0])[1];BP.pushState({module:a,action:c,params:b},null,null);BP.CMS.init();window.onpopstate=function(a){if(!(typeof a.state=="undefined"||a.state==null)){a=a.state;if(a.module=="home"&&BP.at("home"))a.params="min";(new ViewChangeEvent(ViewChangeEvent.VIEW_POPSTATE,a)).dispatch()}}});
function onConnectionReady(a){var c=null;(c=navigator.appName.indexOf("Microsoft")!=-1?window[a]:document[a])?(BP.bpPlayer=c,BP.selectedExercise?BP.EM.loadExercise(c,BP.selectedExercise):BP.params()!=null&&BP.EM.loadExerciseFromContent(c)):alert("There was a problem while loading the video player.")};
