<?php

use RedBeanPHP\R;

class SignupHandler extends Handler {

    public function get() {
        if ($this->is_logged_in()) {
            $this->response->redirect("/");
            return;
        }
        $this->write_file("signup.twig");
    }
    
    public function post() {
        do {
        if ($this->request->get("email") == NULL) break;
        if ($this->request->get("password") == NULL) break;
        if ($this->request->get("password2") == NULL) break;
        if ($this->request->get("password") != $this->request->get("password2")) break;
        $user = R::findOne("user", "email = ?", [$this->request->get("email")]);
        if ($user != NULL) break;
        $user = R::dispense("user");
        $hashed = password_hash($this->request->get("password"), PASSWORD_DEFAULT);
        $user->email = $this->request->get("email");
        $user->pwhash = $hashed;
        $user->cookie = base64_encode(random_bytes(48));
        R::store($user);
        $this->response->redirect("/login");
        
        return;
        } while (false);
        
        $this->render_write_file("signup.twig", array(
            "message" => "The username and/or password you entered was invalid."
        ));
    }

}
