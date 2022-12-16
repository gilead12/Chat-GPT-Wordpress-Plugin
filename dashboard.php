<?php

include "header.php";
$postContent = NULL;

global $wpdb;
$sql = "SELECT * FROM wp_chatgpt_content_writer";
$results = $wpdb->get_results($sql);
$getApiToken = $results[0]->api_token;
$getTemperature = intval($results[0]->temperature);
$getMaxTokens = intval($results[0]->max_tokens);
$getLanguage = $results[0]->language;

$languages = array("tr","en");
if(in_array($getLanguage,$languages)) {
    include "language/".$getLanguage.".php";
} else {
  include "language/en.php";
}


if(isset($_POST['goTest'])){
  $TEXT = $_POST["chatGptText"];
  $header = array(
    'Authorization: Bearer '.$getApiToken,
    'Content-type: application/json; charset=utf-8',
  );
  $params = json_encode(array(
    'prompt'		=> $TEXT,
    'model'			=> 'text-davinci-003',
    'temperature'	=> $getTemperature,
    'max_tokens' => $getMaxTokens,
  ));
  $curl = curl_init('https://api.openai.com/v1/completions');
  $options = array(
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER =>$header,
      CURLOPT_POSTFIELDS => $params,
      CURLOPT_RETURNTRANSFER => true,
  );
  curl_setopt_array($curl, $options);
  $response = curl_exec($curl);
  $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
  if(200 == $httpcode){
    $json_array = json_decode($response, true);
    $choices = $json_array['choices'];
      $postContent = $choices[0]["text"];
  }
}

if(isset($_POST["addBlog"])){
  $my_post = array();
  $my_post['post_title']    = $_POST["postTitle"];
  $my_post['post_content']  = $_POST["postContent"];
  $my_post['tags_input']  = $_POST["postKeywords"];;
  $my_post['post_status']   = 'publish';
  $my_post['post_author']   = 1;
  $my_post['post_category'] = array($_POST["postCategory"]);
  // Insert the post into the database
  wp_insert_post( $my_post );
}


?>

<form method="post">
  <br>
  <div class="mb-3">
    <label class="form-label"><?php echo $lang["chatGptText"]; ?></label>
    <textarea class="form-control" id="chatGptText" name="chatGptText" rows="3"></textarea>
  </div>
  <button type="submit" name="goTest" class="btn btn-secondary"><?php echo $lang["testButton"]; ?></button><br><br>

  <div class="mb-3">
    <label class="form-label"><?php echo $lang["blogTitle"]; ?></label>
    <input type="text" name="postTitle" id="postTitle" class="form-control"/>
  </div>
  <div class="mb-3">
    <label class="form-label"><?php echo $lang["blogContent"]; ?></label>
    <textarea style="height:250px;" class="form-control" name="postContent" id="postContent" rows="3"><?php echo $postContent; ?></textarea>
    <small><?php echo $lang["blogContentDesc"]; ?></small>
  </div>
  <div class="mb-3">
    <label class="form-label"><?php echo $lang["blogCategory"]; ?></label>
    <select name="postCategory" id="postCategory" class="form-select">
    <?php
      $categories = get_categories(array( 'hide_empty' => 0 ));
      foreach ($categories as $category) {
          echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
      }
    ?>
</select>
  </div>
  <div class="mb-3">
    <label class="form-label"><?php echo $lang["blogKeywords"]; ?></label>
    <textarea class="form-control" name="postKeywords" id="postKeywords" rows="3"></textarea>
  </div>

  <button type="submit" name="addBlog" class="btn btn-success"><?php echo $lang["addBlogButton"]; ?></button>
</form>
