<?php
require_once './backend/core/Result.php';
require_once './backend/domain/LlmRequester.php';
require_once './backend/domain/Listing.php';
require_once './backend/db/Database.php';
require_once './backend/util/Util.php';



class AdEvaluator
{



  public static function evaluate(Listing $ad, Rating $rating, array $images): Result
  {

    $prompt = 'I want you to be a professional c2c ad listing evaluator. You are able to identify a ad based on images and description of ad the sellers rating etc. Your main purpose is to help others evaluate if the product or service is legit. ';

    $listing = "Here is the listing details: Price: $ad->price, description: $ad->description, title: $ad->title, date posted: $ad->date_posted. ";
    $srating = "Here is the rating: $rating->rating out of 5 made by $rating->count reviews.";

    $image_prompt = "If there are images please evaluate the likelyhood the image is a real ad or an image grabbed online and whether the images match the product. ";
    $repsonse_wanted = "Keep the response short and brief, bullet pointed but as single lines no points should take less than 20 seconds to read. The client should not know you exist so no first person talk.";
    $prompt = $prompt . $listing . $srating . $image_prompt . $repsonse_wanted;
    $response = LlmRequester::prompt_with_images($prompt, $images);
    return $response;
  }
}
