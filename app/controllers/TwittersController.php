<?php

namespace app\controllers;

use app\models\Twitters;
use lithium\action\DispatchException;
use li3_twitteroauth\TwitterApps;
use lithium\storage\Session;
use TwitterOAuth;
use app\models\Users;

class TwittersController extends \lithium\action\Controller {

    public function login() {
        $access_token = Session::read('access_token');
        if ( empty($access_token) || empty($access_token['oauth_token']) || empty($access_token['oauth_token_secret'])) {
            Session::clear();
        }
    }

    public function clearsessions() {

    }

    public function logout() {

    }

    public function connect() {

    }

    public function callback() {

        $config = TwitterApps::config("default");

        /* If the oauth_token is old redirect to the connect page. */
        if (isset($_REQUEST['oauth_token']) && Session::read('oauth_token') !== $_REQUEST['oauth_token']) {
            Session::write('oauth_status','oldtoken');
            $this->redirect("login");
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth($config["consumer_key"], $config["consumer_secret"], Session::read('oauth_token'), Session::read('oauth_token_secret'));

        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

        /* Save the access tokens. Normally these would be saved in a database for future use. */
        Session::write('access_token',$access_token);

        /* Remove no longer needed request tokens */
        Session::delete('oauth_token');
        Session::delete('oauth_token_secret');

        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
            /* The user has been verified and the access tokens can be saved for future use */
            Session::write('status','verified');

            $this->redirect("/");
        } else {
            $this->redirect("login");
        }

    }

    public function redirectauth() {

        $config = TwitterApps::config("default");

        /* Build TwitterOAuth object with client credentials. */
        $connection = new TwitterOAuth($config["consumer_key"], $config["consumer_secret"]);
        /* Get temporary credentials. */
        $request_token = $connection->getRequestToken($config["callback_url"]);

        /* Save temporary credentials to session. */
        $oauth_token = $request_token['oauth_token'];
        Session::write('oauth_token',$request_token['oauth_token']);
        Session::write('oauth_token_secret',$request_token['oauth_token_secret']);

        /* If last connection failed don't display authorization link. */
        switch ($connection->http_code) {
            case 200:
                /* Build authorize URL and redirect user to Twitter. */
                $url = $connection->getAuthorizeURL($oauth_token);
                $this->redirect($url);
                break;
            default:
                $this->set("message","Could not connect to Twitter. Refresh the page or try again later.");
        }

    }

}

?>