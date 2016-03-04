@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="report-header">
                <strong>페이스북캠페인</strong>
                <span>만들기</span>
                <small class="help-block color-orange visible-lg visible-md">*캠페인을 진행해 상품이나 매장, 서비스를 홍보합니다.</small>
            </h1>
            {!! Form::open(['url' => 'facebook/create', 'method' => 'post']) !!}
                <input name="advertising_id" type="hidden">
                <div class="panel">
                    <h2 class="line">캠페인 정보</h2>
                    <div class="form-group">
                        <label>광고계정<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="account_id" type="text" value="act_998426013584269">
                    </div>
                    <div class="form-group">
                        <label>캠페인목표<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <select class="form-control" required="required" name="objective">
                            <option value="">선택하세요.</option>
                            <option value="LINK_CLICKS" selected="selected">웹사이트 방문 수 높이기</option>
                            <option value="CONVERSIONS">웹사이트 전화 늘리기</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>웹사이트 주소<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="홍보할 URL을 입력하세요" required="required" name="object_store_url" type="text" value="http://biz.yellostory.co.kr">
                    </div>
                    <div class="form-group">
                        <label>캠페인명<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="예) 운동화, 운동복 에어조던 이발기, 도미노피자 대치점" required="required" name="name" type="text" value="캠페인 테스트">
                    </div>
                </div>
                    
                <div class="panel">
                    <h2 class="line">AdSet 정보</h2>
                    <div class="form-group">
                        <label>AdSet 이름<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="adset_name" type="text" value="AdSet-1">
                    </div>
                    <div class="form-group">
                        <label>전체예산<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="lifetime_budget" type="text" value="35000">
                    </div>
                    <div class="form-group">
                        <label>기간<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="start_time" type="text" value="2016-03-03">
                        <input class="form-control" placeholder="" required="required" name="end_time" type="text" value="2016-03-10">
                    </div>
                </div>
                
                <div class="panel">
                    <h2 class="line">Ad 정보</h2>
                    <div class="form-group">
                        <label>Ad 이름<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="ad_name" type="text" value="Ad-1">
                    </div>
                    <div class="form-group">
                        <label>AdCreative 이름<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="adcreative_name" type="text" value="AdCreative-1">
                    </div>
                    <div class="form-group">
                        <label>Page ID<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="page_id" type="text" value="766809370116473">
                    </div>
                    <div class="form-group">
                        <label>Image URL<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="image_url" type="text" value="https://external.xx.fbcdn.net/safe_image.php?d=AQDKZUH92JrEOwaX&w=470&h=246&url=https%3A%2F%2Fstatic.withblog.net%2Fwww%2Fweb%2Fimg%2Fwb-default.png&cfs=1&upscale=1&ext=png2jpg">
                    </div>
                    <div class="form-group">
                        <label>제목<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="title" type="text" value="위드블로그">
                    </div>
                    <div class="form-group">
                        <label>설명<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="description" type="text" value="위드블로그 가입해 보세요.">
                    </div>
                    <div class="form-group">
                        <label>링크 URL<span class="color-orange">*</span></label>
                        <small class="inline-block hidden-sm hidden-xs color-ing"></small>
                        <input class="form-control" placeholder="" required="required" name="link" type="text" value="https://withblog.net/">
                    </div>
                </div>
                
                <div class="form-group">
                    <input class="form-control btn btn-custom orange" type="submit" value="캠페인 등록 요청하기">
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
