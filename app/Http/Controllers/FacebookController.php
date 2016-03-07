<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookAuthenticationException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Http\Exception\AuthorizationException;
use FacebookAds\Http\Exception\RequestException;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\AdImage;
use FacebookAds\Object\ObjectStorySpec;
use FacebookAds\Object\TargetingSpecs;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdImageFields;
use FacebookAds\Object\Fields\ObjectStorySpecFields;
use FacebookAds\Object\Fields\TargetingSpecsFields;
use FacebookAds\Object\Fields\ObjectStory\LinkDataFields;
use FacebookAds\Object\ObjectStory\LinkData;
use FacebookAds\Object\Values\AdObjectives;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdPreviewFields;
use DateTime;

use FacebookAds\Object\Values\AdFormats;


class FacebookController extends Controller
{
    
    /**
     * Construct FacebookController
     *
     * @return null
     */
    public function __construct()
    {
        // init Facebook SDK
        Api::init(
            config('facebook.app_id'),
            config('facebook.app_secret'),
            config('facebook.access_token')
        );

        // set logger for Facebook SDK
        /*
        $ymd = date('Ymd');
        $file = APPPATH . "logs/facebook.{$ymd}.log";
        $logger = new CurlLogger(fopen($file, 'a'));
        Api::instance()->setLogger($logger);
        */
    }

