            </div>
        </div>
    </div>
    <div class="hidden-left-links">
        <div class="sidebar-mobile-toggler text-center">
            <a href="#" class="sidebar-mobile-main-toggle-left">
                <i class="icon-indent-increase"></i>
            </a>
            <span class="w-100 text-left"><?php echo Lang::line('Navigation') ?></span>
        </div>
        <ul class="nav nav-sidebar"></ul>
    </div>
</div>
<?php
if (Config::getFromCache('USE_CHAT') && Ue::isUseChat() && Config::getFromCache('isUseWebSocket')) {
    require_once 'chat/client/main.php';
} 
?>

<script type="text/javascript">
Core.initHeaderTabFix(); 
var timerUid = '<?php echo Ue::sessionUserId(); ?>', timerUKid = '<?php echo Ue::sessionUserKeyId(); ?>'; 
<?php 
if (defined('CONFIG_CHECK_UPDATE') && CONFIG_CHECK_UPDATE) {
?>
var isAppUpdating=false,isTimerAppUpdating=false,isTimerAppWaiting=false,isTimerSessionExpired=false,timerSeconds=10000,timerUKDiffAction='<?php echo strtolower(Config::getFromCache('userKeyDiffAction')); ?>',appUpdatingTimer,onlyAdminMessage,cd97d6s8dg7sed4='<?php if (Session::unitName()) { echo Crypt::encrypt(Session::get(SESSION_PREFIX . 'sdbid'), 'db00x'); } ?>';var checkUpdate=function(){$.ajax({type:'get',url:'api/notify',data:{i:timerUid,k:timerUKid},cache:false,dataType:'json',success:function(data){if(data.status=='1'){$('body').removeClass('body-select-none');if(!isTimerAppWaiting){PNotify.removeAll();new PNotify({title:'Info',text:'<span class="notify-update-msg">'+data.message+'</span>',type:'info',sticker:false,hide:false,delay:1800000,history:{history:false}});isTimerAppWaiting=true;countDownCheckUpdate(data.minute)}isAppUpdating=false}else if(data.status=='2'){if(!isAppUpdating){$('body').addClass('body-select-none');PNotify.removeAll();new PNotify({title:'Notice',text:data.message,type:'notice',hide:false,delay:1800000,history:{history:false},addclass:"stack-modal",stack:{"dir1":"down","dir2":"right","modal":true,"overlay_close":false},buttons:{closer:false,closer_hover:false,sticker:false,sticker_hover:false}})}timerSeconds=30000;isAppUpdating=true}else if(data.status=='4'){$('body').removeClass('body-select-none');PNotify.removeAll();new PNotify({title:'Success',text:'<span class="notify-update-msg">'+data.message+'</span>',type:'success',delay:1800000,sticker:false});AutoReLoad.init();isAppUpdating=false}else if(data.status=='5'){if(!isAppUpdating){$('body').removeClass('body-select-none');PNotify.removeAll();new PNotify({title:'Warning',text:'<span class="notify-admin-msg">'+data.message+'</span>',type:'warning',sticker:false,hide:true,delay:1000000000,history:{history:false}});onlyAdminMessage=data.message}else{if($('body').find('.notify-admin-msg').length==0){$('body').removeClass('body-select-none');PNotify.removeAll();new PNotify({title:'Warning',text:'<span class="notify-admin-msg">'+onlyAdminMessage+'</span>',type:'warning',sticker:false,hide:true,delay:1000000000,history:{history:false}})}}timerSeconds=30000;isAppUpdating=true}else if(data.status=='6'){if($('body').find('#dialog-session-expired').length==0){$('<div id="dialog-session-expired"></div>').appendTo('body');var $dialogLogin=$('#dialog-session-expired');$.ajax({type:'post',url:'login/sessionExpired',beforeSend:function(){Core.blockUI({message:'Loading...',boxed:true})},success:function(dataHtml){$dialogLogin.empty().append(dataHtml);$dialogLogin.dialog({cache:false,resizable:false,draggable:false,bgiframe:true,autoOpen:false,title:'',width:500,minWidth:500,height:'auto',modal:true,dialogClass:'session-expired-dialog',closeOnEscape:false,open:function(){disableScrolling();$dialogLogin.dialog('widget').next('.ui-widget-overlay').addClass('session-expired-overlay')},close:function(){enableScrolling();$dialogLogin.empty().dialog('destroy').remove()}});$dialogLogin.dialog('open');Core.unblockUI()}});isTimerSessionExpired=true;if(typeof mgChat!=='undefined'){rtc.send({type:'logout',data:{}})}}}else{$('body').removeClass('body-select-none');var varAutoReLoad=false;if($('#nf-last-update-msg').length){varAutoReLoad=true}if($('.notify-update-msg').length||$('#nf-last-update-msg').length){PNotify.removeAll()}if(varAutoReLoad){AutoReLoad.init();varAutoReLoad=false}isAppUpdating=false;if(isTimerSessionExpired&&$('#dialog-session-expired').length){$('#dialog-session-expired').dialog('close')}if(data.status=='7'&&$('body').find('.notify-userkeynotsame-msg').length==0){if(timerUKDiffAction=='appmenuredirect'){window.location.href='appmenu/redirectDefaultUrl'}else{new PNotify({title:'Warning',text:'<span class="notify-userkeynotsame-msg">'+plang.get('uk_not_same_msg')+'</span>',type:'warning',addclass:pnotifyPosition,sticker:false,hide:true,delay:1000000000,history:{history:false}})}}if(data.hasOwnProperty('newnotification')){var newNotificationCount=Number(data.newnotification);var $hdrNotifElem=$('a.hdr-open-notification-list');$hdrNotifElem.find('.badge').remove();if(newNotificationCount>0){$hdrNotifElem.append('<span class="badge badge-warning">'+newNotificationCount+'</span>')}}isTimerSessionExpired=false;if(typeof mgChat!=='undefined'){if(!wsChatStatus){wsChatStatus='online'}else{if(data.hasOwnProperty('idle')){if(wsChatStatus=='online'){wsChatStatus='idle';rtc.send({type:'chatstatus',data:{'status':'idle'}})}}else if(wsChatStatus=='idle'){wsChatStatus='online';rtc.send({type:'chatstatus',data:{'status':'online'}})}}}}}});clearConsole();setTimeout(checkUpdate,timerSeconds)};setTimeout(function(){checkUpdate()},100);function countDownCheckUpdate(minutes){var seconds=60;var mins=minutes;if(!isTimerAppUpdating){function tickCheckUpdate(){var counter=$('#checkupdate-timer');var current_minutes=mins-1;seconds--;if(counter.length){counter.html(current_minutes.toString()+':'+(seconds<10?'0':'')+String(seconds))}if(seconds==0&&mins==1){clearTimeout(appUpdatingTimer);if(!isTimerAppUpdating){$('body').addClass('body-select-none');PNotify.removeAll();new PNotify({title:'Notice',text:'<span id="nf-last-update-msg">Систем дээр шинэчлэлт хийгдэж байна та түр хүлээнэ үү.</span>',type:'notice',hide:false,delay:1800000,history:{history:false},addclass:"stack-modal",stack:{"dir1":"down","dir2":"right","modal":true,"overlay_close":false},buttons:{closer:false,closer_hover:false,sticker:false,sticker_hover:false}});isTimerAppUpdating=true}}else if(seconds>0){if(!isTimerAppUpdating){appUpdatingTimer=setTimeout(tickCheckUpdate,1000)}}else{if(mins>1&&!isTimerAppUpdating){appUpdatingTimer=setTimeout(function(){countDownCheckUpdate(mins-1)},1000)}}}tickCheckUpdate()}}
<?php
}       
?>    
$(document).ajaxSuccess(function(e,s,o){if(void 0!==s.responseJSON){var i=s.responseJSON;if(i&&(i.hasOwnProperty("message")&&"session_unregistred"==i.message||i.hasOwnProperty("sessionCase"))&&0==$("body").find("#dialog-session-expired").length){PNotify.removeAll(),$('<div id="dialog-session-expired"></div>').appendTo("body");var n=$("#dialog-session-expired");$.ajax({type:"post",url:"login/sessionExpired",data:{sessionDestroy:1},beforeSend:function(){Core.blockUI({message:"Loading...",boxed:!0})},success:function(e){n.empty().append(e),n.dialog({cache:!1,resizable:!1,draggable:!1,bgiframe:!0,autoOpen:!1,title:"",width:500,minWidth:500,height:"auto",modal:!0,dialogClass:"session-expired-dialog",closeOnEscape:!1,open:function(){disableScrolling(),n.dialog("widget").next(".ui-widget-overlay").addClass("session-expired-overlay")},close:function(){enableScrolling(),n.empty().dialog("destroy").remove()}}),n.dialog("open"),Core.unblockUI()}}),isTimerSessionExpired=!0,"undefined"!=typeof mgChat&&rtc.send({type:"logout",data:{}})}}});
$(document).ready(function () {
    $(this).mousemove(function (e) {
        if (typeof pageTitleNotification != 'undefined' && typeof pageTitleNotificationTitleChanged !== 'undefined') {
            pageTitleNotification.off();
            delete pageTitleNotificationTitleChanged;
        }
    });
    $(this).keypress(function (e) {
        if (typeof pageTitleNotification != 'undefined' && typeof pageTitleNotificationTitleChanged !== 'undefined') {
            pageTitleNotification.off();
            delete pageTitleNotificationTitleChanged;
        }
    });
    
    var isOpenMenuId = false;
    var openMenuId = '<?php $openMenuId = Input::get('openmenuid', issetParam($GLOBALS['openMenuId'])); echo ($openMenuId && is_numeric($openMenuId)) ? $openMenuId : ''; ?>';
    
    if (openMenuId) {
        var $menuElement = $('a[data-meta-data-id="'+openMenuId+'"]');
        if ($menuElement.length) {
            isOpenMenuId = true;
            $menuElement.click();
        }
    } 
    
    if (!isOpenMenuId) {
        
        var $pageContentWrapperText = $('.pf-header-main-content').clone().find('script').remove().end().text();
    
        if ($pageContentWrapperText.trim() == '') {
            var $sidebarMenu = $('.page-topbar > ul.navbar-nav > li:not(.not-module-menu):eq(0) > a[onclick*="\'layout\'"], .page-topbar > ul.navbar-nav > li:not(.not-module-menu):eq(0) > a[onclick*="\'dashboard\'"], .page-topbar > ul.navbar-nav > li:not(.not-module-menu):eq(0) > a[onclick*="\'kpi\'"], .page-topbar > ul.navbar-nav > li:not(.not-module-menu):eq(0) > a[onclick*="mdform/indicatorProcessWidget"]');
            if ($sidebarMenu.length) {
                $sidebarMenu.click();
            } else {
                $sidebarMenu = $('.page-topbar > ul.navbar-nav a[data-default-open="true"]:eq(0)');
                if ($sidebarMenu.length) {
                    $sidebarMenu.click();
                }
            }
        }
    }
    
    <?php
    if (Config::getFromCache('is_dev')) {
    ?>
    $.contextMenu({
        selector: '[data-pfgotometa="1"]',
        callback: function(key, opt) {
            var id = null;
            
            if (opt.$trigger.hasAttr('data-moduleid')) {
                id = opt.$trigger.attr('data-moduleid');
            } else if (opt.$trigger.hasAttr('data-meta-data-id')) {
                id = opt.$trigger.attr('data-meta-data-id');
            } else if (opt.$trigger.hasAttr('data-metadataid')) {
                id = opt.$trigger.attr('data-metadataid');
            } else if (opt.$trigger.hasAttr('data-meta-id')) {
                id = opt.$trigger.attr('data-meta-id');
            } else if (opt.$trigger.hasAttr('data-menu-id')) {
                id = opt.$trigger.attr('data-menu-id');
            }
            
            if (id) {
                if (key === 'gotoEditMeta') {
                    window.open('mdmetadata/gotoEditMeta/' + id, '_blank');
                } else if (key === 'gotoFolder') {
                    window.open('mdmetadata/gotoFolder/' + id, '_blank');
                } else if (key === 'idcopy') {
                    var $copyElement = $('<input>');
                    $('body').append($copyElement);
                    $copyElement.val(id).select();
                    document.execCommand('copy');
                    $copyElement.remove();
                }
            }
        },
        items: {
            "gotoEditMeta": {name: plang.get('edit_btn'), icon: 'edit'}, 
            "gotoFolder": {name: 'Фолдер руу очих', icon: 'folder'}, 
            "idcopy": {name: plang.get('setting_copy_id'), icon: 'copy'}
        }
    }); 
    <?php
    }
    ?>
            
    function clickQuickMenuByHotKey(e){var n=$("body").find('.page-actions [data-qm-hotkey="'+e+'"]');n.length&&n.click()}$(document).bind("keydown","F4",function(e){return clickQuickMenuByHotKey("F4"),e.preventDefault(),!1}),$(document.body).on("keydown","input, select, textarea, a, button","F4",function(e){return clickQuickMenuByHotKey("F4"),e.preventDefault(),!1}),$(document).bind("keydown","F6",function(e){return clickQuickMenuByHotKey("F6"),e.preventDefault(),!1}),$(document.body).on("keydown","input, select, textarea, a, button","F6",function(e){return clickQuickMenuByHotKey("F6"),e.preventDefault(),!1}),$(document).bind("keydown","F7",function(e){return clickQuickMenuByHotKey("F7"),e.preventDefault(),!1}),$(document.body).on("keydown","input, select, textarea, a, button","F7",function(e){return clickQuickMenuByHotKey("F7"),e.preventDefault(),!1}),$(document).bind("keydown","F8",function(e){return clickQuickMenuByHotKey("F8"),e.preventDefault(),!1}),$(document.body).on("keydown","input, select, textarea, a, button","F8",function(e){return clickQuickMenuByHotKey("F8"),e.preventDefault(),!1}),$(document).bind("keydown","F9",function(e){return clickQuickMenuByHotKey("F9"),e.preventDefault(),!1}),$(document.body).on("keydown","input, select, textarea, a, button","F9",function(e){return clickQuickMenuByHotKey("F9"),e.preventDefault(),!1}),$(document).bind("keydown","F10",function(e){return clickQuickMenuByHotKey("F10"),e.preventDefault(),!1}),$(document.body).on("keydown","input, select, textarea, a, button","F10",function(e){return clickQuickMenuByHotKey("F10"),e.preventDefault(),!1});
});

