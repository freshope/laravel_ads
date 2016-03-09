@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="report-header">
                <strong>페이스북캠페인</strong>
                <span>미리보기</span>
                <small class="help-block color-orange visible-lg visible-md">*페이스북에 광고 게시물 미리보기</small>
            </h1>
            <div class="panel">
                <h2 class="line">광고 정보</h2>
                <div class="form-group">
                    <label>웹사이트 URL<span class="color-orange">*</span></label>
                    <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                    <input class="form-control link" placeholder="" required="required" name="account_id" type="text" value="http://digitalstory.net/220642806470">
                </div>
            </div>


            <div class="form-group">
                <span class="form-control btn btn-custom orange preview_btn">미리보기</span>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-xs-7">
            <div class="desktopfeed_div">
            </div>
        </div>
        <div class="col-xs-5">
            <div class="mobilefeed_div">
            </div>
            <div class="rightcolumn_div">
            </div>
        </div>
    </div>
</div>


    <script>
        $(document).on("click",".preview_btn",function(e){
            preview_action();
        });

        function preview_action() {

            //var preview_url = "/facebook/previewaction";
            var preview_url = "/facebook/api/v2.5/act_998426013584269/generatepreviews";
            var description = "";
            var message ="";
            var name ="";
            var image_hash = "";
            var page_id="766809370116473";
            var link = $(".link").val();

            var creative={
                    "object_story_spec": {
                        "link_data": {
                            "call_to_action" : "",
                            "description": description,
                            "link": link,
                            "message": message,
                            "name": name,
                            //				"picture": picture
                            "image_hash": image_hash
                        },
                        "page_id":page_id
                    }
                };
/*
            if(call_to_action_select != "" && call_to_action_select != null && call_to_action_select != 'undefined'){
                call_to_action = {
                    "type":call_to_action_select,
                    "value":{
                        "link":link,
                        "link_caption":link_caption
                    }
                };
            }
*/
            var mobilefeed_url = preview_url+'?ad_format=MOBILE_FEED_STANDARD&creative='+JSON.stringify(creative);
            var desktopfeed_url = preview_url+'?ad_format=DESKTOP_FEED_STANDARD&creative='+JSON.stringify(creative);
            var rightcolumn_url = preview_url+'?ad_format=RIGHT_COLUMN_STANDARD&creative='+JSON.stringify(creative);

            //var mobilefeed_url = preview_url+'?creative='+JSON.stringify(creative) ;
            //var mobilefeed_url = preview_url+'?link='+link+'&page_id='+page_id;

            $.getJSON(mobilefeed_url , function(opts_mobilefeed){
                //console.log(opts_mobilefeed);

                $(".mobilefeed_div").empty();
                iframe_src_mob = opts_mobilefeed.data[0]['body'].replace('scrolling="yes"','scrolling="no"').replace('height="450"','height="375"').replace('width="335"','width="322"').replace('border: none;','border: 1px solid;');
                $(".mobilefeed_div").html(iframe_src_mob);


            }).error(function(jqXHR, textStatus) { alert("FaceBook API Error \n - "+textStatus +" :\n"+jqXHR.responseText); });

            $.getJSON(desktopfeed_url , function(opts_desktopfeed){
                //console.log(opts_mobilefeed);

                $(".desktopfeed_div").empty();
                iframe_src_mob = opts_desktopfeed.data[0]['body'].replace('scrolling="yes"','scrolling="no"').replace('height="450"','height="472"');
                $(".desktopfeed_div").html(iframe_src_mob);

            }).error(function(jqXHR, textStatus) { alert("FaceBook API Error \n - "+textStatus +" :\n"+jqXHR.responseText); });

            $.getJSON(rightcolumn_url , function(opts_rightcolumn){
                //console.log(opts_mobilefeed);

                $(".rightcolumn_div").empty();
                iframe_src_mob = opts_rightcolumn.data[0]['body'].replace('scrolling="yes"','scrolling="no"').replace('height="213"','height="230"').replace('border: none;','border: 1px solid;');
                $(".rightcolumn_div").html(iframe_src_mob);

            }).error(function(jqXHR, textStatus) { alert("FaceBook API Error \n - "+textStatus +" :\n"+jqXHR.responseText); });
        }
    </script>

@endsection
