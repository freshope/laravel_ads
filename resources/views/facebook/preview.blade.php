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

            <div class="mobilefeed_div">
            </div>
        </div>
    </div>
</div>


    <script>
        $(document).on("click",".preview_btn",function(e){
            preview_action();
        });

        function preview_action() {

            var preview_url = "/facebook/previewaction";
            var description = "";
            var message ="";
            var name ="";
            var image_hash = "";
            var page_id="766809370116473";
            var link = $(".link").val();
            /*
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
            */
            //var mobilefeed_url = preview_url+'?ad_format=MOBILE_FEED_STANDARD&creative='+JSON.stringify(creative) ;
            //var mobilefeed_url = preview_url+'?creative='+JSON.stringify(creative) ;
            var mobilefeed_url = preview_url+'?link='+link+'&page_id='+page_id;

            $.getJSON(mobilefeed_url , function(opts_mobilefeed){
                //console.log(opts_mobilefeed);

                $(".mobilefeed_div").empty();
                iframe_src_mob = opts_mobilefeed.data[0]['body'].replace('width="335"','width="322"').replace('height="450"','height="395"').replace('scrolling="yes"','scrolling="no"');
                $(".mobilefeed_div").html(iframe_src_mob);

            }).error(function(jqXHR, textStatus) { alert("FaceBook API Error \n - "+textStatus +" :\n"+jqXHR.responseText); });
        }
    </script>

@endsection
