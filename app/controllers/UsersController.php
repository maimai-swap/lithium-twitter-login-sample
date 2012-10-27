<?php

namespace app\controllers;

use app\models\Users;
use lithium\action\DispatchException;
use lithium\storage\Session;
use TwitterOAuth;
use li3_twitteroauth\TwitterApps;


class UsersController extends \lithium\action\Controller {

    public function index() {
        $access_token = Session::read("access_token");
        $config = TwitterApps::config("default");

        if (
            isset($access_token["oauth_token"]) &&
            isset($access_token["oauth_token_secret"]) &&
            isset($access_token["user_id"]) &&
            isset($access_token["screen_name"])
        ) {
            $this->set($access_token);
            $ret = Users::first(array("conditions"=>array("user_id"=>$access_token["user_id"])));
            if ( !$ret ) {
                $question = Users::create();
                $question->save($access_token);
            }

            $connection = new TwitterOAuth($config["consumer_key"], $config["consumer_secret"], Session::read('oauth_token'), Session::read('oauth_token_secret'));
            $ret = $connection->get("/users/show",array("screen_name"=>$access_token["screen_name"]));
            $this->set(array("user"=>$ret));

        }

    }

    public function delete() {

    }
}
?>