    /**
     * Call facebook API bridge
     *
     * @return json
     */
    public function bridgeApi(Request $request, $version)
    {
        $segments = $request->segments();
        $method = implode('/', array_slice($segments, 3));
        $query = $request->all();
        
        $app_id = config('facebook.app_id');
        $app_secret = config('facebook.app_secret');
        $access_token = config('facebook.access_token');

        $fb = new Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_access_token' => $access_token,
            'default_graph_version' => $version
        ]);

        try {
            $response = $fb->sendRequest('GET', $method, $query);
        } catch(FacebookAuthenticationException $e) {
            return response()->json(json_decode($e->getResponse()->getBody()));
        } catch(FacebookResponseException $e) {
            return response()->json(json_decode($e->getResponse()->getBody()));
        } catch(FacebookSDKException $e) {
            return response()->json(json_decode($e->getResponse()->getBody()));
        }

        return response()->json(json_decode($response->getBody()));
    }

    /**
     * Show input facebook form
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('facebook.index');
    }

    /**
     * Show input facebook form
     *
     * @return \Illuminate\Http\Response
     */
    public function getPreview()
    {
        return view('facebook.preview');
    }

    /**
     * Show input facebook form
     *
     * @return \Illuminate\Http\Response
     */
    public function getPreviewaction(Request $request)
    {

        //$link_data = new LinkData();
        //$link_data->{LinkDataFields::LINK} = $request->input('link');

        //$link_data->{LinkDataFields::MESSAGE} = "";
        //$link_data->{LinkDataFields::CAPTION} = "";
        //$link_data->{LinkDataFields::IMAGE_HASH} = $adimage->hash;
        //$link_data->{LinkDataFields::IMAGE_HASH} = '707665f1f6d86f951450dfbcce013d56';
        //$link_data->{LinkDataFields::IMAGE_HASH} = "";

        $link_data = new LinkData();
        $link_data->setData(array(
            LinkDataFields::LINK => $request->input('link'),
            //LinkDataFields::PICTURE => '<IMAGE_URL>',
            LinkDataFields::MESSAGE => 'Message',
            LinkDataFields::NAME => 'Name',
            LinkDataFields::DESCRIPTION => 'Description',
            /*
            LinkDataFields::CALL_TO_ACTION => array(
                'type' => CallToActionTypes::USE_APP,
                'value' => array(
                    'link' => '<URL>',
                    'link_caption' => 'CTA Caption',
                ),
            ),
            */
        ));
/*
        $object_story_spec = new ObjectStorySpec();
        $object_story_spec->{ObjectStorySpecFields::PAGE_ID} = $request->input('page_id');
        $object_story_spec->{ObjectStorySpecFields::LINK_DATA} = $link_data;
*/
        $story = new ObjectStorySpec();
        $story->setData(array(
            ObjectStorySpecFields::PAGE_ID => $request->input('page_id'),
            ObjectStorySpecFields::LINK_DATA => $link_data,
        ));
/*
        $creative = new AdCreative();
        $creative->{AdCreativeFields::OBJECT_STORY_SPEC } = $object_story_spec;
*/
        $creative = new AdCreative();
        $creative->setData(array(
            AdCreativeFields::OBJECT_STORY_SPEC => $story,
        ));

        $account = new AdAccount('act_998426013584269');
        $result = $account->getAdPreviews(array(), array(
                AdPreviewFields::CREATIVE => $creative,
                AdPreviewFields::AD_FORMAT => AdFormats::DESKTOP_FEED_STANDARD,
        ))->getResponse()->getContent();


        //var_dump($result);
        echo json_encode($result);

    }


    /**
     * Save facebook campaign
     *
     * @param Request
     * 
     * @return json
     */
    public function postCreate(Request $request)
    {
        $result = $this->createCampaign($request);
        if( !$result->success ) {
            dd($result);
        }
        
        $result = $this->createAdset($request, $result->campaign);
        if( !$result->success ) {
            dd($result);
        }
        
        $result = $this->createAd($request, $result->adset);
        if( !$result->success ) {
            dd($result);
        }
        
        dd($result);

        //return view('facebook.index');
    }
    
    /**
     * Create facebook campaign
     *
     * @param Request
     * 
     * @return object
     */
    private function createCampaign(Request $request)
    {
        $result = ['success' => TRUE];
        
        try {
            $campaign = new Campaign(null, $request->input('account_id'));
            $campaign->{CampaignFields::NAME} = $request->input('name');
            $campaign->{CampaignFields::OBJECTIVE} = $request->input('objective', 'LINK_CLICKS');
            $campaign->{CampaignFields::BUYING_TYPE} = $request->input('buying_type', 'AUCTION');
            //$campaign->{CampaignFields::SPEND_CAP} = $this->spend_cap;
            //$campaign->{CampaignFields::ADLABELS} = $this->adlabels;
            $campaign->create([
                Campaign::STATUS_PARAM_NAME => Campaign::STATUS_PAUSED,
                //'execution_options' => ['validate_only']
            ]);
        } catch(AuthorizationException $e) {
            $result['success'] = FALSE;
            $result['error'] = [
                'message' => $e->getMessage(),
                'error_message' => $e->getErrorUserMessage(),
            ];
        } catch(RequestException $e) {
            $result['success'] = FALSE;
            $result['error'] = [
                'message' => $e->getMessage(),
                'error_message' => $e->getErrorUserMessage(),
            ];
        }
        
        $result['campaign'] = $campaign;
        
        return (object) $result;
    }

    /**
     * Create facebook adset
     *
     * @param Request
     * @param Campaign
     * 
     * @return object
     */
    private function createAdset(Request $request, Campaign $campaign)
    {
        $result = ['success' => TRUE];
        
        try {
            $adset = new AdSet(null, $request->input('account_id'));
            $adset->{AdSetFields::NAME} = $request->input('adset_name');
            $adset->{AdSetFields::CAMPAIGN_ID} = $campaign->id;
            $adset->{AdSetFields::TARGETING} = $this->targeting_to_object();
            //$adset->{AdSetFields::ADSET_SCHEDULE} = $this->schedule_to_object();
            $adset->{AdSetFields::IS_AUTOBID} = $this->is_autobid_to_bool($request->input('is_autobid', 'Y'));
            if( !$adset->{AdSetFields::IS_AUTOBID} ) {
                $adset->{AdSetFields::BID_AMOUNT} = $request->input('bid_amount');
            }
            $adset->{AdSetFields::BILLING_EVENT} = $request->input('billing_event', 'LINK_CLICKS');
            $adset->{AdSetFields::OPTIMIZATION_GOAL} = $request->input('optimization_goal', 'LINK_CLICKS');
            $adset->{AdSetFields::DAILY_BUDGET} = $request->input('daily_budget');
            $adset->{AdSetFields::LIFETIME_BUDGET} = $request->input('lifetime_budget');
            //$adset->{AdSetFields::LIFETIME_IMPS} = $this->lifetime_imps;
            $adset->{AdSetFields::PACING_TYPE} = $this->pacing_type_to_array($request->input('pacing_type', 'standard'));
            //$adset->{AdSetFields::ADLABELS} = $this->adlabels;
            //$adset->{AdSetFields::CREATIVE_SEQUENCE} = $this->creative_sequence;
            //$adset->{AdSetFields::PRODUCT_AD_BEHAVIOR} = $this->product_ad_behavior;
            $adset->{AdSetFields::START_TIME} = $this->time_to_iso8601($request->input('start_time'));
            $adset->{AdSetFields::END_TIME} = $this->time_to_iso8601($request->input('end_time'));
            $adset->{AdSetFields::PROMOTED_OBJECT} = $this->promoted_object_to_array();
            $adset->create([
                AdSet::STATUS_PARAM_NAME => AdSet::STATUS_PAUSED,
                //'execution_options' => ['validate_only']
            ]);
        } catch(AuthorizationException $e) {
            $result['success'] = FALSE;
            $result['error'] = [
                'message' => $e->getMessage(),
                'error_message' => $e->getErrorUserMessage(),
            ];
        } catch(RequestException $e) {
            $result['success'] = FALSE;
            $result['error'] = [
                'message' => $e->getMessage(),
                'error_message' => $e->getErrorUserMessage(),
            ];
        }
        
        $result['adset'] = $adset;
        
        return (object) $result;
    }

    /**
     * Create facebook ad
     *
     * @param Request
     * @param AdSet
     * 
     * @return object
     */
    private function createAd(Request $request, AdSet $adset)
    {
        $adcreative_result = $this->createCreative($request);
        if( !$adcreative_result->success ) {
            dd($adcreative_result);
        }
        
        $adcreative = $adcreative_result->adcreative;
        $result = ['success' => TRUE];
        
        try {
            $ad = new Ad(null, $request->input('account_id'));
            $ad->{AdFields::NAME} = $request->input('ad_name');
            $ad->{AdFields::ADSET_ID} = $adset->id;
            $ad->{AdFields::CREATIVE} = ['creative_id' => $adcreative->id];
            //$ad->{AdFields::ADLABELS} = $this->adlabels;
            //$ad->{AdFields::BID_AMOUNT} = $this->bid_amount;
            $ad->create([
                Ad::STATUS_PARAM_NAME => Ad::STATUS_PAUSED,
                //'execution_options' => array('validate_only')
            ]);
        } catch(AuthorizationException $e) {
            $result['success'] = FALSE;
            $result['error'] = [
                'message' => $e->getMessage(),
                'error_message' => $e->getErrorUserMessage(),
            ];
        } catch(RequestException $e) {
            $result['success'] = FALSE;
            $result['error'] = [
                'message' => $e->getMessage(),
                'error_message' => $e->getErrorUserMessage(),
            ];
        }
        
        $result['ad'] = $ad;
        
        return (object) $result;
    }

    /**
     * Create facebook creative
     *
     * @param Request
     * 
     * @return object
     */
    private function createCreative(Request $request)
    {
        $result = ['success' => TRUE];
        $account_id = $request->input('account_id');
        /*
        $image_url = $request->input('image_url');
        $filename = '/tmp/'. uniqid('fb_') .'.png';
        exec("/usr/bin/wget -O {$filename} {$image_url}");
        //$file = file_get_contents($request->input('image_url'));
        //$file = base64_encode($file);
        //dd($file);
        
        $adimage = new AdImage(null, $account_id);
        $adimage->{AdImageFields::FILENAME} = $request->input('image_url');
        //$adimage->{AdImageFields::FILENAME} = $filename;
        $adimage->create();
        
        //unlink($filename);
        */

        $link_data = new LinkData();
        $link_data->{LinkDataFields::MESSAGE} = str_replace('_', ' ', $request->input('title'));
        $link_data->{LinkDataFields::CAPTION} = $request->input('description');
        $link_data->{LinkDataFields::LINK} = $request->input('link');
        //$link_data->{LinkDataFields::IMAGE_HASH} = $adimage->hash;
        //$link_data->{LinkDataFields::IMAGE_HASH} = '707665f1f6d86f951450dfbcce013d56';

        $object_story_spec = new ObjectStorySpec();
        $object_story_spec->{ObjectStorySpecFields::PAGE_ID} = $request->input('page_id');
        $object_story_spec->{ObjectStorySpecFields::LINK_DATA} = $link_data;

        try {
            $adcreative = new AdCreative(null, $account_id);
            $adcreative->{AdCreativeFields::NAME} = str_replace('_', ' ', $request->input('adcreative_name'));
            //$adcreative->{AdCreativeFields::TITLE} = str_replace('_', ' ', $object_story_spec->title);
            //$adcreative->{AdCreativeFields::BODY} = $object_story_spec->link_desc;
            //$adcreative->{AdCreativeFields::IMAGE_HASH} = $adimage->hash;
            //$adcreative->{AdCreativeFields::OBJECT_ID} = $fb_adcreative_card->page_id;
            $adcreative->{AdCreativeFields::OBJECT_STORY_SPEC} = $object_story_spec;
            $adcreative->create();
        } catch(RequestException $e) {
            $result['success'] = FALSE;
            $result['error'] = [
                'message' => $e->getMessage(),
                'error_message' => $e->getErrorUserMessage(),
            ];
        }
        
        $result['adcreative'] = $adcreative;
        
        return (object) $result;
    }

    /**
     * Convert targeting to object
     *
     * @return object
     */
    private function targeting_to_object()
    {
        $targeting = [];
        $targeting[TargetingSpecsFields::GEO_LOCATIONS] = ['countries' => ['KR']];
        $targeting[TargetingSpecsFields::AGE_MAX] = 65;
        $targeting[TargetingSpecsFields::AGE_MIN] = 13;
        $targeting[TargetingSpecsFields::GENDERS] = [1,2];
        
        return $targeting;
    }

    /**
     * Convert is_autobid to bool
     *
     * @param String
     * 
     * @return bool
     */
    private function is_autobid_to_bool($is_autobid)
    {
         return $is_autobid == 'Y' ? TRUE : FALSE;
    }

    /**
     * Convert pacing_type to array
     *
     * @param String
     * 
     * @return array
     */
    private function pacing_type_to_array($pacing_type)
    {
        return empty($pacing_type) ? [] : [$pacing_type];
    }

    /**
     * Convert time to iso8601
     *
     * @param String
     * 
     * @return DateTime
     */
    private function time_to_iso8601($time)
    {
        return (new DateTime($time))->format(DateTime::ISO8601);
    }

    /**
     * Convert promoted_object to array
     *
     * @return array
     */
    private function promoted_object_to_array()
    {
        /*
        $fb_adcampaign = $this->fb_adcampaign;
        $fb_promoted_object = $fb_adcampaign->fb_promoted_object;
        switch($fb_adcampaign->objective) {
            case AdObjectives::CONVERSIONS:
                return array(
                    'pixel_id' => $fb_promoted_object->pixel_id,
                    'custom_event_type' => $fb_promoted_object->custom_event_type,
                );
                break;
            case AdObjectives::PAGE_LIKES:
                return array(
                    'page_id' => $fb_promoted_object->page_id
                );
                break;
        }
        */

        return [];
    }
    
}
