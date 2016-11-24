<?php

use RedBeanPHP\R;

class LoginHandler extends Handler {

    public function get() {
        if ($this->is_logged_in()) {
            $this->response->redirect("/");
            return;
        }
        $this->write_file("login.twig");
    }
    
    public function post() {
        do {
        if ($this->request->get("email") == NULL) break;
        if ($this->request->get("password") == NULL) break;
        $user = R::findOne("user", "email = ?", [$this->request->get("email")]);
        if ($user == NULL) break; 
        if (!password_verify($this->request->get("password"), $user->pwhash)) {
        break;
        }
        $this->response->set_cookie("auth", $user->email . "|" . $user->cookie, time() + 60*60*24*30);
        $this->response->redirect("/");
        
        return;
        } while (false);
        
        $this->render_write_file("login.twig", array(
            "message" => "The username and/or password you entered was invalid."
        ));
    }

}
