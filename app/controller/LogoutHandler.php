<?php

class LogoutHandler extends Handler {
	public function get() {
		$this->response->set_cookie("auth", "");
		$this->response->redirect("/login");
	}
}