<?php if (Config::getFromCache('iscontentVideo') == '1' && Session::get(SESSION_PREFIX.'startupContent') == '1' && isset($this->contentPath) && file_exists($this->contentPath) ) { ?>
    
    function renderContent () {
        
        var iconUniqId = Core.getUniqueID('icon_');
        var $html = "<iframe src='<?php echo $this->contentPath; ?>' width='100%' height='"+ ($(window).height()-150) +"px' frameborder='0'></iframe>";
        
        var $modalId = 'modal-content' + iconUniqId;
        $('<div id="' + $modalId  +'" class="modal fade" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-full">' +
                    '<div class="modal-content modalcontent'+ iconUniqId +'">' +
                        '<div class="modal-header">' +
                            '<h5 class="modal-title">'+ plang.get('introduction_title') +'</h5>' +
                            '<button type="button" class="close" data-dismiss="modal">×</button>' +
                        '</div>' +
                        '<div class="modal-body">' + $html + '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-light" data-dismiss="modal">Хаах</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>').appendTo('body');
        var $dialog = $('#' + $modalId);
        $dialog.modal({
            
        });
        
        $('iframe').load( function() {
            $('iframe').contents().find("head")
            .append($("<style type='text/css'>  img {width:100%; height: 100%;}   </style>"));
        });
        $dialog.on('hidden.bs.modal', function () {
            $dialog.remove();
            enableScrolling();
            
            $.ajax({
                type: 'post',
                url: 'appmenu/saveStartupMeta',
                dataType: 'json',
                beforeSend: function () {},
                success: function (data) {},
                error: function () {}
            });
        });

        $dialog.modal('show');
        Core.initAjax($dialog);

    }
    renderContent();
    
<?php 
    Session::set(SESSION_PREFIX.'isViewed', '1');
    Session::set(SESSION_PREFIX.'startupContent', '0');
}  
if (Config::getFromCache('isVersionCheck') === '1') { ?>
    navigator.sayswho= (function(){
        var ua= navigator.userAgent, tem, 
        M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if(/trident/i.test(M[1])){
            tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
            return 'IE '+(tem[1] || '');
        }
        if(M[1]=== 'Chrome'){
            tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
            if(tem!= null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
        }
        M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
        if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
        return M.join(' ');
    })();

    if (navigator.sayswho < 90) {
        new PNotify({
            title: 'Warning',
            text: 'Интернэт хөтөчөө шинэчлэнэ үү',
            type: 'warning',
            sticker: false
        });
    }
<?php }
?>
</script>
</body>
</html>