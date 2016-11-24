<?php

class HomeHandler extends Handler {
	public function get() {
	    if (!$this->is_logged_in()) {
	        $this->response->redirect("/login");
	        return;
	    }
	    $this->write_file("hello.twig");
	}
}
