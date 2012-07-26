<?php

class HomeHandler extends Handler {
	public function get() {
		$this->render_write_file("home.twig", array("name" => "World"));
	}
}